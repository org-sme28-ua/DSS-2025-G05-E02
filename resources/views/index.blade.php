<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Entrega 2 - Gestión de Modelos</title>
</head>
<body class="p-5">
    <h1>Gestión del Modelo de Dominio</h1>
    
    <form action="{{ route('changeTable') }}" method="GET" class="mb-4">
        <label>Selecciona Clase del Modelo:</label>
        <select name="tabla" class="form-select w-25 d-inline" onchange="this.form.submit()">
            @foreach($tables as $t)
                <option value="{{ $t }}" {{ $tableName == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
    </form>

    <form action="{{ route('home') }}" method="GET" class="mb-3 d-flex">
        <input type="hidden" name="tabla" value="{{ $tableName }}">
        <input type="text" name="search" class="form-control w-25 me-2" placeholder="Buscar..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                @foreach($columns as $col)
                    <th>
                        <a href="{{ route('home', ['tabla'=>$tableName, 'sort'=>$col, 'order'=>request('order')=='asc'?'desc':'asc']) }}">
                            {{ strtoupper($col) }}
                        </a>
                    </th>
                @endforeach
                <th>ACCIONES</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                @foreach($columns as $col)
                    <td>{{ $item->$col }}</td>
                @endforeach
                <td>
                    <form action="{{ route('data.destroy', [$tableName, $item->id]) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm">Borrar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $items->appends(request()->query())->links() }}
</body>
</html>