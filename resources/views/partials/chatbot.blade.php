<style>
    /* ── Chatbot Widget Styles ── */
    #chatbot-fab {
        animation: fab-pulse 3s ease-in-out infinite;
    }

    @keyframes fab-pulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(0, 128, 128, 0.4);
        }

        50% {
            box-shadow: 0 0 0 10px rgba(0, 128, 128, 0);
        }
    }

    #chatbot-window {
        transition: opacity 0.25s ease, transform 0.25s cubic-bezier(.4, 0, .2, 1);
    }

    #chatbot-window.hidden-chat {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
        pointer-events: none;
    }

    #chatbot-window.visible-chat {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: all;
    }

    /* Message bubbles */
    .msg-bot {
        background: linear-gradient(135deg, #f0fafa 0%, #e0f4f4 100%);
        border: 1px solid rgba(0, 128, 128, 0.15);
        border-radius: 4px 16px 16px 16px;
    }

    .msg-user {
        background: linear-gradient(135deg, #008080 0%, #006666 100%);
        color: #fff;
        border-radius: 16px 4px 16px 16px;
    }

    /* Navigation buttons inside bot messages */
    .msg-bot a[href] {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        padding: 7px 14px;
        background: #10454F;
        color: #BDE038 !important;
        font-weight: 700;
        font-size: 12px;
        border-radius: 8px;
        text-decoration: none !important;
        transition: background 0.2s, transform 0.15s;
        border: none;
    }

    .msg-bot a[href]:hover {
        background: #006666;
        transform: translateY(-1px);
    }

    /* Typing indicator */
    .typing-dot {
        width: 7px;
        height: 7px;
        background: #008080;
        border-radius: 50%;
        animation: typing-bounce 1.2s ease-in-out infinite;
    }

    .typing-dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing-bounce {

        0%,
        80%,
        100% {
            transform: translateY(0);
            opacity: 0.4;
        }

        40% {
            transform: translateY(-6px);
            opacity: 1;
        }
    }

    /* Scrollbar */
    #chatbot-messages::-webkit-scrollbar {
        width: 4px;
    }

    #chatbot-messages::-webkit-scrollbar-thumb {
        background: rgba(0, 128, 128, .3);
        border-radius: 4px;
    }

    #chatbot-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    /* Send button */
    #chatbot-send:not(:disabled):hover {
        background: #006666;
        transform: scale(1.05);
    }

    #chatbot-send {
        transition: background 0.2s, transform 0.15s;
    }
</style>

<!-- ── Floating Action Button ── -->
<button id="chatbot-fab" aria-label="Open chat assistant"
    class="fixed bottom-6 right-6 z-50 w-14 h-14 rounded-full bg-logo-teal text-white shadow-2xl flex items-center justify-center focus:outline-none focus:ring-4 focus:ring-logo-teal/40">
    <svg id="fab-icon-open" class="w-6 h-6 transition-all duration-200" fill="none" stroke="currentColor"
        viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.84L3 20l1.09-3.27C3.4 15.5 3 13.8 3 12 3 7.58 7.03 4 12 4s9 3.58 9 8z" />
    </svg>
    <svg id="fab-icon-close" class="w-6 h-6 transition-all duration-200 hidden" fill="none" stroke="currentColor"
        viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
    </svg>
</button>

