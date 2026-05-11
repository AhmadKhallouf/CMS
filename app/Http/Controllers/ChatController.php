<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Show the chat view with a specific user
     */
    public function index(User $user): View
{
    // Mark messages as read when opening chat
    Message::where('sender_id', $user->id)
          ->where('receiver_id', auth()->id())
          ->where('is_read', false)
          ->update(['is_read' => true]);

    return view('chats.chat', [  // Make sure this path matches
        'receiver' => $user,
    ]);
}

    /**
     * Get messages between current user and another user
     */
    public function getMessages(User $user): JsonResponse
    {
        $messages = Message::where(function($query) use ($user) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $user->id);
        })->orWhere(function($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', auth()->id());
        })
        ->with('sender', 'receiver')
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json($messages);
    }

    /**
     * Send a new message
     */
    public function sendMessage(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
            'message' => $request->message,
            'is_read' => false
        ]);

        $message->load('sender', 'receiver');
        
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(User $user): JsonResponse
    {
        Message::where('sender_id', $user->id)
               ->where('receiver_id', auth()->id())
               ->where('is_read', false)
               ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}