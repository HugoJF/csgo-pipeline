<div class="media text-muted pt-3">
    <div class="media-body small lh-125">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="d-block text-gray-dark"><a href="{{ route('servers.view', $server) }}">{{ $server->ip }}:{{ $server->port }}</a></h5>
            <div>
                <a class="btn btn-sm btn-link" href="{{ route('servers.edit', $server) }}">Edit</a>
                <a class="mx-1 text-black-50">|</a>
                {!! Form::open(['route' => ['servers.destroy', $server], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
                <button class="btn btn-sm btn-link" href="#">Delete</button>
                {!! Form::close() !!}
            </div>
        </div>
        
        <p>Events processed: {{ $server->events }}</p>
        <p>Priority: <strong>{{ $server->priority }}</strong></p>
    </div>
</div>