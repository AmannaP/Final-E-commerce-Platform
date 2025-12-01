<?php
require_once '../settings/core.php';
require_once '../controllers/chat_controller.php';

if (!checkLogin()) header("Location: ../login/login.php");

$group_id = $_GET['id'] ?? null;
$group = get_group_details_ctr($group_id);

if (!$group) {
    header("Location: chat.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($group['group_name']) ?> | GBVAid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background-color: #f0f2f5; height: 100vh; display: flex; flex-direction: column; }
        
        .chat-header {
            background-color: white;
            padding: 15px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .chat-container {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        /* Message Bubbles */
        .message {
            max-width: 75%;
            padding: 10px 15px;
            border-radius: 15px;
            position: relative;
            font-size: 0.95rem;
        }
        
        .message.received {
            align-self: flex-start;
            background-color: white;
            border-bottom-left-radius: 2px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .message.sent {
            align-self: flex-end;
            background-color: #c453eaff;
            color: white;
            border-bottom-right-radius: 2px;
            box-shadow: 0 1px 2px rgba(196, 83, 234, 0.2);
        }
        
        .msg-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            margin-bottom: 4px;
            opacity: 0.8;
        }
        
        .message.sent .msg-info { color: #f3e8ff; }
        .message.received .msg-info { color: #888; }
        
        /* Input Area */
        .chat-input-area {
            background-color: white;
            padding: 15px;
            border-top: 1px solid #ddd;
        }
        
        .send-btn {
            background-color: #c453eaff;
            color: white;
            border-radius: 50%;
            width: 45px; height: 45px;
            display: flex; align-items: center; justify-content: center;
            border: none;
            transition: background 0.3s;
        }
        .send-btn:hover { background-color: #a020f0; color: white; }
        .send-btn:disabled { background-color: #ccc; cursor: not-allowed; }
    </style>
</head>
<body>

<div class="chat-header">
    <div class="d-flex align-items-center">
        <a href="chat.php" class="btn btn-light btn-sm me-3 rounded-circle"><i class="bi bi-arrow-left"></i></a>
        <div>
            <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($group['group_name']) ?></h5>
            <small class="text-muted">Live Community Chat</small>
        </div>
    </div>
    <a href="report_incident.php" class="btn btn-outline-danger btn-sm rounded-pill">Report Incident</a>
</div>

<div class="chat-container" id="chatBox">
    <div class="text-center text-muted my-4 small">
        <span class="bg-light px-3 py-1 rounded-pill">Welcome to the safe space. Be kind. 💜</span>
    </div>
    <div class="text-center mt-5">
        <div class="spinner-border text-secondary spinner-border-sm" role="status"></div>
    </div>
</div>

<div class="chat-input-area">
    <form id="chatForm" class="d-flex align-items-center gap-2">
        <input type="hidden" name="group_id" value="<?= $group['group_id'] ?>">
        
        <input type="text" id="messageInput" name="message" class="form-control rounded-pill py-2 px-4" placeholder="Type a message..." autocomplete="off">
        <button type="submit" class="send-btn"><i class="bi bi-send-fill"></i></button>
    </form>
</div>

<script src="../js/chat.js"></script>

</body>
</html>