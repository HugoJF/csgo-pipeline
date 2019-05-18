<?php

namespace App\Jobs;

use App\Contracts\FilterBase;
use App\CsgoEvents\PlayerDamageEvent;
use App\Filter;
use App\Line;
use App\Pipe;
use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ProcessEvents implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $events = [
		PlayerDamageEvent::class,
	];

	/** @var Collection */
	protected $filtersModel;

	/** @var Collection */
	protected $filters;

	/** @var Collection */
	protected $pipes;

	/** @var Collection */
	protected $lines;

	/** @var Command */
	protected $command;

	/** @var integer */
	private $eventPerJob;

	/**
	 * Pending pipe Redis list key name
	 *
	 * @var string
	 */
	private $pendingPipe = 'pending';

	/**
	 * Max amount of unbuilt events to hold
	 *
	 * @var int
	 */
	private $pendingPipeSize = 50000;

	/**
	 * Create a new job instance.
	 *
	 * @param Command $command
	 */
	public function __construct(Command $command = null)
	{
		$this->command = $command;
	}

	public function boot()
	{
		$this->eventPerJob = config('pipeline.events_per_job', 1000);
		$this->filtersModel = Filter::orderby('count', 'DESC')->get();

		$this->pipes = Pipe::all();

		$this->lines = Line::all();

		$this->filters = $this->filtersModel->map(function ($model) {
			return FilterBase::fromModel($model);
		});
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->boot();
		$start = microtime(true);

		for ($i = 0; $i < $this->eventPerJob; $i++) {

			$raw = Redis::command('lpop', ['entry']);

			$res = $this->handleEvent($raw);

			$this->info(json_encode($res));
		}

		$end = microtime(true);

		$duration = $end - $start;
		$this->info("Processing of {$this->eventPerJob} events took: {$duration} seconds");
	}

	/**
	 * @param mixed $filtersModel
	 *
	 * @return ProcessEvents
	 */
	public function setFiltersModel($filtersModel)
	{
		$this->filtersModel = $filtersModel;

		return $this;
	}

	/**
	 * Attempts to process a raw event
	 *
	 * @param $event - raw event to be processed
	 *
	 * @return bool
	 */
	protected function handleEvent($event)
	{
		// Check if event should be filtered
		if ($this->isEventFiltered($event))
			return false;

		// Attempt to process it
		foreach ($this->events as $csgoEvent) {
			$built = $csgoEvent::build($event);

			// If nothing was built, return false
			if (!is_object($built)) {
				$this->pushToPendingPipe($event);

				continue;
			}

			// Store the class that built the event
			$class = get_class($built);

			$this->info('Built command on ' . $class);

			// Get every line that receives this event
			$lines = $this->getLines($class);

			// Get the pipes that are connected
			$pipes = $this->getPipes($lines->pluck('pipe_id'));

			// Push data into them
			$this->pushDataToPipes(json_encode($built), $pipes);

			// Return built event
			return $built;
		}

		return false;
	}

	/**
	 * Push to unbuilt events pipe
	 *
	 * @param $string - data to push to pending pipe
	 */
	protected function pushToPendingPipe($string)
	{
		$llen = Redis::command('llen', [$this->pendingPipeSize]);

		// Push to pending pipe only if under limit
		if ($llen < $this->pendingPipeSize)
			Redis::command('rpush', [$this->pendingPipe, $string]);
	}

	/**
	 * @param $data  - data to be pushed
	 * @param $pipes - pipes to push
	 */
	protected function pushDataToPipes($data, $pipes)
	{
		$pipes->each(function ($pipe) use ($data) {
			$llen = Redis::command('llen', [$pipe->key]);

			// Only push if length is under limit or pop_on_limit is true
			if ($llen < $pipe->limit || $pipe->pop_on_limit)
				Redis::command('rpush', [$pipe->key, json_encode($data)]);

			// Pop oldest value if we are over limits
			if ($llen >= $pipe->limit && $pipe->pop_on_limit)
				Redis::command('lpop', [$pipe->key]);
		});
	}

	/**
	 * Return pipes connected by the lines
	 *
	 * @param $lines
	 *
	 * @return Collection
	 */
	protected function getPipes($lines)
	{
		return $this->pipes->filter(function ($pipe) use ($lines) {
			return $lines->contains($pipe->id);
		});
	}

	/**
	 * @param $class - full class path of the class that processed the event
	 *
	 * @return Collection
	 */
	protected function getLines($class)
	{
		return $this->lines->filter(function ($item) use ($class) {
			return $item->event_type === $class;
		});
	}

	/**
	 * @param $event - raw event data
	 *
	 * @return bool - if the event was filtered
	 */
	protected function isEventFiltered($event)
	{
		/** @var FilterBase $filter */
		foreach ($this->filters as $filter) {
			if ($filter->filters($event)) {
				$filter->model->increment('count');

				return true;
			}
		}

		return false;
	}

	/**
	 * Auto select between stdout and file logging
	 *
	 * @param $message
	 */
	protected function info($message)
	{
		if ($this->command) {
			$this->command->info($message);
		} else {
			Log::info($message);
		}
	}
}