<!-- ── Chat Window ── -->
<div id="chatbot-window"
    class="hidden-chat fixed bottom-24 right-6 z-50 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-logo-teal/20 flex flex-col overflow-hidden"
    style="max-height: 520px; height: 520px;">

    <!-- Header -->
    <div class="flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-logo-teal to-teal-700 text-white flex-shrink-0">
        <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2" />
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold leading-none">GReAT Assistant</p>
            <p class="text-xs text-white/70 mt-0.5">Ask me anything about this system</p>
        </div>
        <span class="flex items-center gap-1 text-xs text-white/80">
            <span class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></span>
            Online
        </span>
    </div>

    <!-- Messages -->
    <div id="chatbot-messages" class="flex-1 overflow-y-auto px-4 py-4 space-y-3 bg-gray-50/50">
        <!-- Welcome message -->
        <div class="flex items-start gap-2">
            <div class="w-7 h-7 rounded-full bg-logo-teal/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2" />
                </svg>
            </div>
            <div class="msg-bot px-3 py-2.5 text-sm text-gray-700 max-w-[85%] leading-relaxed">
                👋 Hi! I'm the <strong>GReAT Assistant</strong>. I can help you navigate the
                <em>Government Revenue, Accounting and Taxation System</em>.<br><br>
                What would you like to know?
            </div>
        </div>

        <!-- Suggested questions -->
        <div class="flex flex-wrap gap-1.5 ml-9">
            <button onclick="sendSuggestion('What modules are available?')"
                class="text-xs px-2.5 py-1 rounded-full border border-logo-teal/30 text-teal-700 bg-white hover:bg-logo-teal hover:text-white transition-all duration-150">
                What modules are available?
            </button>
            <button onclick="sendSuggestion('How do I process a business permit?')"
                class="text-xs px-2.5 py-1 rounded-full border border-logo-teal/30 text-teal-700 bg-white hover:bg-logo-teal hover:text-white transition-all duration-150">
                Business permit process?
            </button>
            <button onclick="sendSuggestion('How do I pay real property tax?')"
                class="text-xs px-2.5 py-1 rounded-full border border-logo-teal/30 text-teal-700 bg-white hover:bg-logo-teal hover:text-white transition-all duration-150">
                Real property tax?
            </button>
        </div>
    </div>

    <!-- Input -->
    <div class="flex-shrink-0 border-t border-gray-100 bg-white px-3 py-3">
        <div class="flex items-end gap-2">
            <textarea id="chatbot-input" rows="1" placeholder="Type your question…"
                class="flex-1 resize-none rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 focus:border-logo-teal transition-all duration-150 max-h-28 overflow-y-auto"
                style="min-height: 38px; line-height: 1.4;" onkeydown="handleChatKey(event)"></textarea>
            <button id="chatbot-send" onclick="sendMessage()"
                class="w-10 h-10 rounded-xl bg-logo-teal text-white flex items-center justify-center flex-shrink-0 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 disabled:opacity-40 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </button>
        </div>
        <p class="text-[10px] text-gray-400 text-center mt-1.5">Powered by AI · GReAT System</p>
    </div>
</div>

