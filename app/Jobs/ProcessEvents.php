<?php

namespace App\Jobs;

use App\Contracts\FilterBase;
use App\CsgoEvents\ConsoleSay;
use App\CsgoEvents\CvarChanged;
use App\CsgoEvents\FailureCode;
use App\CsgoEvents\GameOver;
use App\CsgoEvents\IdBan;
use App\CsgoEvents\IdPermanentBan;
use App\CsgoEvents\IpBan;
use App\CsgoEvents\LoadingMap;
use App\CsgoEvents\LogFileClosed;
use App\CsgoEvents\LogFileOpened;
use App\CsgoEvents\MolotovSpawned;
use App\CsgoEvents\PlayerAssisted;
use App\CsgoEvents\PlayerConnected;
use App\CsgoEvents\PlayerDamage;
use App\CsgoEvents\PlayerDisconnected;
use App\CsgoEvents\PlayerEnteredGame;
use App\CsgoEvents\PlayerFlash;
use App\CsgoEvents\PlayerFlashAssisted;
use App\CsgoEvents\PlayerKilledByBomb;
use App\CsgoEvents\PlayerKilled;
use App\CsgoEvents\PlayerKilledProp;
use App\CsgoEvents\PlayerLeftBuyZone;
use App\CsgoEvents\PlayerNameChange;
use App\CsgoEvents\PlayerPurchase;
use App\CsgoEvents\PlayerSay;
use App\CsgoEvents\PlayerTeamSay;
use App\CsgoEvents\PlayerThrewGrenade;
use App\CsgoEvents\PlayerTriggerEvent;
use App\CsgoEvents\PlayerValidated;
use App\CsgoEvents\RconBadPassword;
use App\CsgoEvents\RconEvent;
use App\CsgoEvents\ServerCvarsEnd;
use App\CsgoEvents\ServerCvarsStart;
use App\CsgoEvents\ServerIsOutOfDate;
use App\CsgoEvents\ServerMessage;
use App\CsgoEvents\SourcemodPluginsLoaded;
use App\CsgoEvents\StartedMap;
use App\CsgoEvents\StartingFreezePeriod;
use App\CsgoEvents\SuicideEvent;
use App\CsgoEvents\SwitchTeam;
use App\CsgoEvents\TeamName;
use App\CsgoEvents\TeamScored;
use App\CsgoEvents\TeamTriggerEvent;
use App\CsgoEvents\VarChanged;
use App\CsgoEvents\WorldTriggerEvent;
use App\Filter;
use App\Line;
use App\Pipe;
use App\Server;
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

	public static $events = [
		PlayerDamage::class,
		PlayerDisconnected::class,
		PlayerSay::class,
		PlayerTriggerEvent::class,
		RconEvent::class,
		StartingFreezePeriod::class,
		SuicideEvent::class,
		SwitchTeam::class,
		TeamScored::class,
		TeamTriggerEvent::class,
		WorldTriggerEvent::class,
		PlayerValidated::class,
		PlayerThrewGrenade::class,
		PlayerKilledByBomb::class,
		PlayerKilled::class,
		MolotovSpawned::class,
		PlayerFlash::class,
		PlayerKilledProp::class,
		PlayerLeftBuyZone::class,
		PlayerTeamSay::class,
		PlayerEnteredGame::class,
		PlayerConnected::class,
		CvarChanged::class,
		GameOver::class,
		IpBan::class,
		LoadingMap::class,
		PlayerAssisted::class,
		ServerCvarsStart::class,
		SourcemodPluginsLoaded::class,
		StartedMap::class,
		VarChanged::class,
		IdBan::class,
		LogFileClosed::class,
		LogFileOpened::class,
		PlayerFlashAssisted::class,
		PlayerNameChange::class,
		ServerCvarsEnd::class,
		PlayerPurchase::class,
		ConsoleSay::class,
		FailureCode::class,
		IdPermanentBan::class,
		RconBadPassword::class,
		ServerIsOutOfDate::class,
		TeamName::class,
		ServerMessage::class,
	];

	/** @var Collection */
	protected $filtersModel;

	/** @var Collection */
	protected $filters;

	/** @var Collection */
	protected $pipes;

	/** @var Collection */
	protected $lines;

	/** @var Collection */
	private $servers;

	/** @var Command */
	protected $command;

	/** @var integer */
	private $eventsPerJob;

	/** @var bool */
	private $verbose;

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
	 * @param bool    $verbose
	 */
	public function __construct(Command $command = null, $verbose = false)
	{
		$this->command = $command;
		$this->verbose = $verbose;
	}

	public function boot()
	{
		$this->eventsPerJob = config('pipeline.events_per_job', 1000);
		$this->filtersModel = Filter::orderby('count', 'DESC')->get();

		$this->pipes = Pipe::all();

		$this->lines = Line::all();

		// TODO: check API for sync and DELETE THE REDIS KEY
		$this->servers = Server::all();

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
		// Queries database for information
		$this->boot();

		// Store job start time
		$start = microtime(true);

		// Computes redistributed reserves per server
		$adjustedReserves = $this->calculateEventsPerServer();

		//		$adjustedReserves->transform(function ($item) {
		//			unset($item['server']);
		//
		//			return $item;
		//		});
		//
		//		dd($adjustedReserves);

		foreach ($adjustedReserves as $data) {
			// Start start time
			$s = microtime(true);

			// Extract variables from data
			$server = $data['server'];
			$reserve = round($data['adjustedReserve']);

			// Start processing $reserve events from $server
			$this->processServer($server, $reserve);

			// Store ending time
			$e = microtime(true);

			// Debug
			$d = round($e - $s, 3);
			$this->info("Processed {$reserve} in {$d} seconds from server {$server->ip}:{$server->port}.");
		}

		$remainder = $adjustedReserves->reduce(function ($acc, $reserve) {
			return $acc + $reserve['adjustedReserve'];
		}, 0);

		$remainder = round($this->eventsPerJob - $remainder);

		$this->info("Processing $remainder events from pending pipe");
		$this->processKey($this->pendingPipe, $remainder);

		// Store job end time
		$end = microtime(true);

		// Debug
		$duration = round($end - $start, 3);
		$this->info("Processing of {$this->eventsPerJob} events took: {$duration} seconds");
	}

	protected function totalPriority(Collection $servers)
	{
		return $servers->reduce(function ($acc, $server) {
			return $acc + $server->priority;
		}, 0);
	}

	protected function eventsPerPriority(Collection $servers)
	{
		$totalPriority = $this->totalPriority($servers);

		if ($totalPriority === 0)
			return 0;

		return $this->eventsPerJob / $totalPriority;
	}

	protected function serverKey(Server $server)
	{
		return "{$server->ip}:{$server->port}";
	}

	protected function pipeLength(Server $server)
	{
		$serverKey = $this->serverKey($server);

		return Redis::command('llen', [$serverKey]);
	}

	protected function calculateEventsPerServer()
	{
		// Calculate how many events should be processed by priority unit
		$eventsPerPriority = $this->eventsPerPriority($this->servers);

		// Pre-process server data
		$eventsPerServer = $this->servers->mapWithKeys(function ($server) use ($eventsPerPriority) {
			// Number of reserved events
			$reservedEvents = $eventsPerPriority * $server->priority;

			// Number of pending events
			$pipeLength = $this->pipeLength($server);

			// Calculate priority based on $eventPerPriority
			$realPriority = $pipeLength / $eventsPerPriority;

			// Render server key
			$serverKey = $this->serverKey($server);

			return [
				$serverKey => [
					'server'       => $server,
					'reserve'      => $reservedEvents,
					'realPriority' => min($server->priority, $realPriority),
					'pipeLength'   => $pipeLength,
					'overReserve'  => ($pipeLength > $reservedEvents),
				],
			];
		});

		// Calculate the total un-adjusted priority
		$totalPriority = $this->totalPriority($this->servers);
		$this->info("Total priority for all servers is: $totalPriority");

		// Filter over-reserve servers
		$overReserveServers = $eventsPerServer->filter(function ($server) {
			return $server['overReserve'];
		});

		// Calculate the over-reserve priority
		$totalOverReservePriority = $overReserveServers->reduce(function ($acc, $data) {
			return $acc + $data['server']->priority;
		}, 0);

		// Calculate the total priority of servers over reserve
		$totalRealPriority = $eventsPerServer->reduce(function ($acc, $data) {
			return $acc + $data['realPriority'];
		}, 0);
		$this->info("Over reserve priority: $totalRealPriority");

		// Calculate the available priority for redistribution
		$availablePriority = $totalPriority - $totalRealPriority;
		$this->info("Available priority: $availablePriority");

		// Fix possible division by 0
		if ($totalOverReservePriority === 0)
			$totalOverReservePriority = 1;

		// Calculate how much priority each over reserve server will receive (based on it's base priority)
		$extraPriorityPerPriority = $availablePriority / $totalOverReservePriority;
		$this->info("Extra priority per server: $extraPriorityPerPriority");

		// Calculate the amount of events that each server should receive per extra priority
		$extraEventsPerPriority = $eventsPerPriority * $extraPriorityPerPriority;
		$this->info("Extra events per priority: $extraEventsPerPriority");

		// Update original data array
		$eventsPerServer->transform(function ($data) use ($extraEventsPerPriority) {
			// If the server is over reserve, increase by what's available
			if ($data['overReserve'])
				$data['adjustedReserve'] = $data['reserve'] + $extraEventsPerPriority * $data['server']->priority;
			// If the server is under reserve, set reserve to the exact amount it needs now
			else
				$data['adjustedReserve'] = $data['pipeLength'];

			return collect($data);
		});

		return $eventsPerServer;
	}

	protected function processServer(Server $server, $eventCount)
	{
		$key = $this->serverKey($server);

		$server->increment('events', $eventCount);

		$this->processKey($key, $eventCount);
	}

	protected function processKey($key, $eventCount)
	{
		$llen = Redis::command('llen', [$key]);
		$eventCount = min($llen, $eventCount);

		for ($i = 0; $i < $eventCount; $i++) {
			$raw = Redis::command('lpop', [$key]);

			$res = $this->handleEvent($raw);

			if ($this->verbose)
				$this->info(json_encode($res));
		}
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

		$built = false;

		// Attempt to process it
		foreach (self::$events as $csgoEvent) {
			// Avoid empty events
			if (empty($event))
				return false;

			// Attempt to build event
			$built = $csgoEvent::build($event);

			// If nothing was built, return false
			if (!is_object($built)) {
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

		$this->info('Event could not be processed: ~' . $event . '~');
		$this->info('Resulting: ' . json_encode($built));
		$this->pushToPendingPipe($event);

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
