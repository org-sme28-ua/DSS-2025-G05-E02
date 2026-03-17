<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bookie 2.0</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#7f2c33] text-white min-h-screen">
    <div class="grid grid-cols-12 gap-4 min-h-screen">
        <aside class="col-span-12 md:col-span-3 bg-[#6c252f] p-6 space-y-6 shadow-lg">
            <div class="flex items-center gap-2 mb-6">
                <div class="h-10 w-10 rounded bg-white text-[#7f2c33] flex items-center justify-center font-bold">🎰</div>
                <h1 class="text-xl font-bold">Bookie 2.0</h1>
            </div>

            <nav class="space-y-2">
                <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-[#803137] hover:bg-[#914041]">
                    <span>🏠</span><span>Lobby</span>
                </a>
                <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-[#803137] hover:bg-[#914041]">
                    <span>🔔</span><span>Notificaciones</span>
                </a>
                <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-[#803137] hover:bg-[#914041]">
                    <span>💰</span><span>Billetera</span>
                </a>
            </nav>

            <div class="border-t border-white/20 pt-4 space-y-2">
                <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-[#803137] hover:bg-[#914041]">👤 Profile</a>
                <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-[#803137] hover:bg-[#914041]">⚙️ Settings</a>
                <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-[#803137] hover:bg-[#914041]">🔓 Logout</a>
            </div>
        </aside>

        <main class="col-span-12 md:col-span-9 p-6 space-y-6">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="bg-[#803137] p-3 rounded-lg shadow-lg w-full md:w-auto">
                    <h2 class="text-2xl font-bold">Bienvenido, Usuario123</h2>
                    <p class="text-sm text-slate-300">Notificaciones: <strong>{{ App\Models\Notificacion::where('user_id', 1)->where('leido', false)->count() }}</strong></p>
                </div>
                <div class="relative w-full md:w-1/2">
                    <input type="text" placeholder="Buscar..." class="w-full rounded-full py-2 pl-4 pr-10 text-[#7f2c33] font-medium" />
                    <span class="absolute right-3 top-1/2 -translate-y-1/2">🔍</span>
                </div>
            </header>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($juegos as $juego)
                    <article class="bg-[#7b2d34] p-5 rounded-xl border border-white/20 hover:bg-[#8c323a] transition">
                        <div class="flex justify-between items-start">
                            <h3 class="text-xl font-extrabold">{{ strtoupper($juego->nombre) }}</h3>
                            <span class="text-xs px-2 py-1 rounded bg-white/20">{{ strtoupper($juego->categoria) }}</span>
                        </div>
                        <p class="mt-4">Estado: <strong>{{ ucfirst($juego->estado) }}</strong></p>
                    </article>
                @endforeach
                @if($juegos->isEmpty())
                    <div class="col-span-full bg-[#7b2d34] p-5 rounded-xl">No hay juegos disponibles.</div>
                @endif
            </div>

            <section class="bg-[#6c252f] p-4 rounded-xl border border-white/20">
                <h3 class="text-lg font-bold mb-3">Apuestas recientes</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($ultimas_apuestas as $apuesta)
                        <div class="bg-[#7f2c33] p-3 rounded-lg">
                            <p class="text-sm text-slate-200">{{ $apuesta->user->name ?? 'Usuario' }} ➜ {{ $apuesta->juego->nombre ?? 'Juego' }}</p>
                            <p class="text-lg font-semibold">${{ number_format($apuesta->monto, 2) }} (x{{ $apuesta->cuota }})</p>
                            <p class="text-xs uppercase">{{ $apuesta->estado }}</p>
                        </div>
                    @endforeach
                    @if($ultimas_apuestas->isEmpty())
                        <p>No hay apuestas aún.</p>
                    @endif
                </div>
            </section>

            <section class="bg-[#6c252f] p-4 rounded-xl border border-white/20">
                <h3 class="text-lg font-bold mb-3">Chats recientes</h3>
                <ul class="space-y-2">
                    @foreach($chats as $chat)
                        <li class="flex justify-between items-center bg-[#7f2c33] p-2 rounded-lg">
                            <span>{{ $chat->nombre }}</span>
                            <small class="text-xs {{ $chat->activo ? 'text-emerald-300' : 'text-rose-300' }}">{{ $chat->activo ? 'Activo' : 'Inactivo' }}</small>
                        </li>
                    @endforeach
                </ul>
            </section>
        </main>
    </div>
</body>
</html>