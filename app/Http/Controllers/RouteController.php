<?php

namespace App\Http\Controllers;

use App\Models\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

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

            $methods = explode('|', implode('|', $apiRoute->methods()));
            $method = strtolower($methods[0]);
            $controllers = explode('@', $apiRoute->getActionName());
            $controller = isset($controllers[0]) ? $controllers[0] . '::class' : '';
            $fungsi = isset($controllers[1]) ? $controllers[1] : '';

            $route = new Router();
            $route->url = $apiRoute->uri();
            $route->name = $apiRoute->getName();
            $route->controller = $controller;
            $route->method = $method;
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
            $methods = explode('|', implode('|', $webRoute->methods()));
            $method = strtolower($methods[0]);
            $controllers = explode('@', $webRoute->getActionName());
            $controller = isset($controllers[0]) ? $controllers[0] . '::class' : '';
            $fungsi = isset($controllers[1]) ? $controllers[1] : '';

            $route = new Router();
            $route->url = $webRoute->uri();
            $route->name = $webRoute->getName();
            $route->controller = $controller;
            $route->method = $method;
            $route->fungsi = $fungsi;
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

    public function bacaroute()
    {
        // Eksekusi perintah route:list dan tangkap outputnya
        // Artisan::call('route:list');
        $output = Artisan::call('route:list');
        // Tangkap outputnya
        $routeList = Artisan::output();

        // Filter output hanya untuk route web
        $filteredRoutes = $this->filterWebRoutes($routeList);

        // Tampilkan output yang telah difilter
        echo $filteredRoutes;
    }

    private function filterWebRoutes($routeList)
    {
        // Pisahkan output menjadi baris-baris
        $lines = explode(PHP_EOL, $routeList);

        // Inisialisasi array untuk menyimpan route web
        $webRoutes = [];

        // Loop melalui setiap baris
        foreach ($lines as $line) {
            // Cek apakah baris mengandung "web" (menandakan route web)
            if (strpos($line, 'web')) {
                // Tambahkan baris ke dalam array route web
                $webRoutes[] = $line;
            }
        }

        // Gabungkan baris-baris yang sudah difilter kembali menjadi satu string
        $filteredOutput = implode(PHP_EOL, $webRoutes);

        return $filteredOutput;
    }
}
