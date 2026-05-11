<x-app-layout>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Conversations</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f0f2f5; }
        .app { max-width: 500px; margin: 0 auto; background: white; min-height: 100vh; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { background: #0084ff; color: white; padding: 20px; }
        .header h1 { font-size: 24px; font-weight: 600; }
        .search-section { padding: 15px; border-bottom: 1px solid #e0e0e0; background: white; }
        .search-box { display: flex; gap: 10px; align-items: center; background: #f0f2f5; border-radius: 25px; padding: 8px 15px; }
        .search-box input { flex: 1; border: none; background: transparent; outline: none; font-size: 16px; }
        .search-box button { background: none; border: none; color: #0084ff; cursor: pointer; font-size: 16px; }
        .search-results { position: absolute; width: 470px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-top: 5px; z-index: 1000; }
        .conversations-list { padding: 10px; }
        .conversation-item { display: flex; align-items: center; padding: 12px; border-radius: 10px; cursor: pointer; transition: background 0.3s; text-decoration: none; color: inherit; }
        .conversation-item:hover { background: #f5f5f5; }
        .avatar { width: 50px; height: 50px; border-radius: 50%; margin-right: 15px; object-fit: cover; background: #0084ff; color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .conversation-info { flex: 1; }
        .conversation-name { font-weight: 600; margin-bottom: 5px; }
        .last-message { font-size: 14px; color: #666; display: flex; align-items: center; gap: 5px; }
        .message-time { font-size: 12px; color: #999; }
        .unread { background: #e7f3ff; }
        .unread-badge { background: #0084ff; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px; margin-left: 10px; }
        .user-result { display: flex; align-items: center; padding: 12px; cursor: pointer; }
        .user-result:hover { background: #f5f5f5; }
    </style>
</head>
<body>
    <div class="app">
        <div class="header">
            <h1>Chats</h1>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <div class="search-box">
                <input type="text" id="search-input" placeholder="Search users by name or email..." autocomplete="off">
                <button id="search-btn">🔍</button>
            </div>
            <div id="search-results" class="search-results" style="display: none;"></div>
        </div>

        <!-- Conversations List -->
        <div class="conversations-list" id="conversations-list">
            @forelse($users as $user)
                <a href="{{ route('chat', $user) }}" class="conversation-item {{ $user->last_message && !$user->last_message->is_read && $user->last_message->receiver_id == auth()->id() ? 'unread' : '' }}">
                    <div class="avatar">
                        @if($user->profile_photo_url)
                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                        @else
                            {{ substr($user->name, 0, 1) }}
                        @endif
                    </div>
                    <div class="conversation-info">
                        <div class="conversation-name">{{ $user->name }}</div>
                        @if($user->last_message)
                            <div class="last-message">
                                <span>{{ $user->last_message->sender_id == auth()->id() ? 'You: ' : '' }}</span>
                                <span>{{ Str::limit($user->last_message->message, 30) }}</span>
                                <span class="message-time">• {{ $user->last_message->created_at->diffForHumans() }}</span>
                            </div>
                        @else
                            <div class="last-message">No messages yet</div>
                        @endif
                    </div>
                    @if($user->last_message && !$user->last_message->is_read && $user->last_message->receiver_id == auth()->id())
                        <div class="unread-badge">1</div>
                    @endif
                </a>
            @empty
                <div style="text-align: center; padding: 40px; color: #666;">
                    <p>No conversations yet. Search for users to start chatting!</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const searchBtn = document.getElementById('search-btn');
        const searchResults = document.getElementById('search-results');
        const currentUserId = {{ auth()->id() }};

        // Search function with better error handling
function performSearch() {
    const query = searchInput.value.trim();
    
    if (query.length < 2) {
        searchResults.style.display = 'none';
        return;
    }

    // Show loading state
    searchResults.innerHTML = '<div style="padding: 12px; text-align: center;">Searching...</div>';
    searchResults.style.display = 'block';

    console.log('Searching for:', query);

    fetch(`/conversations/search?query=${encodeURIComponent(query)}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Server error');
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Search results data:', data); // Debug log
        
        // Check if the response has the new structure
        if (data.success && data.users && data.users.length > 0) {
            searchResults.innerHTML = data.users.map(user => `
                <div class="user-result" onclick="window.location.href='/chat/${user.id}'">
                    <div class="avatar" style="width: 40px; height: 40px;">
                        ${user.profile_photo_url ? 
                            `<img src="${user.profile_photo_url}" alt="${user.name}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">` : 
                            user.name.substring(0, 1)
                        }
                    </div>
                    <div style="margin-left: 10px;">
                        <div style="font-weight: 600;">${user.name}</div>
                        <div style="font-size: 12px; color: #666;">${user.email}</div>
                    </div>
                </div>
            `).join('');
        } else {
            searchResults.innerHTML = '<div style="padding: 12px; text-align: center;">No users found</div>';
        }
    })
    .catch(error => {
        console.error('Search error details:', error);
        searchResults.innerHTML = `<div style="padding: 12px; text-align: center; color: red;">
            Error: ${error.message}<br>
            <small>Check console for details</small>
        </div>`;
    });
}

        // Event listeners
        searchBtn.addEventListener('click', performSearch);
        
        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                performSearch();
            }
        });

        // Hide search results when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchResults.contains(e.target) && e.target !== searchInput && e.target !== searchBtn) {
                searchResults.style.display = 'none';
            }
        });

        // Listen for new messages to update conversation list
        if (window.Echo) {
            console.log('Echo initialized'); // Debug log
            window.Echo.private(`chat.${currentUserId}`)
                .listen('.MessageSent', (e) => {
                    console.log('New message received:', e); // Debug log
                    // Refresh the page to show updated conversation list
                    location.reload();
                });
        } else {
            console.error('Echo not initialized');
        }
    });
</script>
</body>
</html>
</x-app-layout>