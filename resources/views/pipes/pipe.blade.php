<div class="media text-muted pt-3">
    <a href="#">
        @include('ui.switch')
    </a>
    <div class="media-body pb-3 mb-0 small lh-125">
        <div class="d-flex justify-content-between align-items-center w-100">
            <a href="{{ route('pipes.view', $pipe) }}"><strong class="d-block text-gray-dark">#{{ $pipe['key'] }}</strong></a>
            <div>
                
                <a class="btn btn-sm btn-link" href="{{ route('lines.create', $pipe) }}">Add line</a>
                <a class="mx-1 text-black-50">|</a>
                <a class="btn btn-sm btn-link" href="{{ route('pipes.edit', $pipe) }}">Edit</a>
                <a class="mx-1 text-black-50">|</a>
                {!! Form::open(['route' => ['pipes.destroy', $pipe], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
                <button class="btn btn-sm btn-link" href="#">Delete</button>
                {!! Form::close() !!}
            </div>
        </div>
        <p>Description: {{ $pipe->description }}</p>
        <p>Entries: {{ $pipe->pendingEvents }}</p>
        <h6>
            @foreach ($pipe->lines as $line)
                {!! Form::open(['route' => ['lines.destroy', $line], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
                <button href="#" class="py-1 px-2 btn btn-link badge badge-secondary">{{ class_basename($line->event_type) }} ✕</button>
                {!! Form::close() !!}
            @endforeach
        </h6>
    </div>
</div>