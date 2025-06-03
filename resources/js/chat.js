// Lắng nghe channel riêng của người dùng
window.Echo.private(`chat.${window.Laravel.userId}`)
    .listen('ChatSend', (e) => {
        appendMessage(e.message, false); // false = tin nhắn nhận
    });

function appendMessage(message, isSender = false) {
    const container = document.getElementById('chat-box');

    const messageEl = document.createElement('div');
    messageEl.classList.add('p-2', 'mb-1', 'rounded', 'max-w-[75%]');
    messageEl.classList.add(isSender ? 'bg-green-200 ml-auto text-right' : 'bg-gray-100');
    messageEl.innerText = message.content;

    container.appendChild(messageEl);
    container.scrollTop = container.scrollHeight;
}

// Gửi tin nhắn
document.getElementById('send-btn').addEventListener('click', () => {
    const input = document.getElementById('message-input');
    const message = input.value.trim();
    if (message !== '') {
        axios.post('/messages/send', {
            content: message,
            receiver_id: window.Laravel.receiverId,
        });
        appendMessage({ content: message }, true);
        input.value = '';
    }
});
