<?php

namespace App\Http\Controllers;

use App\Models\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

class RouteController extends Controller
{
    public function index()
    {
        $rute = collect(Route::getRoutes())->filter(function ($route) {
            return strpos($route->uri(), 'api') !== false;
        });

        return view('route.index', compact('rute'));
    }



    public function saveApiRoutes()
    {
        // Mendapatkan daftar route API
        $apiRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return strpos($route->uri(), 'api') !== false;
        });

        // Menyimpan daftar route API ke dalam database
        foreach ($apiRoutes as $apiRoute) {
            $route = new Router();
            $route->url = $apiRoute->uri();
            $route->name = $apiRoute->getName();
            $route->controller = $apiRoute->getActionName();
            $route->method = implode('|', $apiRoute->methods());
            $route->route = 'api';
            $route->save();
        }

        return 'Daftar route API berhasil disimpan ke dalam database.';
    }

    public function saveWebRoutes()
    {
        $webRoutes = collect(Route::getRoutes())->filter(function ($route) {
            // Filter rute-rute yang ingin dikecualikan
            $excludedRoutes = [
                'sanctum.csrf-cookie',
                'ignition.healthCheck',
                'ignition.executeSolution',
                'ignition.updateConfig',
            ];
            return !in_array($route->getName(), $excludedRoutes) && strpos($route->uri(), 'api') === false;
        });
        // Menyimpan daftar route web ke dalam database
        foreach ($webRoutes as $webRoute) {
            $route = new Router();
            $route->url = $webRoute->uri();
            $route->name = $webRoute->getName();
            $route->controller = $webRoute->getActionName();
            $route->method = implode('|', $webRoute->methods());
            $route->route = 'web';
            $route->save();
        }

        return 'Daftar route web berhasil disimpan ke dalam database.';
    }
    public function web_route()
    {
        // Mendapatkan daftar route web
        $routes = collect(Route::getRoutes())->filter(function ($route) {
            // Filter rute-rute yang ingin dikecualikan
            $excludedRoutes = [
                'sanctum.csrf-cookie',
                'ignition.healthCheck',
                'ignition.executeSolution',
                'ignition.updateConfig',
            ];

            // Kembalikan true jika rute tidak ada dalam daftar yang ingin dikecualikan
            return !in_array($route->getName(), $excludedRoutes) && strpos($route->uri(), 'api') === false;
        });
        return view('route.web', compact('routes'));
    }
}
