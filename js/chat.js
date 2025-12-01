// js/chat.js

$(document).ready(function() {
    // 1. Get Group ID from the hidden input field in the HTML
    const chatBox = $('#chatBox');
    const groupId = $('input[name="group_id"]').val(); 
    let autoScroll = true;

    // Validate Group ID
    if (!groupId) {
        console.error("Group ID not found.");
        return;
    }

    // 2. Function to Fetch Messages
    function fetchMessages() {
        $.ajax({
            url: '../actions/fetch_chat_action.php',
            type: 'GET',
            data: { group_id: groupId },
            success: function(data) {
                chatBox.html(data); // Insert HTML into chat box
                if(autoScroll) {
                    scrollToBottom();
                }
            }
        });
    }

    // 3. Scroll to bottom helper
    function scrollToBottom() {
        chatBox.scrollTop(chatBox[0].scrollHeight);
    }

    // 4. Send Message
    $('#chatForm').on('submit', function(e) {
        e.preventDefault();
        const msgInput = $('#messageInput');
        const msg = msgInput.val().trim();
        
        if(msg === "") return;

        // Disable button temporarily
        const btn = $(this).find('button');
        btn.prop('disabled', true);

        $.ajax({
            url: '../actions/send_chat_action.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if(res === 'success') {
                    msgInput.val(''); // Clear input
                    fetchMessages(); // Refresh immediately
                    autoScroll = true; // Force scroll to bottom for new message
                }
            },
            complete: function() {
                btn.prop('disabled', false);
                msgInput.focus();
            }
        });
    });

    // 5. Initial Load & Polling
    fetchMessages(); // Run once on load
    
    // Poll every 2 seconds
    setInterval(fetchMessages, 2000); 

    // 6. Smart Scroll Detection
    // Stop auto-scrolling if the user manually scrolls up to read history
    chatBox.on('scroll', function() {
        // If user is near the bottom (within 50px), enable auto-scroll
        if(chatBox.scrollTop() + chatBox.innerHeight() >= chatBox[0].scrollHeight - 50) {
            autoScroll = true;
        } else {
            autoScroll = false;
        }
    });
});