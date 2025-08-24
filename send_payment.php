<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipient_email = htmlspecialchars($_POST['recipient_email']);
    $amount = floatval($_POST['amount']);

    // Input validation
    if ($amount <= 0) {
        $error = "Invalid amount.";
    } elseif ($amount > $user['balance']) {
        $error = "Insufficient balance.";
    } else {
        // Check if recipient exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$recipient_email]);
        $recipient = $stmt->fetch();

        if (!$recipient) {
            $error = "Recipient not found.";
        } elseif ($recipient['id'] == $user_id) {
            $error = "You cannot send money to yourself.";
        } else {
            // Begin Transaction
            $pdo->beginTransaction();

            try {
                // Deduct from sender
                $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
                $stmt->execute([$amount, $user_id]);

                // Add to recipient
                $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                $stmt->execute([$amount, $recipient['id']]);

                // Record transaction
                $stmt = $pdo->prepare("INSERT INTO transactions (sender_id, receiver_id, amount) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $recipient['id'], $amount]);

                $pdo->commit();
                $success = "Payment of $$amount sent to " . $recipient['name'] . " successfully!";
                
                // Refresh balance
                $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user['balance'] = $stmt->fetchColumn();

            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Payment failed. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayClone - Send Payment</title>
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
        
        .main-container {
            width: 90%;
            max-width: 800px;
            margin: 2rem auto;
        }
        
        .payment-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            transition: transform 0.3s ease;
        }
        
        .payment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        .page-title i {
            color: #0070ba;
            font-size: 1.5rem;
        }
        
        .balance-display {
            background: linear-gradient(135deg, #0070ba 0%, #1546a0 100%);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .balance-amount {
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #4a5568;
        }
        
        .form-control {
            width: 100%;
            padding: 0.9rem 1rem;
            font-size: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .form-control:focus {
            outline: none;
            border-color: #0070ba;
            box-shadow: 0 0 0 3px rgba(0, 112, 186, 0.1);
        }
        
        .amount-input-group {
            position: relative;
        }
        
        .amount-input-group .currency-symbol {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #4a5568;
            font-weight: 600;
        }
        
        .amount-input-group .form-control {
            padding-left: 2rem;
        }
        
        .submit-btn {
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
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }
        
        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.6s ease;
        }
        
        .submit-btn:hover::before {
            left: 100%;
        }
        
        .submit-btn:hover {
            background: linear-gradient(135deg, #0087e0 0%, #1857c8 100%);
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(0, 112, 186, 0.3);
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            color: #0070ba;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            color: #004f83;
            transform: translateX(-3px);
        }
        
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .success {
            background: #e6f7ee;
            color: #0c6b39;
            border-left: 4px solid #10b981;
        }
        
        .error {
            background: #feeeed;
            color: #b91c1c;
            border-left: 4px solid #ef4444;
        }
        
        .recent-contacts {
            margin-top: 2rem;
        }
        
        .section-title {
            font-size: 1.2rem;
            color: #4a5568;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .contacts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 1rem;
        }
        
        .contact-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .contact-item:hover {
            transform: translateY(-3px);
        }
        
        .contact-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            color: #4a5568;
            font-weight: 600;
            font-size: 1.2rem;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .contact-item:hover .contact-avatar {
            border-color: #0070ba;
            color: #0070ba;
        }
        
        .contact-name {
            font-size: 0.9rem;
            color: #4a5568;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 1rem;
            }
            
            .main-container {
                width: 95%;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .balance-display {
                align-self: stretch;
                justify-content: center;
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
    
    <div class="main-container">
        <div class="payment-card">
            <div class="card-header">
                <h2 class="page-title">
                    <i class="fas fa-paper-plane"></i> Send Money
                </h2>
                <div class="balance-display">
                    <i class="fas fa-wallet"></i>
                    Balance: <span class="balance-amount">$<?= number_format($user['balance'], 2); ?></span>
                </div>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="message error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $error; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="message success">
                    <i class="fas fa-check-circle"></i>
                    <?= $success; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="send_payment.php">
                <div class="form-group">
                    <label for="recipient_email" class="form-label">Recipient's Email</label>
                    <div class="input-group">
                        <input type="email" id="recipient_email" name="recipient_email" class="form-control" placeholder="Enter recipient's email address" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="amount" class="form-label">Amount to Send</label>
                    <div class="amount-input-group">
                        <span class="currency-symbol">$</span>
                        <input type="number" step="0.01" min="0.01" id="amount" name="amount" class="form-control" placeholder="0.00" required>
                    </div>
                </div>
                
                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Send Payment
                </button>
            </form>
            
            <a href="dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <div class="recent-contacts">
            <h3 class="section-title">
                <i class="fas fa-user-friends"></i> Recent Contacts
            </h3>
            
            <div class="contacts-grid">
                <?php
                // This is just for UI demonstration - in a real app, you'd fetch real contacts
                $demoContacts = [
                    ['initial' => 'J', 'name' => 'John D.'],
                    ['initial' => 'S', 'name' => 'Sarah M.'],
                    ['initial' => 'A', 'name' => 'Alex W.'],
                    ['initial' => 'R', 'name' => 'Rebecca T.'],
                ];
                
                foreach($demoContacts as $contact):
                ?>
                <div class="contact-item" onclick="document.getElementById('recipient_email').value = '<?= strtolower($contact['name'][0]) . strtolower(explode(' ', $contact['name'])[1][0]) ?>@example.com'">
                    <div class="contact-avatar">
                        <?= $contact['initial'] ?>
                    </div>
                    <div class="contact-name"><?= $contact['name'] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        // Add fade-in animation for form elements
        document.addEventListener('DOMContentLoaded', function() {
            const formElements = document.querySelectorAll('.form-group, .submit-btn');
            formElements.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                item.style.transform = 'translateY(10px)';
                
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, 100 * index);
            });
            
            // Form validation enhancement
            const amountInput = document.getElementById('amount');
            amountInput.addEventListener('input', function() {
                const currentBalance = <?= $user['balance'] ?>;
                const enteredAmount = parseFloat(this.value);
                
                if (enteredAmount > currentBalance) {
                    this.style.borderColor = '#ef4444';
                    // Optional: Show warning message
                } else if (enteredAmount <= 0) {
                    this.style.borderColor = '#ef4444';
                } else {
                    this.style.borderColor = '#10b981';
                }
            });
        });
    </script>
</body>
</html>
