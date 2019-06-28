<?php

namespace App\Http\Controllers;

use App\Pipe;
use App\Server;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
    	$pipes = Pipe::all();
    	$servers = Server::all();

        return view('home', compact('pipes', 'servers'));
    }
}
