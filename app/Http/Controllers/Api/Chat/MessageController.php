<?php

namespace App\Http\Controllers\Api\Chat;

use App\Models\User;
use App\Models\Message;
use App\Events\NewMessage;
use App\Events\UserTyping;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MessageController extends Controller
{
    public function index(User $user)
    {
        $currentUser = Auth::user();

        $messages = Message::where(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $currentUser->id)
                ->where('recipient_id', $user->id);
        })
            ->orWhere(function ($query) use ($currentUser, $user) {
                $query->where('sender_id', $user->id)
                    ->where('recipient_id', $currentUser->id);
            })
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function store(Request $request, User $user)
    {
        // return $request->all();
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $user->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        broadcast(new NewMessage($message))->toOthers();

        return response()->json($message->load(['sender', 'recipient']), 201);
    }

    public function show(User $recipient)
    {
        return view('chat', [
            'recipient' => $recipient,
            'token' => auth()->user()->createToken('chat-token', ['chat'])->plainTextToken
        ]);
    }

    public function markAsRead(User $sender)
    {
        Message::where('sender_id', $sender->id)
            ->where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }


    public function typing()
    {
        // Fire the typing event
        broadcast(new UserTyping(Auth::id()))->toOthers();
        return response()->json(['status' => 'typing broadcasted!']);
    }

    public function setOnline()
    {
        Cache::put('user-is-online-' . Auth::id(), true, now()->addMinutes(5));
        return response()->json(['status' => 'Online']);
    }

    public function setOffline()
    {
        Cache::forget('user-is-online-' . Auth::id());
        return response()->json(['status' => 'Offline']);
    }


    public function users()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('users', compact('users'));
    }
}