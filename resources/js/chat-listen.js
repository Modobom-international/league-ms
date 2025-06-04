window.Echo.private(`chat.${conversationId}`)
    .listen('.ChatSent', (e) => {
        console.log("Đã nhận được ChatSent:", e);
        const msg = e.message;

        if (msg.user_id === window.authUserId) return;

        const wrapper = document.createElement('div');
        wrapper.className = 'flex justify-start';

        const bubble = document.createElement('div');
        bubble.className = 'px-4 py-2 rounded-lg max-w-xs bg-gray-200 text-black';
        bubble.textContent = msg.content;

        wrapper.appendChild(bubble);
        messagesContainer.appendChild(wrapper);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    });
