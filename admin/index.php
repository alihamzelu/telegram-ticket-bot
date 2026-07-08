<?php
require 'db.php';

$tickets = $pdo->query("
    SELECT t.*, u.name, u.username
    FROM tickets t
    JOIN users u ON t.user_id = u.telegram_id
    ORDER BY t.id DESC
")->fetchAll();

$total_tickets = count($tickets);
$open_tickets = count(array_filter($tickets, fn($t) => $t['status'] === 'open'));
$closed_tickets = count(array_filter($tickets, fn($t) => $t['status'] === 'closed'));
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Panel</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dark: '#0f172a',
                        darker: '#020617',
                        accent: '#3b82f6'
                    }
                }
            }
        }
    </script>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            background:#0f172a;
            color:#e2e8f0;
            font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;
        }

        .sidebar{
            background:linear-gradient(180deg,#1e293b 0%,#0f172a 100%);
            border-right:1px solid #334155;
        }

        .ticket-card{
            background:#1e293b;
            border:1px solid #334155;
            transition:.3s;
        }

        .ticket-card:hover{
            border-color:#3b82f6;
            transform:translateY(-2px);
        }

        .badge{
            padding:.25rem .75rem;
            border-radius:9999px;
            font-size:.875rem;
            font-weight:600;
        }

        .badge-open{
            background:#065f46;
            color:#10b981;
        }

        .badge-closed{
            background:#7c2d12;
            color:#f97316;
        }

        .nav-item{
            padding:.75rem 1rem;
            border-radius:.5rem;
            cursor:pointer;
            transition:.2s;
        }

        .nav-item:hover{
            background:#334155;
        }

        .nav-item.active{
            background:#3b82f6;
        }
    </style>
</head>

<body class="flex">

<div class="sidebar w-64 min-h-screen p-6 sticky top-0">

    <div class="mb-8">
        <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-blue-600 bg-clip-text text-transparent">
            Support Panel
        </h1>

        <p class="text-sm text-gray-400 mt-1">
            Ticket Management
        </p>
    </div>

    <div class="space-y-3 mb-8">

        <div class="bg-blue-500 bg-opacity-10 border border-blue-500 border-opacity-30 rounded-lg p-4">
            <p class="text-gray-400 text-sm">Total Tickets</p>
            <p class="text-3xl font-bold text-blue-400"><?= $total_tickets ?></p>
        </div>

        <div class="bg-green-500 bg-opacity-10 border border-green-500 border-opacity-30 rounded-lg p-4">
            <p class="text-gray-400 text-sm">Open Tickets</p>
            <p class="text-3xl font-bold text-green-400"><?= $open_tickets ?></p>
        </div>

        <div class="bg-orange-500 bg-opacity-10 border border-orange-500 border-opacity-30 rounded-lg p-4">
            <p class="text-gray-400 text-sm">Closed Tickets</p>
            <p class="text-3xl font-bold text-orange-400"><?= $closed_tickets ?></p>
        </div>

    </div>

    <nav class="space-y-2">
        <div class="nav-item active">📊 Dashboard</div>

    </nav>

</div>

<div class="flex-1 p-8">

    <div class="mb-6">
        <h2 class="text-3xl font-bold">Support Tickets</h2>
        <p class="text-gray-400 mt-2">
            Manage and respond to customer support requests.
        </p>
    </div>

    <div class="grid gap-4">

        <?php if(empty($tickets)): ?>

            <div class="rounded-lg p-8 text-center">
                <p class="text-gray-400">
                    No tickets found.
                </p>
            </div>

        <?php else: ?>

            <?php foreach($tickets as $ticket): ?>

                <a href="ticket.php?id=<?= $ticket['id'] ?>" class="ticket-card rounded-lg p-6 block">

                    <div class="flex justify-between items-start mb-4">

                        <div>
                            <h3 class="text-lg font-semibold">
                                Ticket #<?= $ticket['id'] ?>
                            </h3>

                            <p class="text-sm text-gray-400 mt-1">
                                From:
                                <strong><?= htmlspecialchars($ticket['name']) ?></strong>
                                @<?= htmlspecialchars($ticket['username']) ?>
                            </p>
                        </div>

                        <span class="badge badge-<?= $ticket['status']=="open" ? "open":"closed" ?>">
                            <?= $ticket['status']=="open" ? "🟢 Open":"🔴 Closed" ?>
                        </span>

                    </div>

                    <p class="text-gray-300">
                        <?= htmlspecialchars(substr($ticket['message'],0,150)) ?>...
                    </p>

                    <p class="text-xs text-gray-500 mt-3">
                        📅 <?= date('Y-m-d H:i',strtotime($ticket['created_at'])) ?>
                    </p>

                </a>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>

</body>
</html>