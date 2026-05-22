<?php

namespace App\Filament\Pages;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;

class AdminChatPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Chats';

    protected static ?string $title = 'Chat';

    protected static ?string $slug = 'chat';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.admin-chat';

    public ?User $selectedUser = null;

    public array $messages = [];

    public string $newMessage = '';

    public string $searchTerm = '';

    public $users = [];

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }

    public function mount(): void
    {
        $this->loadUsers();
    }

    public function getListeners(): array
    {
        $userId = auth()->id();

        return [
            "echo-private:chat.{$userId},.MessageSent" => 'receiveMessage',
        ];
    }

    public function loadUsers(): void
    {
        $currentUserId = auth()->id();

        $this->users = User::where('id', '!=', $currentUserId)
            ->withCount(['sentMessages', 'receivedMessages'])
            ->havingRaw('sent_messages_count + received_messages_count > 0')
            ->orderBy('name')
            ->get()
            ->map(function ($user) use ($currentUserId) {
                $lastMessage = Message::where(function ($query) use ($user, $currentUserId) {
                    $query->where('sender_id', $currentUserId)
                        ->where('receiver_id', $user->id);
                })->orWhere(function ($query) use ($user, $currentUserId) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', $currentUserId);
                })->latest()->first();

                $user->last_message = $lastMessage;
                $user->unread_count = Message::where('sender_id', $user->id)
                    ->where('receiver_id', $currentUserId)
                    ->where('is_read', false)
                    ->count();

                return $user;
            })
            ->sortByDesc(fn ($user) => $user->last_message?->created_at)
            ->values();
    }

    public function updatedSearchTerm(): void
    {
        $this->searchUsers();
    }

    public function searchUsers(): void
    {
        if (strlen($this->searchTerm) < 2) {
            $this->loadUsers();

            return;
        }

        $currentUserId = auth()->id();

        $this->users = User::where('id', '!=', $currentUserId)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
            })
            ->get()
            ->map(function ($user) use ($currentUserId) {
                $user->unread_count = Message::where('sender_id', $user->id)
                    ->where('receiver_id', $currentUserId)
                    ->where('is_read', false)
                    ->count();

                return $user;
            });
    }

    public function selectUser(int $userId): void
    {
        $this->selectedUser = User::find($userId);
        $this->loadMessages();
        $this->markAsRead();
        $this->searchTerm = '';
        $this->loadUsers();
    }

    public function loadMessages(): void
    {
        if (! $this->selectedUser) {
            return;
        }

        $this->messages = Message::where(function ($query) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $this->selectedUser->id);
        })->orWhere(function ($query) {
            $query->where('sender_id', $this->selectedUser->id)
                ->where('receiver_id', auth()->id());
        })
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();

        $this->dispatch('scroll-chat-to-bottom');
    }

    public function sendMessage(): void
    {
        if (! $this->newMessage || ! $this->selectedUser) {
            return;
        }

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->selectedUser->id,
            'message' => $this->newMessage,
            'is_read' => false,
        ]);

        $message->load('sender', 'receiver');

        broadcast(new MessageSent($message))->toOthers();

        $this->messages[] = $message->toArray();
        $this->newMessage = '';
        $this->loadUsers();

        $this->dispatch('scroll-chat-to-bottom');
    }

    public function markAsRead(): void
    {
        if (! $this->selectedUser) {
            return;
        }

        Message::where('sender_id', $this->selectedUser->id)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $this->loadUsers();
    }

    public function receiveMessage(array $payload): void
    {
        if ((int) ($payload['receiver_id'] ?? 0) !== (int) auth()->id()) {
            $this->loadUsers();

            return;
        }

        if ($this->selectedUser && (int) $payload['sender_id'] === (int) $this->selectedUser->id) {
            $exists = collect($this->messages)->contains(fn ($m) => ($m['id'] ?? null) == ($payload['id'] ?? null));

            if (! $exists) {
                $this->messages[] = $payload;
                $this->markAsRead();
                $this->dispatch('scroll-chat-to-bottom');
            }
        }

        $this->loadUsers();
    }
}
