@php
    $webhookUrl = config('services.n8n.chat_webhook_url')
        ?: 'https://nonintrospective-remington-unquayed.ngrok-free.dev/webhook/ebdeba3f-6b4f-49f3-ba0a-8253dd226161/chat';
@endphp

<script type="module" defer>
    import Chatbot from 'https://cdn.n8nchatui.com/v1/embed.js';

    (() => {
        if (window.__sepsN8nChatInitialized) {
            return;
        }

        window.__sepsN8nChatInitialized = true;

        Chatbot.init({
            n8nChatUrl: @js($webhookUrl),
            metadata: {},
            theme: {
                button: {
                    backgroundColor: '#e60076',
                    right: 20,
                    bottom: 20,
                    size: 55,
                    iconColor: '#ffffff',
                    // customIconSrc: 'https://www.svgrepo.com/show/362552/chat-centered-dots-bold.svg',
                    customIconSize: 60,
                    customIconBorderRadius: 15,
                    autoWindowOpen: {
                        autoOpen: false,
                        openDelay: 2,
                    },
                    borderRadius: 'circle',
                    draggable: false,
                },
                tooltip: {
                    showTooltip: true,
                    tooltipMessage: "Hello 👋 I'm SEPS Assistant Happy to Help",
                    tooltipBackgroundColor: '#fff9f6',
                    tooltipTextColor: '#1c1c1c',
                    tooltipFontSize: 15,
                    hideTooltipOnMobile: true,
                },
                allowProgrammaticMessage: false,
                chatWindow: {
                    borderRadiusStyle: 'rounded',
                    avatarBorderRadius: 25,
                    messageBorderRadius: 6,
                    showTitle: true,
                    title: 'SEPS Chat Assistant',
                    // titleAvatarSrc: 'https://www.svgrepo.com/show/362552/chat-centered-dots-bold.svg',
                    avatarSize: 40,
                    welcomeMessage: 'Hello! This is SEPS Chat Question and Answer Agent',
                    errorMessage: 'Please connect me to n8n first',
                    backgroundColor: '#ffffff',
                    height: 600,
                    width: 400,
                    fontSize: 16,
                    starterPrompts: [
                        'FAQ: What is SEPS?',
                        'FAQ: Who can use SEPS?',
                        'FAQ: What are the benefits of using SEPS?',
                        'FAQ:How do suppliers submit eligibility documents?',
                        'FAQ: How are supplier documents verified?',
                        'FAQ: How will suppliers know if they are eligible?',
                    ],
                    starterPromptFontSize: 15,
                    renderHTML: false,
                    clearChatOnReload: false,
                    showScrollbar: false,
                    botMessage: {
                        backgroundColor: '#e60076',
                        textColor: '#fafafa',
                        showAvatar: true,
                        avatarSrc: 'https://www.svgrepo.com/show/334455/bot.svg',
                        showCopyToClipboardIcon: false,
                    },
                    userMessage: {
                        backgroundColor: '#fff6f3',
                        textColor: '#050505',
                        showAvatar: true,
                        avatarSrc: 'https://www.svgrepo.com/show/532363/user-alt-1.svg',
                    },
                    textInput: {
                        placeholder: 'Ask a question',
                        backgroundColor: '#ffffff',
                        sendButtonColor: '#e60076',
                        maxChars: 10000,
                        maxCharsWarningMessage: 'You exceeded the characters limit. Please input less than 10000 characters.',
                        autoFocus: false,
                        borderRadius: 6,
                        sendButtonBorderRadius: 50,
                    },
                },
            },
        });
    })();
</script>
