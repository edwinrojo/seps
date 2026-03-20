@php
    $webhookUrl = config('services.n8n.chat_webhook_url')
        ?: 'https://nonintrospective-remington-unquayed.ngrok-free.dev/webhook/ebdeba3f-6b4f-49f3-ba0a-8253dd226161/chat';
@endphp

<script>
    (() => {
        if (window.__sepsN8nChatInitialized) {
            return;
        }

        window.__sepsN8nChatInitialized = true;

        const webhookUrl = @js($webhookUrl);

        const ensureStylesheet = () => {
            if (document.getElementById('seps-n8n-chat-style')) {
                return;
            }

            const link = document.createElement('link');
            link.id = 'seps-n8n-chat-style';
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css';
            document.head.appendChild(link);
        };

        const mountChat = async () => {
            if (window.__sepsN8nChatMounted) {
                return;
            }

            ensureStylesheet();

            const { createChat } = await import('https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js');

            createChat({ webhookUrl });
            window.__sepsN8nChatMounted = true;
        };

        mountChat();
        document.addEventListener('livewire:navigated', mountChat);
    })();
</script>
