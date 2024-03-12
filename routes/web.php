<?php

use App\Http\Controllers\CrudTransaksiController;
use App\Models\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/crud', [App\Http\Controllers\CrudController::class, 'index'])->name('crud');
Route::post('/crud/generate', [App\Http\Controllers\CrudController::class, 'generate'])->name('crud.generate');
Route::post('/crud/getkolom', [App\Http\Controllers\CrudController::class, 'tampiKolom'])->name('crud.getkolom');
Route::get('/router', [App\Http\Controllers\RouteController::class, 'index'])->name('router');
Route::get('/router/web', [App\Http\Controllers\RouteController::class, 'web_route'])->name('router.web');
Route::get('/router/saveweb', [App\Http\Controllers\RouteController::class, 'saveWebRoutes'])->name('router.saveweb');
Route::get('/router/saveapi', [App\Http\Controllers\RouteController::class, 'saveApiRoutes'])->name('router.saveapi');

Route::resource('/crudx', CrudTransaksiController::class);


// Ambil informasi rute dari database
$routes = Router::all();
// Loop melalui setiap rute
foreach ($routes as $route) {
    // Tambahkan rute ke dalam aplikasi
    Route::match([$route->method], $route->url, [
        'uses' => $route->controller,
        'as' => $route->name
    ]);
}

$routes = Router::all();
foreach ($routes as $route) {
    Route::get($route->url, function () use ($route) {
        return view('welcome'); // Ganti dengan view yang sesuai
    })->name($route->name);
}
