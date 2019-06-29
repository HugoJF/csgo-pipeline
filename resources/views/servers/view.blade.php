@extends('layouts.app')

@section('content')
    <h1>Server <strong>{{ $server->ip }}:{{ $server->port }}</strong> queue</h1>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <ul class="list-unstyled">
                @forelse ($data as $entry)
                    <li class="py-1 px-2 my-2 border border-grey rounded bg-light">
                        <code class="text-dark">{{ $entry }}</code>
                    </li>
                @empty
                    <h4>Empty queue</h4>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
