<div class="media text-muted pt-3">
    <div class="media-body small lh-125">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="d-block text-gray-dark">{{ $server->ip }}:{{ $server->port }}</h5>
            <div>
                <button class="btn btn-sm btn-link" href="#">Delete</button>
            </div>
        </div>
        <p>Events processed: {{ $server->events }}</p>

    </div>
</div>