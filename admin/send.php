<?php
require 'db.php';

$ticket_id = (int)$_POST['ticket_id'];
$message = $_POST['message'];

$stmt = $pdo->prepare("INSERT INTO messages (ticket_id, sender, message) VALUES (?, 'admin', ?)");
$stmt->execute([$ticket_id, $message]);

$stmt = $pdo->prepare("SELECT chat_id FROM tickets WHERE id=?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if ($ticket) {

    $token = "8991681537:AAH-Cz9qXCosWOAmmbFtMyp6cXwhiow5AaA";

    file_get_contents(
        "https://api.telegram.org/bot{$token}/sendMessage?" .
        http_build_query([
            "chat_id" => $ticket["chat_id"],
            "text" => $message
        ])
    );
}

header("Location: ticket.php?id=$ticket_id");
exit;