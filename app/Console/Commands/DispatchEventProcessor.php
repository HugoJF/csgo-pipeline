<?php

namespace App\Console\Commands;

use App\Jobs\ProcessEvents;
use Illuminate\Console\Command;

class DispatchEventProcessor extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'events:process';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		dispatch_now(new ProcessEvents($this));
	}
}
