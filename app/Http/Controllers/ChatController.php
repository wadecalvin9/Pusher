<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;

class ChatController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get all conversations for the current user, excluding self-chats
        $conversations = Conversation::where(function($query) use ($user) {
            $query->where('user_one_id', $user->id)
                  ->orWhere('user_two_id', $user->id);
        })
        ->whereColumn('user_one_id', '!=', 'user_two_id')
        ->with(['userOne', 'userTwo', 'messages' => function($query) {
                $query->latest()->first();
            }])
            ->get();
            
        // Get all users for starting new conversations
        $users = User::where('id', '!=', $user->id)->get();
        
        return view('chat', compact('conversations', 'users'));
    }

    public function getOrCreateConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user_id;
        $currentUserId = auth()->id();

        if ($userId == $currentUserId) {
            return response()->json(['error' => 'Cannot chat with yourself'], 422);
        }

        // Find existing conversation
        $conversation = Conversation::where(function($query) use ($currentUserId, $userId) {
            $query->where('user_one_id', $currentUserId)
                  ->where('user_two_id', $userId);
        })->orWhere(function($query) use ($currentUserId, $userId) {
            $query->where('user_one_id', $userId)
                  ->where('user_two_id', $currentUserId);
        })->first();

        // Create if doesn't exist
        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $currentUserId,
                'user_two_id' => $userId,
            ]);
        }

        // Load messages
        $messages = $conversation->messages()->with('user')->get();

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        // Load the user relationship
        $message->load('user');

        // Broadcast the message
        event(new MessageSent($message));

        return response()->json([
            'status' => 'Message Sent!',
            'message' => $message,
        ]);
    }
}
