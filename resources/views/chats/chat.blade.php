<x-app-layout>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat with {{ $receiver->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html,body{height:100%;margin:0;background:#f0f2f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;}
        .chat-app{display:flex;flex-direction:column;height:100vh;max-width:800px;margin:0 auto;background:#fff;box-shadow:0 0 10px rgba(0,0,0,0.1);}
        .chat-header{background:#0084ff;color:#fff;padding:15px 20px;display:flex;align-items:center;gap:15px}
        .back-btn{color:#fff;text-decoration:none;font-size:24px}
        .messages-container{flex:1;overflow-y:auto;padding:20px;background:#f0f2f5;display:flex;flex-direction:column;gap:8px}
        .message-wrapper{display:flex;margin-bottom:4px;width:100%}
        .message-wrapper.sent{justify-content:flex-end}
        .message-wrapper.received{justify-content:flex-start}
        .message-bubble{max-width:70%;padding:10px 15px;border-radius:18px;word-wrap:break-word;box-shadow:0 1px 2px rgba(0,0,0,.1)}
        .sent .message-bubble{background:#0084ff;color:#fff;border-bottom-right-radius:4px}
        .received .message-bubble{background:#e4e6eb;color:#000;border-bottom-left-radius:4px}
        .message-time{font-size:.7rem;margin-top:5px;opacity:.7;text-align:right}
        .message-status{font-size:.7rem;margin-left:4px;opacity:.8}
        .chat-input-container{padding:15px 20px;background:#fff;border-top:1px solid #e0e0e0;display:flex;gap:10px;align-items:center}
        #message-input{flex:1;padding:12px 16px;border:1px solid #ddd;border-radius:25px;font-size:16px}
        #send-btn{background:#0084ff;color:#fff;border:none;border-radius:50%;width:48px;height:48px;display:flex;align-items:center;justify-content:center;font-size:20px;cursor:pointer}
        #send-btn:hover{background:#0073e6}
    </style>
</head>
<body>
<div class="chat-app">
    <div class="chat-header">
        <a href="{{ route('conversations') }}" class="back-btn">←</a>
        <h2>{{ $receiver->name }}</h2>
    </div>

    <div id="messages-container" class="messages-container"></div>

    <div class="chat-input-container">
        <input type="text" id="message-input" placeholder="Type a message..." autocomplete="off" />
        <button id="send-btn">➤</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const messagesContainer = document.getElementById('messages-container');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');

    const receiverId = {{ $receiver->id }};
    const currentUserId = {{ auth()->id() }};

    const formatTime = ts => new Date(ts).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
    const scrollToBottom = () => messagesContainer.scrollTop = messagesContainer.scrollHeight;

    function renderMessage(msg){
        const wrap = document.createElement('div');
        const isOwn = Number(msg.sender_id) === Number(currentUserId);
        wrap.className = `message-wrapper ${isOwn ? 'sent' : 'received'}`;

        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';
        bubble.innerHTML = `
            <div>${msg.message}</div>
            <div class="message-time">
                ${formatTime(msg.created_at || Date.now())}
                ${isOwn ? `<span class="message-status">${msg.is_read ? '✓✓' : '✓'}</span>` : ''}
            </div>`;
        wrap.appendChild(bubble);
        messagesContainer.appendChild(wrap);
        scrollToBottom();
    }

    // Load chat history
    fetch(`/chat/${receiverId}/messages`)
        .then(r => r.json())
        .then(data => {
            data.forEach(renderMessage);
            scrollToBottom();
        });

    // Send message
    function sendMessage() {
        const text = messageInput.value.trim();
        if (!text) return;
        const tempMsg = {
            message: text,
            sender_id: currentUserId,
            is_read: false,
            created_at: new Date()
        };
        renderMessage(tempMsg);
        messageInput.value = '';

        fetch(`/chat/${receiverId}/send`, {
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
            },
            body:JSON.stringify({message:text})
        }).catch(e=>console.error('Send error',e));
    }

    sendBtn.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', e=>{
        if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); sendMessage(); }
    });

    // Echo realtime receiver
    if(window.Echo){
        console.log('Echo ready, channel chat.'+currentUserId);
        window.Echo.private(`chat.${currentUserId}`)
          .listen('.MessageSent', e => {
              console.log('📩 Event received:', e);

              // The backend broadcasts a flat payload (id, message, sender_id, receiver_id, ...)
              // so we must read fields directly from `e`, not `e.message`.
              if (
                  Number(e.receiver_id) === Number(currentUserId) &&
                  Number(e.sender_id) === Number(receiverId)
              ) {
                  console.log('✅ Render incoming message for this conversation');
                  renderMessage(e);

                  // mark as read
                  fetch(`/chat/${receiverId}/read`, {
                      method:'POST',
                      headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}
                  });
              } else {
                  console.log('⚪ Ignored (not from this conversation)', {
                      currentUserId,
                      receiverId,
                      eventReceiver: e.receiver_id,
                      eventSender: e.sender_id,
                  });
              }
          })
          .error(err=>console.error('Echo error:',err));
    } else {
        console.error('Echo not detected');
    }
});
</script>
</body>
</html>
</x-app-layout>