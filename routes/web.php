<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApuestaController;
use App\Http\Controllers\BilleteraController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\JuegoController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;

use App\Models\Apuesta;
use App\Models\Chat;
use App\Models\Juego;

Route::get('/', function () {
    return view('home', [
        'juegos' => Juego::all(),
        'ultimas_apuestas' => Apuesta::with(['user','juego'])->latest('fecha')->take(5)->get(),
        'chats' => Chat::with('user')->latest()->take(5)->get(),
    ]);
});

Route::controller(UserController::class)->group(function () {
    Route::get('users', 'listar');
    Route::get('users/{user}', 'ver');
    Route::post('users', 'crear');
    Route::put('users/{user}', 'actualizar');
    Route::delete('users/{user}', 'eliminar');
});

Route::controller(ApuestaController::class)->group(function () {
    Route::get('apuestas', 'listar');
    Route::get('apuestas/{apuesta}', 'ver');
    Route::post('apuestas', 'crear');
    Route::put('apuestas/{apuesta}', 'actualizar');
    Route::delete('apuestas/{apuesta}', 'eliminar');
});

Route::controller(BilleteraController::class)->group(function () {
    Route::get('billeteras', 'listar');
    Route::get('billeteras/{billetera}', 'ver');
    Route::post('billeteras', 'crear');
    Route::put('billeteras/{billetera}', 'actualizar');
    Route::delete('billeteras/{billetera}', 'eliminar');
});

Route::controller(ChatController::class)->group(function () {
    Route::get('chats', 'listar');
    Route::get('chats/{chat}', 'ver');
    Route::post('chats', 'crear');
    Route::put('chats/{chat}', 'actualizar');
    Route::delete('chats/{chat}', 'eliminar');
});

Route::controller(JuegoController::class)->group(function () {
    Route::get('juegos', 'listar');
    Route::get('juegos/{juego}', 'ver');
    Route::post('juegos', 'crear');
    Route::put('juegos/{juego}', 'actualizar');
    Route::delete('juegos/{juego}', 'eliminar');
});

Route::controller(MensajeController::class)->group(function () {
    Route::get('mensajes', 'listar');
    Route::get('mensajes/{mensaje}', 'ver');
    Route::post('mensajes', 'crear');
    Route::put('mensajes/{mensaje}', 'actualizar');
    Route::delete('mensajes/{mensaje}', 'eliminar');
});

Route::controller(NotificacionController::class)->group(function () {
    Route::get('notificaciones', 'listar');
    Route::get('notificaciones/{notificacion}', 'ver');
    Route::post('notificaciones', 'crear');
    Route::put('notificaciones/{notificacion}', 'actualizar');
    Route::delete('notificaciones/{notificacion}', 'eliminar');
});

Route::controller(RankingController::class)->group(function () {
    Route::get('rankings', 'listar');
    Route::get('rankings/{ranking}', 'ver');
    Route::post('rankings', 'crear');
    Route::put('rankings/{ranking}', 'actualizar');
    Route::delete('rankings/{ranking}', 'eliminar');
});

Route::controller(SettingController::class)->group(function () {
    Route::get('settings', 'listar');
    Route::get('settings/{setting}', 'ver');
    Route::post('settings', 'crear');
    Route::put('settings/{setting}', 'actualizar');
    Route::delete('settings/{setting}', 'eliminar');
});

