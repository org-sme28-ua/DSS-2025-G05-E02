@extends('layouts.admin')

@section('content')
    <h2 style="font-family:'Playfair Display'; font-size:26px; margin-bottom:20px;">Gestión de Usuarios</h2>
    
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Nivel VIP</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $user)
                <tr>
                    <td>#{{ $user->id }}</td>
                    <td><strong>{{ $user->nombre }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge-gold">VIP {{ $user->nivelVIP }}</span>
                    </td>
                    <td>
                        <a href="{{ route('usuarios.edit', $user->id) }}" class="btn">✏️</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="margin-top:20px;">
        {{ $usuarios->links() }}
    </div>
@endsection
