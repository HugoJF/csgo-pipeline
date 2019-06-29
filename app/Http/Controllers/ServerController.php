<?php

namespace App\Http\Controllers;

use App\Forms\ServerForm;
use App\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Kris\LaravelFormBuilder\FormBuilder;

class ServerController extends Controller
{
	public function create(FormBuilder $formBuilder)
	{
		$form = $formBuilder->create(ServerForm::class, [
			'method' => 'POST',
			'url'    => route('servers.store'),
		]);

		return view('form', [
			'title'       => 'New server',
			'submit_text' => 'Create server',
			'form'        => $form,
		]);
	}

	public function view(Server $server)
	{
		$key = "$server->ip:$server->port";
		$data = Redis::command('lrange', [$key, 0, -1]);

		return view('servers.view', compact('server', 'data'));
	}

	public function edit(FormBuilder $formBuilder, Server $server)
	{
		$form = $formBuilder->create(ServerForm::class, [
			'method' => 'PATCH',
			'model'  => $server,
			'url'    => route('servers.update', $server),
		]);

		return view('form', [
			'title'       => "Server $server->ip:$server->port details",
			'submit_text' => 'Update server',
			'form'        => $form,
		]);
	}

	public function update(Request $request, Server $server)
	{
		$server->fill($request->only(['ip', 'port', 'priority']));

		$server->save();

		flash()->success("Server <strong>$server->ip:$server->port</strong> was updated!");

		return redirect()->route('home');
	}

	public function store(Request $request)
	{
		$server = Server::make();

		$server->fill($request->only(['ip', 'port', 'priority']));

		$server->save();

		flash()->success('Server created!');

		return redirect()->route('home');
	}

	public function destroy(Server $server)
	{
		$server->delete();

		flash()->success("Server <strong>$server->ip:$server->port</strong> was deleted!");

		return redirect()->route('home');
	}
}
