@extends('layouts.admin')

@section('content')
<div style="padding: 20px;">
    <h2 style="font-family:'Playfair Display'; font-size:28px; margin-bottom: 20px;">👥 Gestión de Usuarios</h2>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-wrap" style="background: white; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #eee;">
                    <th style="padding: 15px; text-align: left;">ID</th>
                    <th style="padding: 15px; text-align: left;">Nombre</th>
                    <th style="padding: 15px; text-align: left;">Email</th>
                    <th style="padding: 15px; text-align: left;">Nivel VIP</th>
                    <th style="padding: 15px; text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $user)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px;">#{{ $user->id }}</td>
                    <td style="padding: 15px;"><strong>{{ $user->nombre }}</strong></td>
                    <td style="padding: 15px;">{{ $user->email }}</td>
                    <td style="padding: 15px;">
                        <span style="background: #fcf4e0; color: #b89b5e; padding: 4px 10px; border-radius: 12px; font-weight: bold;">
                            VIP {{ $user->nivelVIP ?? 1 }}
                        </span>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <a href="{{ route('usuarios.edit', $user->id) }}" style="text-decoration: none;">✏️ Editar</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 20px; text-align: center;">No hay usuarios.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $usuarios->links() }}
    </div>
</div>
@endsection