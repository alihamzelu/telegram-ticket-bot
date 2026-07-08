<?php
require 'db.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT t.*, u.name, u.username
    FROM tickets t
    JOIN users u ON t.user_id = u.telegram_id
    WHERE t.id = ?
");
$stmt->execute([$id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die("Ticket not found.");
}

$stmt = $pdo->prepare("
    SELECT *
    FROM messages
    WHERE ticket_id = ?
    ORDER BY id ASC
");
$stmt->execute([$id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {

    $reply = trim($_POST['message']);

    $stmt = $pdo->prepare("
        INSERT INTO messages (ticket_id, sender, message)
        VALUES (?, 'admin', ?)
    ");
    $stmt->execute([$id, $reply]);

    $stmt = $pdo->prepare("
        SELECT u.telegram_id
        FROM tickets t
        JOIN users u ON t.user_id = u.telegram_id
        WHERE t.id = ?
    ");
    $stmt->execute([$id]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        $token = "YOUR_BOT_TOKEN";

        $text = "💬 *New Support Reply*\n\n"
              . $reply
              . "\n\n━━━━━━━━━━━━━━\n"
              . "🙏 Thank you for your patience!";

        file_get_contents(
            "https://api.telegram.org/bot{$token}/sendMessage?" .
            http_build_query([
                "chat_id" => $user["telegram_id"],
                "text" => $text,
                "parse_mode" => "Markdown"
            ])
        );
    }

    header("Location: ticket.php?id=".$id);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {

    $status = $_POST['status'];

    $stmt = $pdo->prepare("
        UPDATE tickets
        SET status = ?
        WHERE id = ?
    ");
    $stmt->execute([$status, $id]);

    $stmt = $pdo->prepare("
        SELECT u.telegram_id
        FROM tickets t
        JOIN users u ON t.user_id = u.telegram_id
        WHERE t.id = ?
    ");
    $stmt->execute([$id]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        $token = "YOUR_BOT_TOKEN";

        if ($status === "closed") {

            $text = "✅ *Ticket Closed*\n\n"
                  . "Your support ticket has been marked as resolved.\n\n"
                  . "🙏 Thank you for contacting us.\n"
                  . "If you need anything else, feel free to create a new ticket anytime.";

        } else {

            $text = "🔓 *Ticket Reopened*\n\n"
                  . "Your support ticket has been reopened.\n\n"
                  . "👨‍💻 Our support team will continue reviewing your request.";
        }

        file_get_contents(
            "https://api.telegram.org/bot{$token}/sendMessage?" .
            http_build_query([
                "chat_id" => $user["telegram_id"],
                "text" => $text,
                "parse_mode" => "Markdown"
            ])
        );
    }

    header("Location: ticket.php?id=".$id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?= $id ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #0f172a;
            color: #e2e8f0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .message-box {
            background: #1e293b;
            border-left: 3px solid #3b82f6;
        }

        .message-box.user {
            border-left-color: #10b981;
            background: #064e3b;
        }

        .message-box.admin {
            border-left-color: #f59e0b;
            background: #78350f;
        }
    </style>
</head>

<body class="p-8">
<div class="max-w-4xl mx-auto">

    <div class="mb-8">
        <a href="index.php" class="text-blue-400 hover:text-blue-300 mb-4 inline-block">← Back to Tickets</a>

        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold">Ticket #<?= $ticket['id'] ?></h1>
                <p class="text-gray-400 mt-2">
                    From:
                    <strong><?= htmlspecialchars($ticket['name']) ?></strong>
                </p>
            </div>

            <form method="POST" class="inline">
                <select
                    name="status"
                    onchange="this.form.submit()"
                    class="bg-slate-700 border border-slate-600 rounded px-4 py-2 text-white">

                    <option value="open" <?= $ticket['status']=='open'?'selected':'' ?>>
                        🔓 Open
                    </option>

                    <option value="closed" <?= $ticket['status']=='closed'?'selected':'' ?>>
                        ✅ Closed
                    </option>

                </select>
            </form>
        </div>
    </div>

    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6 mb-8">
        <h3 class="text-sm font-semibold text-gray-400 mb-2">
            📌 Original Ticket
        </h3>

        <p class="text-white leading-relaxed">
            <?= htmlspecialchars($ticket['message']) ?>
        </p>

        <p class="text-xs text-gray-500 mt-4">
            📅 <?= date('Y-m-d H:i', strtotime($ticket['created_at'])) ?>
        </p>
    </div>

    <div class="bg-slate-900 border border-slate-700 rounded-lg p-6 mb-8 max-h-96 overflow-y-auto">

        <h3 class="font-semibold mb-4">
            💬 Conversation
        </h3>

        <div class="space-y-4">

            <?php foreach ($messages as $msg): ?>

                <div class="message-box <?= $msg['sender'] ?> rounded p-4">

                    <p class="text-sm font-semibold mb-2">

                        <?= $msg['sender']=='admin'
                            ? '👨‍💼 Admin'
                            : '👤 User' ?>

                    </p>

                    <p><?= htmlspecialchars($msg['message']) ?></p>

                    <p class="text-xs text-gray-400 mt-2">
                        ⏰ <?= date('H:i', strtotime($msg['created_at'])) ?>
                    </p>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

    <form method="POST" class="bg-slate-800 border border-slate-700 rounded-lg p-6">

        <label class="block text-sm font-semibold mb-3">
            ✉️ Reply
        </label>

        <textarea
            name="message"
            placeholder="Type your reply..."
            required
            rows="4"
            class="w-full bg-slate-700 border border-slate-600 rounded px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4"></textarea>

        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition">

            📤 Send Reply

        </button>

    </form>

</div>
</body>
</html>