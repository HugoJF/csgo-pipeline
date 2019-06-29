@extends('layouts.app')

@section('content')
    <h1>Pipe <strong>#{{ $key }}</strong> queue</h1>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <ul class="list-unstyled">
                @foreach ($data as $entry)
                    <li class="py-1 px-2 my-2 border border-grey rounded bg-light">
                        <code class="text-dark">{{ $entry }}</code>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
