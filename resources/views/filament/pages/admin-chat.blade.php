<x-filament-panels::page fullHeight class="fi-page-admin-chat">
    <style>
        .fi-page-admin-chat .fi-admin-chat {
            --chat-sidebar-width: 18rem;
            --chat-bg: #f0f2f5;
            --chat-sidebar-bg: #ffffff;
            --chat-header-bg: #ffffff;
            --chat-input-bg: #ffffff;
            --chat-bubble-sent: rgb(var(--primary-600, 8 145 178));
            --chat-bubble-received: #ffffff;
            --chat-bubble-received-text: #111827;
            --chat-border: #e5e7eb;
            --chat-muted: #6b7280;
        }

        .dark .fi-page-admin-chat .fi-admin-chat {
            --chat-bg: #0f1419;
            --chat-sidebar-bg: #1a1f26;
            --chat-header-bg: #1a1f26;
            --chat-input-bg: #1a1f26;
            --chat-bubble-sent: rgb(var(--primary-500, 6 182 212));
            --chat-bubble-received: #2d3748;
            --chat-bubble-received-text: #f3f4f6;
            --chat-border: #2d3748;
            --chat-muted: #9ca3af;
        }

        .fi-page-admin-chat .fi-admin-chat {
            display: flex;
            flex-direction: row;
            height: calc(100vh - 8rem);
            min-height: 32rem;
            overflow: hidden;
            border-radius: 0.75rem;
            border: 1px solid var(--chat-border);
            background: var(--chat-sidebar-bg);
            box-shadow: 0 1px 3px rgb(0 0 0 / 0.08);
        }

        .fi-page-admin-chat .fi-admin-chat__sidebar {
            display: flex;
            flex-direction: column;
            width: var(--chat-sidebar-width);
            min-width: var(--chat-sidebar-width);
            max-width: var(--chat-sidebar-width);
            border-right: 1px solid var(--chat-border);
            background: var(--chat-sidebar-bg);
        }

        .fi-page-admin-chat .fi-admin-chat__main {
            display: flex;
            flex: 1;
            flex-direction: column;
            min-width: 0;
            background: var(--chat-bg);
        }

        .fi-page-admin-chat .fi-admin-chat__search {
            width: 100%;
            border-radius: 0.5rem;
            border: 1px solid var(--chat-border);
            background: var(--chat-bg);
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            color: inherit;
            outline: none;
        }

        .fi-page-admin-chat .fi-admin-chat__search:focus {
            border-color: rgb(var(--primary-500, 6 182 212));
            box-shadow: 0 0 0 2px rgb(var(--primary-500, 6 182 212) / 0.2);
        }

        .fi-page-admin-chat .fi-admin-chat__user-btn {
            display: flex;
            width: 100%;
            align-items: center;
            gap: 0.75rem;
            border: none;
            border-bottom: 1px solid var(--chat-border);
            padding: 0.75rem 1rem;
            text-align: left;
            background: transparent;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .fi-page-admin-chat .fi-admin-chat__user-btn:hover {
            background: rgb(0 0 0 / 0.04);
        }

        .dark .fi-page-admin-chat .fi-admin-chat__user-btn:hover {
            background: rgb(255 255 255 / 0.05);
        }

        .fi-page-admin-chat .fi-admin-chat__user-btn--active {
            background: rgb(var(--primary-500, 6 182 212) / 0.12);
            border-left: 3px solid rgb(var(--primary-500, 6 182 212));
            padding-left: calc(1rem - 3px);
        }

        .fi-page-admin-chat .fi-admin-chat__user-btn--unread:not(.fi-admin-chat__user-btn--active) {
            background: rgb(var(--primary-500, 6 182 212) / 0.06);
        }

        .fi-page-admin-chat .fi-admin-chat__avatar {
            display: flex;
            height: 2.75rem;
            width: 2.75rem;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 9999px;
            background: rgb(var(--primary-500, 6 182 212));
            font-size: 0.875rem;
            font-weight: 600;
            color: #fff;
        }

        .fi-page-admin-chat .fi-admin-chat__avatar img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }

        .fi-page-admin-chat .fi-admin-chat__badge {
            display: inline-flex;
            min-width: 1.25rem;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            background: rgb(var(--primary-500, 6 182 212));
            padding: 0 0.375rem;
            font-size: 0.6875rem;
            font-weight: 700;
            color: #fff;
        }

        .fi-page-admin-chat .fi-admin-chat__messages {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding: 1rem 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .fi-page-admin-chat .chat-bubble-row {
            display: flex;
            width: 100%;
        }

        .fi-page-admin-chat .chat-bubble-row--sent {
            justify-content: flex-end;
        }

        .fi-page-admin-chat .chat-bubble-row--received {
            justify-content: flex-start;
        }

        .fi-page-admin-chat .chat-bubble {
            display: inline-block;
            width: fit-content;
            max-width: min(75%, 22rem);
            padding: 0.5rem 0.875rem 0.375rem;
            font-size: 0.875rem;
            line-height: 1.45;
            word-wrap: break-word;
            overflow-wrap: break-word;
            box-shadow: 0 1px 2px rgb(0 0 0 / 0.06);
        }

        .fi-page-admin-chat .chat-bubble--sent {
            background: var(--chat-bubble-sent);
            color: #fff;
            border-radius: 18px 18px 4px 18px !important;
        }

        .fi-page-admin-chat .chat-bubble--received {
            background: var(--chat-bubble-received);
            color: var(--chat-bubble-received-text);
            border-radius: 18px 18px 18px 4px !important;
        }

        .dark .fi-page-admin-chat .chat-bubble--received {
            border: 1px solid var(--chat-border);
        }

        .fi-page-admin-chat .chat-bubble__time {
            margin-top: 0.25rem;
            text-align: right;
            font-size: 0.6875rem;
            opacity: 0.85;
        }

        .fi-page-admin-chat .chat-bubble--received .chat-bubble__time {
            color: var(--chat-muted);
        }

        .fi-page-admin-chat .fi-admin-chat__composer {
            display: flex;
            gap: 0.5rem;
            border-top: 1px solid var(--chat-border);
            background: var(--chat-input-bg);
            padding: 1rem 1.25rem;
        }

        .fi-page-admin-chat .fi-admin-chat__composer-input {
            flex: 1;
            min-width: 0;
            border-radius: 1.5rem;
            border: 1px solid var(--chat-border);
            background: var(--chat-bg);
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            color: inherit;
            outline: none;
        }

        .fi-page-admin-chat .fi-admin-chat__composer-input:focus {
            border-color: rgb(var(--primary-500, 6 182 212));
        }

        .fi-page-admin-chat .fi-admin-chat__header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid var(--chat-border);
            background: var(--chat-header-bg);
            padding: 0.875rem 1.25rem;
        }
    </style>

    <div class="fi-admin-chat -mx-4 md:-mx-6 lg:-mx-8" wire:poll.60s="loadUsers">
        {{-- Left: conversations --}}
        <aside class="fi-admin-chat__sidebar">
            <div class="border-b p-4" style="border-color: var(--chat-border);">
                <h2 class="text-base font-semibold" style="color: inherit;">Messages</h2>
                <div class="mt-3">
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="searchTerm"
                        placeholder="Search users..."
                        class="fi-admin-chat__search"
                    />
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto">
                @forelse($users as $user)
                    <button
                        type="button"
                        wire:click="selectUser({{ $user->id }})"
                        wire:key="user-{{ $user->id }}"
                        @class([
                            'fi-admin-chat__user-btn',
                            'fi-admin-chat__user-btn--active' => $selectedUser?->id === $user->id,
                            'fi-admin-chat__user-btn--unread' => $user->unread_count > 0,
                        ])
                    >
                        <div class="fi-admin-chat__avatar">
                            @if ($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" />
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="truncate text-sm font-medium">{{ $user->name }}</p>
                                @if ($user->unread_count > 0)
                                    <span class="fi-admin-chat__badge">
                                        {{ $user->unread_count > 9 ? '9+' : $user->unread_count }}
                                    </span>
                                @endif
                            </div>
                            <p class="truncate text-xs" style="color: var(--chat-muted);">{{ $user->email }}</p>
                            @if ($user->last_message)
                                <p class="mt-0.5 truncate text-xs" style="color: var(--chat-muted);">
                                    @if ($user->last_message->sender_id == auth()->id())
                                        <span>You: </span>
                                    @endif
                                    {{ Str::limit($user->last_message->message, 36) }}
                                </p>
                            @endif
                        </div>
                    </button>
                @empty
                    <div class="p-6 text-center text-sm" style="color: var(--chat-muted);">
                        <p class="font-medium">No conversations yet</p>
                        <p class="mt-1">User chats will show up here.</p>
                    </div>
                @endforelse
            </div>
        </aside>

        {{-- Right: active chat --}}
        <div class="fi-admin-chat__main">
            @if ($selectedUser)
                <header class="fi-admin-chat__header">
                    <div class="fi-admin-chat__avatar" style="height: 2.5rem; width: 2.5rem;">
                        @if ($selectedUser->profile_photo_url)
                            <img src="{{ $selectedUser->profile_photo_url }}" alt="{{ $selectedUser->name }}" />
                        @else
                            {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="truncate font-semibold">{{ $selectedUser->name }}</p>
                        <p class="truncate text-xs" style="color: var(--chat-muted);">{{ $selectedUser->email }}</p>
                    </div>
                </header>

                <div
                    x-data="adminChatScroller()"
                    x-init="init()"
                    x-on:scroll-chat-to-bottom.window="scrollToBottomSoon()"
                    class="fi-admin-chat__messages"
                    id="admin-chat-messages"
                >
                    @foreach ($messages as $message)
                        @php $isOwn = $message['sender_id'] == auth()->id(); @endphp
                        <div
                            wire:key="message-{{ $message['id'] ?? md5(json_encode($message)) }}"
                            @class([
                                'chat-bubble-row',
                                'chat-bubble-row--sent' => $isOwn,
                                'chat-bubble-row--received' => ! $isOwn,
                            ])
                        >
                            <div @class([
                                'chat-bubble',
                                'chat-bubble--sent' => $isOwn,
                                'chat-bubble--received' => ! $isOwn,
                            ])>
                                <p class="whitespace-pre-wrap break-words">{{ $message['message'] }}</p>
                                <p class="chat-bubble__time">
                                    {{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}
                                    @if ($isOwn)
                                        <span class="ml-1">{{ ! empty($message['is_read']) ? '✓✓' : '✓' }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach

                    @if (count($messages) === 0)
                        <div class="flex flex-1 items-center justify-center py-16 text-center text-sm" style="color: var(--chat-muted);">
                            <div>
                                <p class="font-medium">No messages yet</p>
                                <p class="mt-1">Send a message to start the conversation.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="fi-admin-chat__composer">
                    <form wire:submit="sendMessage" class="flex w-full gap-2">
                        <input
                            type="text"
                            wire:model="newMessage"
                            placeholder="Type a message..."
                            autocomplete="off"
                            class="fi-admin-chat__composer-input"
                        />
                        <x-filament::button type="submit" icon="heroicon-m-paper-airplane">
                            Send
                        </x-filament::button>
                    </form>
                </div>
            @else
                <div class="flex flex-1 flex-col items-center justify-center gap-3 p-8 text-center" style="color: var(--chat-muted);">
                    <x-filament::icon
                        icon="heroicon-o-chat-bubble-left-right"
                        class="h-16 w-16 opacity-40"
                    />
                    <div>
                        <p class="text-lg font-medium" style="color: inherit;">Select a conversation</p>
                        <p class="mt-1 text-sm">Pick a user on the left to open the chat.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @script
    <script>
        function adminChatScroller() {
            return {
                scrollToBottom() {
                    const container = document.getElementById('admin-chat-messages');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                },
                scrollToBottomSoon() {
                    this.$nextTick(() => {
                        requestAnimationFrame(() => {
                            setTimeout(() => this.scrollToBottom(), 50);
                        });
                    });
                },
                init() {
                    this.scrollToBottomSoon();

                    if (typeof Livewire !== 'undefined') {
                        Livewire.hook('commit', ({ component, succeed }) => {
                            succeed(() => {
                                if (component.el?.querySelector?.('#admin-chat-messages')) {
                                    this.scrollToBottomSoon();
                                }
                            });
                        });
                    }
                },
            };
        }
    </script>
    @endscript
</x-filament-panels::page>
