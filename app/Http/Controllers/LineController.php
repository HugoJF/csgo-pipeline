<?php

namespace App\Http\Controllers;

use App\Forms\LineForm;
use App\Line;
use App\Pipe;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

class LineController extends Controller
{
	public function create(FormBuilder $formBuilder, Pipe $pipe)
	{
		$form = $formBuilder->create(LineForm::class, [
			'method' => 'POST',
			'url'    => route('lines.store', $pipe),
		]);

		return view('form', [
			'title'       => 'New line',
			'submit_text' => 'Create line',
			'form'        => $form,
		]);
	}

	public function store(Request $request, Pipe $pipe)
	{
		$eventType = $request->input('event_type');

		if ($pipe->lines()->where('event_type', $eventType)->exists()) {
			$base = class_basename($eventType);
			flash()->error("Event type <strong>$base</strong> is already connected to pipe!");

			return back();
		}

		$line = Line::make();

		$line->event_type = $eventType;
		$line->pipe()->associate($pipe);

		$line->save();

		flash()->success('Line created successfully!');

		return redirect()->route('home');
	}

	public function destroy(Line $line)
	{
		$line->delete();

		flash()->success('Line was deleted!');

		return back();
	}
}
