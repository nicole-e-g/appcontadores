<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\AgremiadoController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\CarnetController;

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
Route::get('/consulta-habilitacion', [App\Http\Controllers\PublicAgremiadoController::class, 'index'])->name('public.habilidad.index');
Route::post('/consulta-habilitacion', [App\Http\Controllers\PublicAgremiadoController::class, 'buscar'])->name('public.habilidad.buscar');

Route::prefix('admin')->group(function () {
    // 1. Esta es la ruta GET que te da el error 404
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login.form');

    // 2. Esta es la ruta POST para cuando envías el formulario
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login');

    // 3. Esta es la ruta para el botón de "Cerrar Sesión"
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');

});

Route::middleware(['auth:admin', 'nocache'])->prefix('admin')->group(function () {
    // Esta es la ruta que mostrará tu 'index' después del login
    Route::get('/dashboard', [UsuarioController::class, 'index'])->name('admin.dashboard');

    Route::middleware(['superadmin'])->group(function () {//USUARIOS
        Route::get('/usuarios', [UsuarioController::class, 'listado'])->name('admin.usuarios.index');

        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('admin.usuarios.store');

        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('admin.usuarios.update');

        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('admin.usuarios.destroy');
    });

    //AGREMIADOS
    Route::get('/agremiados', [AgremiadoController::class, 'index'])->name('admin.agremiados.index');

    Route::post('/agremiados', [AgremiadoController::class, 'store'])->name('admin.agremiados.store');

    Route::get('/agremiados/{id}/edit', [AgremiadoController::class, 'getDatos'])->name('admin.agremiados.edit');

    Route::put('/agremiados/{agremiado}', [AgremiadoController::class, 'update'])->name('admin.agremiados.update');

    Route::delete('/agremiados/{agremiado}', [AgremiadoController::class, 'destroy'])->name('admin.agremiados.destroy');

    Route::get('/agremiados/{agremiado}', [AgremiadoController::class, 'show'])->name('admin.agremiados.show');

    //PAGOS
    Route::post('/pagos', [PagoController::class, 'store'])->name('admin.pagos.store');

    Route::put('/pagos/{pago}', [PagoController::class, 'update'])->name('admin.pagos.update');

    Route::post('/pagos/{pago}/anular', [PagoController::class, 'anular'])->name('admin.pagos.anular');

    Route::get('/pagos/{pago}/descargar', [PagoController::class, 'descargarPDF'])->name('admin.pagos.descargar');

    //CARNETS
    Route::get('carnets', [CarnetController::class, 'index'])->name('admin.carnets.index');

    Route::get('carnets/data', [CarnetController::class, 'getCarnetsData'])->name('admin.carnets.data');

    Route::put('carnets/{carnet}/entregar', [CarnetController::class, 'entregar'])->name('admin.carnets.entregar');
});
