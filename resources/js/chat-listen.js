// chat-listen.js

// Đảm bảo DOM đã sẵn sàng (nếu bạn load file này ở cuối trang thì có thể bỏ phần này)
document.addEventListener('DOMContentLoaded', () => {
    const chatBox = document.getElementById('chat-box');
    const messagesContainer = document.getElementById('messages');

    if (!chatBox || !messagesContainer) {
        console.warn('❌ Chat elements not found, skipping Echo listener');
        return; // Nếu không phải trang chat, không làm gì cả
    }

    const conversationId = chatBox.dataset.conversationId;

    if (!window.Echo) {
        console.error('❌ window.Echo chưa được khởi tạo!');
        return;
    }

    if (!window.authUserId) {
        console.error('❌ window.authUserId chưa được thiết lập!');
        return;
    }

    console.log(`▶️ Listening on private channel chat.${conversationId}`);

    window.Echo.private(`chat.${conversationId}`)
        .listen('.ChatSent', (e) => {
            const msg = e.message;

            // Nếu tin nhắn do chính user gửi, không cần hiển thị lại
            if (msg.user_id === window.authUserId) {
                console.log('🚫 Tin nhắn của chính mình, bỏ qua.');
                return;
            }

            // Tạo phần tử hiển thị tin nhắn mới
            const wrapper = document.createElement('div');
            wrapper.className = 'flex justify-start';

            const bubble = document.createElement('div');
            bubble.className = 'px-4 py-2 rounded-lg max-w-xs bg-gray-200 text-black';
            bubble.textContent = msg.content;

            wrapper.appendChild(bubble);
            messagesContainer.appendChild(wrapper);

            // Cuộn xuống cuối để hiện tin nhắn mới nhất
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
});
