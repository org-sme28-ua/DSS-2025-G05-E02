<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Mensaje;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        return $this->renderChatPage($request);
    }

    public function start(Request $request)
    {
        $validated = $request->validate([
            'recipient' => ['required', 'string', 'max:255'],
        ], [
            'recipient.required' => 'Introduce el email o nombre del usuario con el que quieres hablar.',
        ]);

        $currentUser = $request->user();
        $recipientSearch = trim($validated['recipient']);

        $recipient = User::query()
            ->where('id', '!=', $currentUser->id)
            ->where(function ($query) use ($recipientSearch) {
                $query->where('email', $recipientSearch)
                    ->orWhere('name', 'like', '%' . $recipientSearch . '%');
            })
            ->orderByRaw('email = ? desc', [$recipientSearch])
            ->orderBy('name')
            ->first();

        if (! $recipient) {
            return back()
                ->withErrors(['recipient' => 'No se ha encontrado ningún usuario con ese email o nombre.'])
                ->withInput();
        }

        $chat = Chat::primerChatEntre($currentUser->id, $recipient->id)
            ?: Chat::crearChatEntre($currentUser->id, $recipient->id);

        return redirect()
            ->route('private.chat.show', $chat)
            ->with('success', 'Chat abierto con ' . $recipient->name . '.');
    }

    public function showConversation(Request $request, Chat $chat)
    {
        abort_unless($chat->hasParticipant($request->user()->id), 403);

        Mensaje::query()
            ->where('chat_id', $chat->id)
            ->where('receptor_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->renderChatPage($request, $chat);
    }

    public function sendMessage(Request $request, Chat $chat)
    {
        $currentUser = $request->user();
        abort_unless($chat->hasParticipant($currentUser->id), 403);

        $validated = $request->validate([
            'contenido' => ['required', 'string', 'max:1500'],
        ], [
            'contenido.required' => 'Escribe un mensaje antes de enviarlo.',
            'contenido.max' => 'El mensaje es demasiado largo.',
        ]);

        $chat->loadMissing(['userOne', 'userTwo']);
        $recipient = $chat->otherParticipant($currentUser);

        if (! $recipient) {
            return back()->withErrors(['contenido' => 'No se pudo encontrar el receptor del chat.']);
        }

        Mensaje::create([
            'chat_id' => $chat->id,
            'emisor_id' => $currentUser->id,
            'receptor_id' => $recipient->id,
            'contenido' => $validated['contenido'],
            'editado' => false,
        ]);

        $chat->update([
            'activo' => true,
            'last_message_at' => now(),
        ]);

        Notificacion::crearNotificacion(
            $recipient->id,
            'Nuevo mensaje de ' . $currentUser->name,
            $currentUser->name . ' te ha enviado un mensaje privado.',
            'mensaje'
        );

        return redirect()
            ->route('private.chat.show', $chat)
            ->with('success', 'Mensaje enviado.');
    }


    
    private function renderChatPage(Request $request, ?Chat $activeChat = null)
    {
        $user = $request->user();

        // Cargar los chats en los que participa el usuario
        $chats = Chat::query()
            ->forUser($user->id)
            ->with(['userOne', 'userTwo', 'ultimoMensaje.emisor'])
            ->withCount('mensajes')
            ->orderByRaw('COALESCE(last_message_at, updated_at) DESC')
            ->get();

        // Contar mensajes no leídos por cada chat
        $unreadByChat = Mensaje::query()
            ->whereIn('chat_id', $chats->pluck('id'))
            ->where('receptor_id', $user->id)
            ->whereNull('read_at')
            ->selectRaw('chat_id, COUNT(*) as total')
            ->groupBy('chat_id')
            ->pluck('total', 'chat_id');

        // Asignar el "otro usuario" y el contador de no leídos a cada objeto chat
        $chats->each(function (Chat $chat) use ($user, $unreadByChat) {
            $chat->setAttribute('other_user', $chat->otherParticipant($user));
            $chat->setAttribute('unread_count', (int) ($unreadByChat[$chat->id] ?? 0));
        });

        if ($activeChat) {
            $activeChat->loadMissing(['userOne', 'userTwo']);
            $activeChat->setAttribute('other_user', $activeChat->otherParticipant($user));
        }

        // Obtener los mensajes del chat activo
        $messages = $activeChat
            ? $activeChat->mensajes()->with(['emisor', 'receptor'])->orderBy('created_at')->get()
            : collect();

        // CREAMOS LA VARIABLE $amigos (sin el 'role' para evitar fallos de SQL)
        $amigos = $user->amigos()->orderBy('name')->get(['users.id', 'name', 'email']);

        return view('chat', [
            'chats' => $chats,
            'activeChat' => $activeChat,
            'messages' => $messages,
            'amigos' => $amigos,
        ]);
    }

    
    // ============================================================
    // API del panel de administración
    // ============================================================
    public function getData(Request $request)
    {
        $query = Chat::query()->with(['user', 'userOne', 'userTwo'])->withCount('mensajes');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('nombre', 'like', '%' . $search . '%')
                    ->orWhereHas('userOne', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('userTwo', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->has('activo') && $request->activo !== '') {
            $query->where('activo', $request->activo === 'true');
        }

        $sort = in_array($request->get('sort'), ['id', 'nombre', 'activo', 'last_message_at', 'created_at'], true)
            ? $request->get('sort')
            : 'id';

        $dir = $request->get('dir') === 'desc' ? 'desc' : 'asc';
        $perPage = (int) $request->get('per', 6);

        return response()->json($query->orderBy($sort, $dir)->paginate($perPage));
    }

    public function show($id)
    {
        return response()->json(Chat::with(['user', 'userOne', 'userTwo', 'mensajes'])->findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['nullable', 'string', 'max:255'],
            'activo' => ['nullable', 'boolean'],
            'user_id' => ['required', 'exists:users,id'],
            'user_one_id' => ['nullable', 'exists:users,id'],
            'user_two_id' => ['nullable', 'exists:users,id', 'different:user_one_id'],
        ]);

        $data['user_one_id'] = $data['user_one_id'] ?? $data['user_id'];
        $data['activo'] = $data['activo'] ?? true;
        $data['nombre'] = $data['nombre'] ?? 'Chat privado';

        $chat = Chat::create($data);

        return response()->json([
            'success' => true,
            'data' => $chat,
            'message' => 'Chat creado correctamente',
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $chat = Chat::findOrFail($id);

        $data = $request->validate([
            'nombre' => ['sometimes', 'string', 'max:255'],
            'activo' => ['sometimes', 'boolean'],
            'user_one_id' => ['sometimes', 'nullable', 'exists:users,id'],
            'user_two_id' => ['sometimes', 'nullable', 'exists:users,id', 'different:user_one_id'],
            'last_message_at' => ['sometimes', 'nullable', 'date'],
        ]);

        $chat->update($data);

        return response()->json([
            'success' => true,
            'data' => $chat,
            'message' => 'Chat actualizado correctamente',
        ]);
    }
    public function destroy($id)
    {
        $chat = Chat::findOrFail($id);
        $chat->delete();

        // Si vienes desde el panel de Blade, te recarga la página con un mensaje verde
        return back()->with('success', 'Chat eliminado correctamente.');
    }
    
}
