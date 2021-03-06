<?php

namespace App\Http\Controllers;

use App\Forms\PipeForm;
use App\Pipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Kris\LaravelFormBuilder\FormBuilder;

class PipeController extends Controller
{
	public function view(Pipe $pipe)
	{
		return $this->viewByKey($pipe->key);
	}

	public function viewByKey($key)
	{
		$data = Redis::command('lrange', [$key, 0, -1]);

		return view('pipes.view', compact('key', 'data'));
	}

	public function create(FormBuilder $formBuilder)
	{
		$form = $formBuilder->create(PipeForm::class, [
			'method' => 'POST',
			'url'    => route('pipes.store'),
		]);

		return view('form', [
			'title'       => 'New pipe',
			'submit_text' => 'Create pipe',
			'form'        => $form,
		]);
	}

	public function store(Request $request)
	{
		$pipe = Pipe::make();

		$pipe->fill($request->only(['active', 'key', 'limit', 'pop_on_limit', 'description']));

		$pipe->save();

		flash()->success("Pipe <strong>#{$pipe->key}</strong> created successfully!");

		return redirect()->route('home');
	}

	public function edit(FormBuilder $formBuilder, Pipe $pipe)
	{
		$form = $formBuilder->create(PipeForm::class, [
			'method' => 'PATCH',
			'model'  => $pipe,
			'url'    => route('pipes.update', $pipe),
		]);

		return view('form', [
			'title'       => "Updating pipe {$pipe->key}",
			'submit_text' => 'Update pipe',
			'form'        => $form,
		]);
	}

	public function update(Request $request, Pipe $pipe)
	{
		$pipe->fill($request->input() + ['active' => 0, 'pop_on_limit' => 0]);

		$pipe->save();

		flash()->success("Pipe <strong>#{$pipe->key}</strong> was updated!");

		return redirect()->route('home');
	}

	public function destroy(Pipe $pipe)
	{
		$pipe->delete();

		Redis::command('ltrim', [$pipe->key, -1, 0]);

		flash()->success("Pipe <strong>#{$pipe->key}</strong> was deleted!");

		return redirect()->route('home');
	}
}
