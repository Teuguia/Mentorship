const messagesRoot = document.querySelector('[data-messages-root]');

if (messagesRoot && window.axios) {
    const conversationId = messagesRoot.dataset.conversationId;
    const currentUserId = Number(messagesRoot.dataset.currentUserId || 0);
    const feedUrl = messagesRoot.dataset.feedUrl;
    const messagesList = document.querySelector('[data-messages-list]');
    const form = document.querySelector('[data-message-form]');
    const textarea = form?.querySelector('textarea[name="body"]');
    const submitButton = form?.querySelector('button[type="submit"]');
    const emptyState = document.querySelector('[data-empty-state]');
    const conversationPreview = document.querySelector(`[data-conversation-preview="${conversationId}"]`);
    const conversationTime = document.querySelector(`[data-conversation-time="${conversationId}"]`);
    const renderedMessageIds = new Set(
        Array.from(document.querySelectorAll('[data-message-id]'))
            .map((element) => Number(element.dataset.messageId))
            .filter((id) => Number.isFinite(id))
    );

    const escapeHtml = (value) =>
        String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

    const scrollToBottom = () => {
        if (messagesList) {
            messagesList.scrollTop = messagesList.scrollHeight;
        }
    };

    const updatePreview = (body, label = 'A l\'instant') => {
        if (conversationPreview) {
            conversationPreview.textContent = body;
        }

        if (conversationTime) {
            conversationTime.textContent = label;
        }
    };

    const appendMessage = (message) => {
        const messageId = Number(message.id);

        if (!messagesList || !Number.isFinite(messageId) || renderedMessageIds.has(messageId)) {
            return;
        }

        renderedMessageIds.add(messageId);

        if (emptyState) {
            emptyState.remove();
        }

        const mine = Number(message.sender_id) === currentUserId;
        const wrapper = document.createElement('div');
        wrapper.className = `flex ${mine ? 'justify-end' : 'justify-start'}`;
        wrapper.dataset.messageId = String(messageId);

        const bubble = document.createElement('div');
        bubble.className = `max-w-xl rounded-2xl px-4 py-3 shadow-sm ${mine ? 'bg-slate-900 text-white' : 'bg-white text-slate-800'}`;
        bubble.innerHTML = `
            <p class="text-sm leading-6">${escapeHtml(message.body)}</p>
            <p class="mt-2 text-[11px] ${mine ? 'text-slate-300' : 'text-slate-400'}">
                ${escapeHtml(message.sender_name ?? 'Utilisateur')} - ${escapeHtml(message.created_at ?? '')}
            </p>
        `;

        wrapper.appendChild(bubble);
        messagesList.appendChild(wrapper);
        updatePreview(message.body);
        scrollToBottom();
    };

    const latestRenderedMessageId = () => {
        if (renderedMessageIds.size === 0) {
            return 0;
        }

        return Math.max(...renderedMessageIds);
    };

    const fetchMessages = async () => {
        if (!feedUrl) {
            return;
        }

        try {
            const response = await window.axios.get(feedUrl, {
                params: {
                    after: latestRenderedMessageId(),
                },
                headers: {
                    Accept: 'application/json',
                },
            });

            for (const message of response.data.messages ?? []) {
                appendMessage(message);
            }
        } catch (error) {
            // Silently ignore transient polling failures to keep the chat usable.
        }
    };

    scrollToBottom();

    if (window.Echo) {
        window.Echo.private(`conversations.${conversationId}`)
            .listen('.message.sent', (event) => {
                if (event?.message) {
                    appendMessage(event.message);
                }
            });
    }

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const body = textarea?.value.trim();

        if (!body || !form || !submitButton) {
            return;
        }

        submitButton.disabled = true;

        try {
            const response = await window.axios.post(form.action, { body }, {
                headers: {
                    Accept: 'application/json',
                },
            });

            appendMessage(response.data.message);
            textarea.value = '';
            textarea.focus();
        } catch (error) {
            form.submit();
        } finally {
            submitButton.disabled = false;
        }
    });

    window.setInterval(fetchMessages, 4000);
}
