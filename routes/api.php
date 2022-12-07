<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/images', [ImageController::class, 'index']);
Route::post('/images/store', [ImageController::class, 'store']);
Route::get('/images/{id}', [ImageController::class, 'show']);

Route::get('local/temp/{path}', function (string $path) {
    return Storage::disk('public')->download($path);
})->name('local.temp');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
