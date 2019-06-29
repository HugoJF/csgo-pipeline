<?php

namespace App\Forms;

use App\Jobs\ProcessEvents;
use Kris\LaravelFormBuilder\Form;

class ServerForm extends Form
{
	public function buildForm()
	{
		$this->ip();
		$this->port();
		$this->priority();
	}

	public function ip()
	{
		$this->add('ip', 'text', [
			'label'      => 'IP',
			'rules'      => ['required'],
			'help_block' => $this->getHelpBlock('Server IP'),
		]);
	}

	public function port()
	{
		$this->add('port', 'number', [
			'label'      => 'Server port',
			'rules'      => ['required'],
			'help_block' => $this->getHelpBlock('Server port'),
		]);
	}

	public function priority()
	{
		$this->add('priority', 'number', [
			'label'      => 'Server priority',
			'rules'      => ['required'],
			'help_block' => $this->getHelpBlock('Server priority'),
		]);
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
