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
                    @php
                        $methods = explode('|', implode('|', $route->methods()));
                        $method = strtolower($methods[0]);
                        $controllers = explode('@', $route->getActionName());
                        $controller = isset($controllers[0]) ? $controllers[0] . '::class' : '';
                        $fungsi = isset($controllers[1]) ? $controllers[1] : '';
                    @endphp
                    <li>
                        <strong>URL:</strong> {{ $route->uri }}<br>
                        <strong>Name:</strong> {{ $route->getName() }}<br>
                        <strong>Controller:</strong> {{ $controller }}<br>
                        <strong>Fungsi:</strong> {{ $fungsi }}<br>
                        <strong>Method:</strong> {{ $method }}
                    </li>
                    <br>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
