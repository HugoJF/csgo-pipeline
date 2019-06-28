@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include('pipes.list')
        </div>
        <div class="col-md-12">
            @include('servers.list')
        </div>
    </div>
@endsection
