// Injected từ Blade:
window.authUserId = authUserId; // phải gán biến này từ view
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: pusherKey,
    cluster: pusherCluster,
    forceTLS: true,
    // encrypted: true, // không cần nếu đã forceTLS
});

// Khởi tạo sau khi DOM load xong
document.addEventListener('DOMContentLoaded', function () {
    const conversationId = document.getElementById('chat-box')?.dataset.conversationId;
    const messagesContainer = document.getElementById('messages');
    const input = document.getElementById('chatInput');
    const form = document.getElementById('chatForm');

    // Hàm thêm tin nhắn vào khung chat
    function appendMessage(content, isSender = false) {
        const wrapper = document.createElement('div');
        wrapper.className = `flex ${isSender ? 'justify-end' : 'justify-start'}`;

        const bubble = document.createElement('div');
        bubble.className = `px-4 py-2 rounded-lg max-w-xs ${isSender ? 'bg-blue-500 text-white' : 'bg-gray-200 text-black'}`;
        bubble.textContent = content;

        wrapper.appendChild(bubble);
        messagesContainer.appendChild(wrapper);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Gửi tin nhắn
    form?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const content = input.value.trim();
        if (!content) return;

        try {
            await axios.post(`/chat/send/${conversationId}`, { content });
            input.value = '';
            appendMessage(content, true); // Hiển thị tin nhắn của mình
        } catch (error) {
            console.error('❌ Lỗi gửi tin nhắn:', error);
        }
    });

    // Nhận tin nhắn từ người khác
    if (conversationId) {
        window.Echo.private(`chat.${conversationId}`)
            .listen('ChatSent', (e) => {
                const msg = e.message;
                if (msg.user_id === window.authUserId) return;
                appendMessage(msg.content, false); // Hiển thị tin nhắn người khác
            });
    }
});

// Gửi tin nhắn mẫu nhanh
document.addEventListener('DOMContentLoaded', function () {
    // Bắt sự kiện click vào nút trả lời nhanh
    document.querySelectorAll('.quick-reply-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = document.getElementById('chatInput');
            input.value = this.dataset.text;
            input.focus();
        });
    });

    // Các xử lý chat khác như gửi, nhận tin nhắn...
});
