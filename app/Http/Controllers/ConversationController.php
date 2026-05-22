<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
   /**
     * Show all conversations for the authenticated user
     */
    public function index(): View
    {
        $users = auth()->user()->conversations() 
                    ->get()
                    ->map(function($user) {
                        $user->last_message = auth()->user()->lastMessageWith($user);
                        return $user;
                    })
                    ->sortByDesc(function($user) {
                        return $user->last_message?->created_at;
                    });

        return view('chats.conversations', compact('users'));
    }

    /**
     * Search for users to start a new conversation
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'query' => 'required|string|min:2'
            ]);

            // FIXED: Use $request->query('query') instead of $request->query
            $searchTerm = $request->query('query');
            
            $users = User::where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->where('id', '!=', auth()->id())
                        ->limit(10)
                        ->get();

            // Add profile photo URL and last message info for each user
            $users->each(function($user) {
                $user->profile_photo_url = $user->profile_photo_url;
                $user->last_message = auth()->user()->lastMessageWith($user);
            });

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
            
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}