<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="border-bottom border-gray pb-2 mb-2">Servers</h6>
    @foreach ($servers as $server)
        @include('servers.server', $server)
    @endforeach
    <small class="border-top border-gray pt-2 d-block text-right mt-3">
        <a href="#">Show all</a>
    </small>
</div>