<script>
    (function() {
        /* ── State ── */
        const conversationHistory = [];
        let isOpen = false;
        let isTyping = false;

        /* ── DOM refs ── */
        const fab = document.getElementById('chatbot-fab');
        const win = document.getElementById('chatbot-window');
        const msgs = document.getElementById('chatbot-messages');
        const input = document.getElementById('chatbot-input');
        const sendBtn = document.getElementById('chatbot-send');
        const iconOpen = document.getElementById('fab-icon-open');
        const iconClose = document.getElementById('fab-icon-close');

        /* ── Toggle ── */
        fab.addEventListener('click', () => {
            isOpen = !isOpen;
            win.classList.toggle('hidden-chat', !isOpen);
            win.classList.toggle('visible-chat', isOpen);
            iconOpen.classList.toggle('hidden', isOpen);
            iconClose.classList.toggle('hidden', !isOpen);
            if (isOpen) setTimeout(() => input.focus(), 300);
        });

        /* ── Auto-resize textarea ── */
        input.addEventListener('input', () => {
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 112) + 'px';
        });

        /* ── Key handler ── */
        window.handleChatKey = function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        };

        /* ── Suggestion chips ── */
        window.sendSuggestion = function(text) {
            input.value = text;
            sendMessage();
        };

        /* ── Send message ── */
        window.sendMessage = async function() {
            const text = input.value.trim();
            if (!text || isTyping) return;

            appendMessage('user', text);
            conversationHistory.push({
                role: 'user',
                content: text
            });

            input.value = '';
            input.style.height = 'auto';
            sendBtn.disabled = true;
            isTyping = true;

            const typingEl = showTyping();

            try {
                const response = await fetch('/chatbot/message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    },
                    body: JSON.stringify({
                        messages: conversationHistory
                    })
                });

                const data = await response.json();
                typingEl.remove();

                if (data.reply) {
                    conversationHistory.push({
                        role: 'assistant',
                        content: data.reply
                    });
                    appendMessage('bot', data.reply);
                } else {
                    appendMessage('bot', '⚠️ Sorry, I encountered an error. Please try again.');
                }
            } catch (err) {
                typingEl.remove();
                appendMessage('bot', '⚠️ Network error. Please check your connection and try again.');
            }

            sendBtn.disabled = false;
            isTyping = false;
        };

        /* ── Append message bubble ── */
        function appendMessage(role, text) {
            const wrapper = document.createElement('div');
            wrapper.className = 'flex items-start gap-2' + (role === 'user' ? ' flex-row-reverse' : '');

            const avatar = document.createElement('div');
            avatar.className =
                'w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 text-xs font-bold';

            if (role === 'bot') {
                avatar.className += ' bg-logo-teal/10';
                avatar.innerHTML = `<svg class="w-4 h-4 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
            </svg>`;
            } else {
                avatar.className += ' bg-logo-teal text-white';
                avatar.textContent = ('{{ auth()->user()->name ?? 'U' }}'.charAt(0) || 'U').toUpperCase();
            }

            const bubble = document.createElement('div');
            bubble.className = (role === 'bot' ? 'msg-bot' : 'msg-user') +
                ' px-3 py-2.5 text-sm max-w-[85%] leading-relaxed';

            // ── KEY FIX: render bot HTML (buttons), escape user text ──
            if (role === 'bot') {
                bubble.innerHTML = formatBotMessage(text);
            } else {
                bubble.textContent = text;
            }

            wrapper.appendChild(avatar);
            wrapper.appendChild(bubble);
            msgs.appendChild(wrapper);
            msgs.scrollTop = msgs.scrollHeight;
        }

        /* ── Typing indicator ── */
        function showTyping() {
            const wrapper = document.createElement('div');
            wrapper.className = 'flex items-start gap-2';
            wrapper.id = 'typing-indicator';
            wrapper.innerHTML = `
            <div class="w-7 h-7 rounded-full bg-logo-teal/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                </svg>
            </div>
            <div class="msg-bot px-4 py-3 flex items-center gap-1.5">
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
            </div>`;
            msgs.appendChild(wrapper);
            msgs.scrollTop = msgs.scrollHeight;
            return wrapper;
        }

        /* ────────────────────────────────────────────────────────────────
         * formatBotMessage
         * Safely renders bot replies:
         *   - Preserves <a href="..."> buttons from Groq (navigation links)
         *   - Escapes everything else so no XSS is possible
         *   - Converts **bold**, *italic*, `code`, and \n line breaks
         * ──────────────────────────────────────────────────────────────── */
        function formatBotMessage(text) {
            // 1. Pull out all <a ...>...</a> tags and replace with safe placeholders
            const anchors = [];
            const ANCHOR_TOKEN = '%%%ANCHOR_';
            let idx = 0;
            const stripped = text.replace(/<a(\s[^>]*)>([\s\S]*?)<\/a>/gi, (match) => {
                anchors.push(match);
                return ANCHOR_TOKEN + (idx++) + '%%%';
            });

            // 2. Escape the remaining text (prevents XSS)
            let safe = stripped
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');

            // 3. Markdown-lite formatting
            safe = safe
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`(.*?)`/g, '<code class="bg-white/60 px-1 rounded text-xs font-mono">$1</code>')
                .replace(/\n/g, '<br>');

            // 4. Re-inject the original <a> tags
            anchors.forEach((a, i) => {
                safe = safe.replace(ANCHOR_TOKEN + i + '%%%', a);
            });

            return safe;
        }

    })();
</script>
