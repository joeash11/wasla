<?php require_once __DIR__ . '/includes/client_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Messages</title>
    <meta name="description" content="View and send messages on Wasla.">
    <link rel="stylesheet" href="styles.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="images/wasla-icon.png">
    <script src="wasla-theme.js"></script>
</head>
<body>
    <?php $active_page = 'messages'; ?>
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="main-wrapper">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="content" id="main-content">
            <h1 class="section-title">Messages</h1>
            <div class="messages-container">
                <!-- Conversation List -->
                <div class="conversation-list" id="conversation-list">
                    <div class="conversation-search">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search conversations..." id="conv-search">
                    </div>
                    <div class="conversation-item active" data-conv="0" onclick="selectConversation(0)">
                        <div class="conv-avatar"><i class="fas fa-user-circle"></i></div>
                        <div class="conv-info">
                            <h4 class="conv-name">Omar Hassan</h4>
                            <p class="conv-preview">Sure, I'll be there at 9 AM</p>
                        </div>
                        <div class="conv-meta">
                            <span class="conv-time">2m ago</span>
                            <span class="conv-badge">2</span>
                        </div>
                    </div>
                    <div class="conversation-item" data-conv="1" onclick="selectConversation(1)">
                        <div class="conv-avatar conv-avatar-purple"><i class="fas fa-user-circle"></i></div>
                        <div class="conv-info">
                            <h4 class="conv-name">Sara Ahmed</h4>
                            <p class="conv-preview">The venue looks great!</p>
                        </div>
                        <div class="conv-meta">
                            <span class="conv-time">1h ago</span>
                        </div>
                    </div>
                    <div class="conversation-item" data-conv="2" onclick="selectConversation(2)">
                        <div class="conv-avatar conv-avatar-green"><i class="fas fa-user-circle"></i></div>
                        <div class="conv-info">
                            <h4 class="conv-name">Khalid Al-Farsi</h4>
                            <p class="conv-preview">Can we schedule a meeting?</p>
                        </div>
                        <div class="conv-meta">
                            <span class="conv-time">3h ago</span>
                        </div>
                    </div>
                    <div class="conversation-item" data-conv="3" onclick="selectConversation(3)">
                        <div class="conv-avatar conv-avatar-orange"><i class="fas fa-user-circle"></i></div>
                        <div class="conv-info">
                            <h4 class="conv-name">Fatima Zahra</h4>
                            <p class="conv-preview">Thanks for the update!</p>
                        </div>
                        <div class="conv-meta">
                            <span class="conv-time">Yesterday</span>
                        </div>
                    </div>
                    <div class="conversation-item" data-conv="4" onclick="selectConversation(4)">
                        <div class="conv-avatar"><i class="fas fa-user-circle"></i></div>
                        <div class="conv-info">
                            <h4 class="conv-name">Wasla Support</h4>
                            <p class="conv-preview">Your ticket has been resolved</p>
                        </div>
                        <div class="conv-meta">
                            <span class="conv-time">2d ago</span>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="chat-area" id="chat-area">
                    <div class="chat-header">
                        <div class="chat-header-user">
                            <div class="conv-avatar"><i class="fas fa-user-circle"></i></div>
                            <div>
                                <h3 class="chat-header-name" id="chat-name">Omar Hassan</h3>
                                <span class="chat-header-status"><i class="fas fa-circle"></i> Online</span>
                            </div>
                        </div>
                        <div class="chat-header-actions">
                            <button class="chat-action-btn"><i class="fas fa-phone"></i></button>
                            <button class="chat-action-btn"><i class="fas fa-video"></i></button>
                            <button class="chat-action-btn"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>
                    <div class="chat-messages" id="chat-messages">
                        <div class="chat-date-divider"><span>Today</span></div>
                        <div class="chat-msg chat-msg-received">
                            <div class="chat-msg-bubble">
                                <p>Hey Abdullah! Are we still on for the Gaming Festival setup tomorrow?</p>
                                <span class="chat-msg-time">9:30 AM</span>
                            </div>
                        </div>
                        <div class="chat-msg chat-msg-sent">
                            <div class="chat-msg-bubble">
                                <p>Yes! Everything is confirmed. We need to be at the venue by 8 AM.</p>
                                <span class="chat-msg-time">9:32 AM</span>
                            </div>
                        </div>
                        <div class="chat-msg chat-msg-received">
                            <div class="chat-msg-bubble">
                                <p>Great, I'll coordinate with the other ushers. How many are we expecting?</p>
                                <span class="chat-msg-time">9:35 AM</span>
                            </div>
                        </div>
                        <div class="chat-msg chat-msg-sent">
                            <div class="chat-msg-bubble">
                                <p>We have 3 ushers confirmed and 2 more pending. I'll send you the full roster by tonight.</p>
                                <span class="chat-msg-time">9:38 AM</span>
                            </div>
                        </div>
                        <div class="chat-msg chat-msg-received">
                            <div class="chat-msg-bubble">
                                <p>Sure, I'll be there at 9 AM</p>
                                <span class="chat-msg-time">9:41 AM</span>
                            </div>
                        </div>
                    </div>
                    <div class="chat-input-area">
                        <button class="chat-attach-btn"><i class="fas fa-paperclip"></i></button>
                        <input type="text" class="chat-input" placeholder="Type a message..." id="chat-input">
                        <button class="chat-send-btn" id="chat-send-btn"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const CURRENT_USER_ID = 2; // Abdullah Elsayed
        let currentPartnerId = null;
        let conversations = [];

        // ===== LOAD CONVERSATIONS FROM BACKEND =====
        async function loadConversations() {
            try {
                const res = await fetch(`db/get_conversations.php?user_id=${CURRENT_USER_ID}`);
                const data = await res.json();
                if (data.error) throw new Error(data.error);
                conversations = data;
                renderConversations(data);
                if (data.length > 0) {
                    selectConversation(data[0].partner_id);
                }
            } catch (err) {
                console.log('Backend not available, using static data:', err.message);
                // Fallback to static conversations
                useFallbackData();
            }
        }

        // ===== RENDER CONVERSATIONS LIST =====
        function renderConversations(convos) {
            const list = document.getElementById('conversation-list');
            const searchHTML = list.querySelector('.conversation-search').outerHTML;
            list.innerHTML = searchHTML;

            convos.forEach((conv, i) => {
                const colors = ['', 'conv-avatar-purple', 'conv-avatar-green', 'conv-avatar-orange', ''];
                const div = document.createElement('div');
                div.className = 'conversation-item' + (i === 0 ? ' active' : '');
                div.setAttribute('data-partner', conv.partner_id);
                div.onclick = () => selectConversation(conv.partner_id);

                const timeAgo = formatTimeAgo(conv.last_time);
                const badgeHTML = conv.unread_count > 0 ? `<span class="conv-badge">${conv.unread_count}</span>` : '';

                div.innerHTML = `
                    <div class="conv-avatar ${colors[i % 5]}"><i class="fas fa-user-circle"></i></div>
                    <div class="conv-info">
                        <h4 class="conv-name">${conv.name}</h4>
                        <p class="conv-preview">${truncate(conv.last_message, 30)}</p>
                    </div>
                    <div class="conv-meta">
                        <span class="conv-time">${timeAgo}</span>
                        ${badgeHTML}
                    </div>
                `;
                list.appendChild(div);
            });

            // Re-attach search
            setupConvSearch();
        }

        // ===== SELECT CONVERSATION =====
        async function selectConversation(partnerId) {
            currentPartnerId = partnerId;

            // Update active state
            document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('active'));
            const activeItem = document.querySelector(`[data-partner="${partnerId}"]`);
            if (activeItem) activeItem.classList.add('active');

            // Find partner name
            const conv = conversations.find(c => c.partner_id == partnerId);
            if (conv) {
                document.getElementById('chat-name').textContent = conv.name;
            }

            // Load messages
            try {
                const res = await fetch(`db/get_messages.php?user_id=${CURRENT_USER_ID}&partner_id=${partnerId}`);
                const messages = await res.json();
                if (messages.error) throw new Error(messages.error);
                renderMessages(messages);
            } catch (err) {
                console.log('Could not load messages:', err.message);
            }
        }

        // ===== RENDER MESSAGES =====
        function renderMessages(messages) {
            const container = document.getElementById('chat-messages');
            container.innerHTML = '<div class="chat-date-divider"><span>Messages</span></div>';

            let lastDate = '';
            messages.forEach(msg => {
                const msgDate = new Date(msg.sent_at).toLocaleDateString();
                if (msgDate !== lastDate) {
                    lastDate = msgDate;
                    const isToday = msgDate === new Date().toLocaleDateString();
                    const dateLabel = isToday ? 'Today' : msgDate;
                    if (messages.indexOf(msg) > 0) {
                        container.innerHTML += `<div class="chat-date-divider"><span>${dateLabel}</span></div>`;
                    }
                }

                const time = new Date(msg.sent_at).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
                const cls = msg.is_mine ? 'chat-msg-sent' : 'chat-msg-received';
                const div = document.createElement('div');
                div.className = `chat-msg ${cls}`;
                div.innerHTML = `<div class="chat-msg-bubble"><p>${escapeHTML(msg.message)}</p><span class="chat-msg-time">${time}</span></div>`;
                container.appendChild(div);
            });

            container.scrollTop = container.scrollHeight;
        }

        // ===== SEND MESSAGE =====
        async function sendMessage() {
            const input = document.getElementById('chat-input');
            const text = input.value.trim();
            if (!text || !currentPartnerId) return;

            // Show message immediately (optimistic)
            const container = document.getElementById('chat-messages');
            const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
            const div = document.createElement('div');
            div.className = 'chat-msg chat-msg-sent';
            div.innerHTML = `<div class="chat-msg-bubble"><p>${escapeHTML(text)}</p><span class="chat-msg-time">${time}</span></div>`;
            container.appendChild(div);
            input.value = '';
            container.scrollTop = container.scrollHeight;

            // Animate in
            div.style.opacity = '0';
            div.style.transform = 'translateY(10px)';
            requestAnimationFrame(() => {
                div.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                div.style.opacity = '1';
                div.style.transform = 'translateY(0)';
            });

            // Send to backend
            try {
                await fetch('db/send_message.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        sender_id: CURRENT_USER_ID,
                        receiver_id: currentPartnerId,
                        message: text
                    })
                });
            } catch (err) {
                console.log('Message saved locally only:', err.message);
            }
        }

        // ===== FALLBACK STATIC DATA =====
        function useFallbackData() {
            const staticConvos = [
                { partner_id: 3, name: 'Omar Hassan', last_message: "Sure, I'll be there at 9 AM", last_time: new Date().toISOString(), unread_count: 2 },
                { partner_id: 4, name: 'Sara Ahmed', last_message: 'The venue looks great!', last_time: new Date(Date.now()-3600000).toISOString(), unread_count: 0 },
                { partner_id: 5, name: 'Khalid Al-Farsi', last_message: 'Can we schedule a meeting?', last_time: new Date(Date.now()-10800000).toISOString(), unread_count: 0 },
            ];
            conversations = staticConvos;
            renderConversations(staticConvos);

            // Select first conversation with static messages
            currentPartnerId = 3;
            document.getElementById('chat-name').textContent = 'Omar Hassan';
        }

        // ===== HELPERS =====
        function formatTimeAgo(dateStr) {
            const now = new Date();
            const date = new Date(dateStr);
            const diffMs = now - date;
            const diffMin = Math.floor(diffMs / 60000);
            if (diffMin < 1) return 'Just now';
            if (diffMin < 60) return diffMin + 'm ago';
            const diffHr = Math.floor(diffMin / 60);
            if (diffHr < 24) return diffHr + 'h ago';
            const diffDays = Math.floor(diffHr / 24);
            if (diffDays === 1) return 'Yesterday';
            return diffDays + 'd ago';
        }

        function truncate(str, len) {
            return str && str.length > len ? str.substring(0, len) + '...' : (str || '');
        }

        function escapeHTML(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function setupConvSearch() {
            const searchInput = document.getElementById('conv-search');
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    const q = e.target.value.toLowerCase();
                    document.querySelectorAll('.conversation-item').forEach(item => {
                        const name = item.querySelector('.conv-name')?.textContent.toLowerCase() || '';
                        item.style.display = name.includes(q) ? '' : 'none';
                    });
                });
            }
        }

        // ===== INIT =====
        document.addEventListener('DOMContentLoaded', () => {
            loadConversations();

            const input = document.getElementById('chat-input');
            const sendBtn = document.getElementById('chat-send-btn');
            sendBtn.addEventListener('click', sendMessage);
            input.addEventListener('keypress', e => { if (e.key === 'Enter') sendMessage(); });
        });
    </script>
</body>
</html>
