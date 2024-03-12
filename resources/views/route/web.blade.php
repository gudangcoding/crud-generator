@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h1>Data Router WEB</h1>
        </div>
        <div class="card-body">
            <h1>List of Routes WEB</h1>
            <ul>
                @foreach ($routes->all() as $route)
                    <li>
                        <strong>URL:</strong> {{ $route->uri }}<br>
                        <strong>Name:</strong> {{ $route->getName() }}<br>
                        <strong>Controller:</strong> {{ $route->getActionName() }}<br>
                        <strong>Method:</strong> {{ implode('|', $route->methods) }}
                    </li>
                    <br>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
