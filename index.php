<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayClone - Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4ecfb 100%);
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #2c3e50;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 3rem 4rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 90%;
            width: 500px;
            transition: transform 0.3s ease;
        }
        
        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        .logo {
            margin-bottom: 2rem;
        }
        
        h1 {
            color: #169bd7;
            margin-bottom: 1.5rem;
            font-size: 2.5rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            letter-spacing: -0.5px;
        }
        
        p {
            color: #5f6368;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        a.btn {
            display: inline-block;
            padding: 0.8rem 1.8rem;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        a.btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.6s ease;
        }
        
        a.btn:hover::before {
            left: 100%;
        }
        
        a.btn-primary {
            background: linear-gradient(135deg, #0070ba 0%, #1546a0 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 112, 186, 0.3);
        }
        
        a.btn-primary:hover {
            background: linear-gradient(135deg, #0087e0 0%, #1857c8 100%);
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(0, 112, 186, 0.4);
        }
        
        a.btn-secondary {
            background: white;
            color: #0070ba;
            border: 2px solid #0070ba;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        a.btn-secondary:hover {
            background: #f8f9fa;
            color: #0087e0;
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }
        
        .footer {
            margin-top: 3rem;
            font-size: 0.8rem;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <svg width="120" height="30" viewBox="0 0 120 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 5H100" stroke="#169bd7" stroke-width="10" stroke-linecap="round"/>
                <path d="M20 15H100" stroke="#263b80" stroke-width="10" stroke-linecap="round"/>
                <path d="M20 25H100" stroke="#169bd7" stroke-width="10" stroke-linecap="round"/>
            </svg>
        </div>
        <h1>Welcome to PayClone</h1>
        <p>Send money, receive payments, and manage your finances with our secure platform.</p>
        <div class="buttons">
            <a href="signup.php" class="btn btn-primary">Sign Up</a>
            <a href="login.php" class="btn btn-secondary">Login</a>
        </div>
        <div class="footer">
            © 2025 PayClone. All rights reserved.
        </div>
    </div>
</body>
</html>
