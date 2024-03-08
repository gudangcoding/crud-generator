<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class RouteController extends Controller
{
    function index()
    {
        // Jalankan perintah route:list dan tangkap keluarannya
        // $output = Artisan::call('route:list');
        // Ambil hasil perintah sebagai string
        // $routeList = Artisan::output();

        // Proses output menjadi format yang sesuai untuk dimasukkan ke dalam tabel
        // $routes = $this->parseRouteList($routeList);

        // dd($routes);
        // echo json_encode($routes);
        // Masukkan data ke dalam tabel Route
        // Route::insert($routes);
        return view('route.index');
    }

    // Metode untuk memproses daftar rute
    private function parseRouteList($routeList)
    {
        // Implementasi logika pengolahan daftar rute di sini
        // Misalnya, Anda bisa melakukan pemisahan string, ekstraksi informasi, dll.

        // Contoh sederhana:
        $routes = explode("\n", $routeList);

        return $routes;
    }
}
