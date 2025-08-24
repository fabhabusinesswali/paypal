<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$transactions = $pdo->prepare("SELECT * FROM transactions WHERE sender_id = ? OR receiver_id = ? ORDER BY created_at DESC LIMIT 10");
$transactions->execute([$user_id, $user_id]);
$history = $transactions->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayClone - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4ecfb 100%);
            min-height: 100vh;
            color: #2c3e50;
            padding-bottom: 30px;
        }
        
        .header {
            background: #fff;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo svg {
            margin-right: 10px;
        }
        
        .logo-text {
            font-size: 1.5rem;
            font-weight: 600;
            color: #169bd7;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-greeting {
            font-weight: 500;
            color: #4a5568;
        }
        
        .logout-btn {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            color: #4a5568;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: #edf2f7;
            color: #2d3748;
        }
        
        .dashboard-container {
            width: 90%;
            max-width: 1200px;
            margin: 2rem auto;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
        }
        
        @media (max-width: 1024px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }
        }
        
        .left-panel, .right-panel {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            transition: transform 0.3s ease;
        }
        
        .left-panel:hover, .right-panel:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        }
        
        .balance-card {
            background: linear-gradient(135deg, #0070ba 0%, #1546a0 100%);
            border-radius: 12px;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .balance-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transform: rotate(30deg);
        }
        
        .balance-label {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 0.5rem;
        }
        
        .balance-amount {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .currency {
            font-size: 1.5rem;
            margin-right: 0.5rem;
        }
        
        .card-number {
            font-size: 0.9rem;
            letter-spacing: 2px;
            opacity: 0.8;
            margin-top: 1rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            color: #2d3748;
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .transaction-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #edf2f7;
        }
        
        .transaction-item:last-child {
            border-bottom: none;
        }
        
        .transaction-details {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .transaction-icon {
            background: #ebf8ff;
            color: #0070ba;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .transaction-icon.outgoing {
            background: #fff5f5;
            color: #e53e3e;
        }
        
        .transaction-icon.incoming {
            background: #f0fff4;
            color: #38a169;
        }
        
        .transaction-info h4 {
            color: #2d3748;
            font-weight: 500;
            margin-bottom: 0.2rem;
        }
        
        .transaction-date {
            font-size: 0.85rem;
            color: #718096;
        }
        
        .transaction-amount {
            font-weight: 600;
        }
        
        .transaction-amount.outgoing {
            color: #e53e3e;
        }
        
        .transaction-amount.incoming {
            color: #38a169;
        }
        
        .no-transactions {
            text-align: center;
            padding: 2rem;
            color: #718096;
            font-style: italic;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: #f8fafc;
            border-radius: 10px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
        }
        
        .stat-title {
            font-size: 0.9rem;
            color: #718096;
            margin-bottom: 0.5rem;
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2d3748;
        }
        
        .send-payment-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #0070ba 0%, #1546a0 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            display: block;
            margin: 1.5rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .send-payment-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.6s ease;
        }
        
        .send-payment-btn:hover::before {
            left: 100%;
        }
        
        .send-payment-btn:hover {
            background: linear-gradient(135deg, #0087e0 0%, #1857c8 100%);
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(0, 112, 186, 0.3);
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 1rem;
            }
            
            .dashboard-container {
                width: 95%;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .balance-amount {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <svg width="40" height="30" viewBox="0 0 120 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 5H100" stroke="#169bd7" stroke-width="10" stroke-linecap="round"/>
                <path d="M20 15H100" stroke="#263b80" stroke-width="10" stroke-linecap="round"/>
                <path d="M20 25H100" stroke="#169bd7" stroke-width="10" stroke-linecap="round"/>
            </svg>
            <span class="logo-text">PayClone</span>
        </div>
        <div class="user-menu">
            <span class="user-greeting">Hello, <?= htmlspecialchars($user['name']); ?></span>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </header>
    
    <div class="dashboard-container">
        <div class="left-panel">
            <div class="balance-card">
                <div class="balance-label">Current Balance</div>
                <div class="balance-amount">
                    <span class="currency">$</span>
                    <?= number_format($user['balance'], 2); ?>
                </div>
                <div class="card-number">
                    Account: •••• <?= substr($user_id . str_repeat('0', 4), 0, 4); ?>
                </div>
            </div>
            
            <a href="send_payment.php" class="send-payment-btn">
                <i class="fas fa-paper-plane"></i> Send Money
            </a>
        </div>
        
        <div class="right-panel">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-title">This Month's Spending</div>
                    <div class="stat-value">$<?= number_format(rand(50, $user['balance']/2), 2); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Pending Payments</div>
                    <div class="stat-value"><?= rand(0, 3); ?></div>
                </div>
            </div>
            
            <h3 class="section-title">
                Transaction History
            </h3>
            
            <?php if (count($history) > 0): ?>
                <?php foreach ($history as $txn): 
                    $isOutgoing = $txn['sender_id'] == $user_id;
                    $transactionType = $isOutgoing ? 'outgoing' : 'incoming';
                    $transactionTitle = $isOutgoing ? 'Payment Sent' : 'Payment Received';
                    $transactionIcon = $isOutgoing ? 'fa-arrow-up' : 'fa-arrow-down';
                    $otherPartyId = $isOutgoing ? $txn['receiver_id'] : $txn['sender_id'];
                    
                    // Get other party name (simplified - in production you'd join tables or similar)
                    $otherNameStmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
                    $otherNameStmt->execute([$otherPartyId]);
                    $otherParty = $otherNameStmt->fetch();
                    $otherPartyName = $otherParty ? $otherParty['name'] : 'Unknown User';
                    
                    $formattedDate = date('M d, Y h:i A', strtotime($txn['created_at']));
                ?>
                <div class="transaction-item">
                    <div class="transaction-details">
                        <div class="transaction-icon <?= $transactionType ?>">
                            <i class="fas <?= $transactionIcon ?>"></i>
                        </div>
                        <div class="transaction-info">
                            <h4><?= $transactionTitle ?></h4>
                            <div class="transaction-date">
                                <?= $formattedDate ?> • 
                                <?= $isOutgoing ? 'To: ' : 'From: ' ?><?= htmlspecialchars($otherPartyName) ?>
                            </div>
                        </div>
                    </div>
                    <div class="transaction-amount <?= $transactionType ?>">
                        <?= $isOutgoing ? '-' : '+' ?>$<?= number_format($txn['amount'], 2) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-transactions">
                    <p>No transactions yet. Start sending or receiving payments!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Optional JS for interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Add fade-in animation for transactions
            const transactions = document.querySelectorAll('.transaction-item');
            transactions.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                item.style.transform = 'translateY(10px)';
                
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        });
    </script>
</body>
</html>
