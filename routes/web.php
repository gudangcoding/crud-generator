
<?php


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TesController;



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
Route::resource('Tes/TesController', TesController::class);