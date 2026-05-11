@extends('layouts.private')

@section('title', 'Configuracion')
@section('topbar_title', 'Configuracion')
@section('active_nav', 'configuracion')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Configuracion</h1>
            <p class="page-subtitle">Ajustes basicos del perfil y preferencias del usuario dentro de la zona privada.</p>
        </div>
    </div>

    <div class="stack">
        <section class="hero-grid">
            <article class="panel">
                <p class="label">Perfil</p>
                <div class="list">
                    <div class="list-item">
                        <span>Nombre</span>
                        <strong>{{ auth()->user()->name }}</strong>
                    </div>
                    <div class="list-item">
                        <span>Email</span>
                        <strong>{{ auth()->user()->email }}</strong>
                    </div>
                    <div class="list-item">
                        <span>Rol</span>
                        <strong>{{ strtoupper(auth()->user()->role) }}</strong>
                    </div>
                </div>
            </article>

            <article class="panel panel-highlight">
                <p class="label">Preferencias</p>
                <p class="muted">
                    Esta pantalla queda preparada para conectar cambios de perfil, seguridad y opciones de notificaciones.
                </p>
            </article>
        </section>
    </div>
@endsection
