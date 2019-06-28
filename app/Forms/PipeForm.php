<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class PipeForm extends Form
{
	public function buildForm()
	{
		$this->key();
		$this->limit();
		$this->popOnLimit();
		$this->active();
		$this->description();
	}

	protected function key(): void
	{
		$this->add('key', 'text', [
			'label'      => 'Key',
			'rules'      => ['required'],
			'help_block' => $this->getHelpBlock('This should be an unique identifier of where events should be pushed'),
		]);
	}

	protected function limit(): void
	{
		$this->add('limit', 'number', [
			'label'      => 'Maximum events',
			'rules'      => ['required'],
			'help_block' => $this->getHelpBlock('How many events should Redis hold before dropping events'),
		]);
	}

	protected function popOnLimit(): void
	{
		$this->add('pop_on_limit', 'checkbox', [
			'label'      => 'Pop on Limit',
			'help_block' => $this->getHelpBlock('If the oldest event should be popped when the pipe is full.'),
		]);
	}

	protected function active(): void
	{
		$this->add('active', 'checkbox', [
			'label'      => 'Active',
			'default_value' => true,
			'help_block' => $this->getHelpBlock('If pipe is active when processing CS:GO events'),
		]);
	}

	protected function description(): void
	{
		$this->add('description', 'textarea', [
			'label'      => 'Description',
			'help_block' => $this->getHelpBlock('User friendly description'),
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
