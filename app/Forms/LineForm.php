<?php

namespace App\Forms;

use App\Jobs\ProcessEvents;
use Kris\LaravelFormBuilder\Form;

class LineForm extends Form
{
	public function buildForm()
	{
		$this->line();
	}

	public function line()
	{
		$this->add('event_type', 'select', [
			'label'       => 'Event Type',
			'choices'     => $this->getLines(),
			'empty_value' => '=== Select line to attach ===',
			'help_block'  => $this->getHelpBlock('Select what event should be connected to event pipe'),
		]);
	}

	private function getLines()
	{
		$lines = collect(ProcessEvents::$events);

		return $lines->mapWithKeys(function ($line, $key) {
			return [$line => class_basename($line)];
		})->sort()->toArray();
	}

	private function getHelpBlock($text)
	{
		return [
			'tag'            => 'small',
			'helpBlockAttrs' => [
				'class' => 'form-text text-muted',
			],
			'text'           => $text ?? '',
		];
	}
}
