



<?php

 use App\Http\Controllers\API\MemberControllerAPI;
use Illuminate\Http\Request;
 use App\Http\Controllers\API\BarangControllerAPI;
use Illuminate\Support\Facades\Route;
 use App\Http\Controllers\API\ProdukControllerAPI;

 use App\Http\Controllers\API\TesControllerAPI;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::resource('api/Tes/TesController', TesControllerAPI::class);Route::resource('api/Produk/ProdukController', ProdukControllerAPI::class);Route::resource('api/Barang/BarangController', BarangControllerAPI::class);Route::resource('api/Member/MemberController', MemberControllerAPI::class);