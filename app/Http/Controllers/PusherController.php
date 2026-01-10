<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PusherController extends Controller
{
    public function index()
    {
        $messages = \App\Models\Message::take(50)->get();
        // Get unique users from messages for the sidebar list
        $activeUsers = \App\Models\Message::select('username')
                        ->distinct()
                        ->orderBy('created_at', 'desc')
                        ->take(10)
                        ->get()
                        ->pluck('username');
                        
        return view('chat', compact('messages', 'activeUsers'));
    }

    public function broadcast(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'message' => 'required|string',
        ]);

        $username = $request->username;
        $message = $request->message;

        \App\Models\Message::create([
            'username' => $username,
            'message' => $message,
        ]);

        event(new \App\Events\MessageSent($username, $message));
        
        return response()->json(['status' => 'Message Sent!']);
    }

    public function receive(Request $request)
    {
        return view('receive', ['message' => $request->get('message')]);
    }
}
