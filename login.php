<?php
require 'db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error_message = "Invalid email or password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayClone - Login</title>
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
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            color: #2c3e50;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 450px;
            padding: 2.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
        }
        
        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }
        
        .logo {
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: center;
        }
        
        h2 {
            color: #169bd7;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: #4a5568;
        }
        
        input {
            display: block;
            width: 100%;
            padding: 0.8rem 1rem;
            font-size: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            background-color: #f8fafc;
            transition: all 0.3s ease;
            outline: none;
        }
        
        input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
            background-color: #fff;
        }
        
        input:hover {
            background-color: #fff;
            border-color: #cbd5e0;
        }
        
        button {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, #0070ba 0%, #1546a0 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            position: relative;
            overflow: hidden;
        }
        
        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.6s ease;
        }
        
        button:hover::before {
            left: 100%;
        }
        
        button:hover {
            background: linear-gradient(135deg, #0087e0 0%, #1857c8 100%);
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(0, 112, 186, 0.3);
        }
        
        .signup-link {
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #718096;
        }
        
        .signup-link a {
            color: #0070ba;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        
        .signup-link a:hover {
            color: #003087;
            text-decoration: underline;
        }
        
        .form-error {
            color: #e53e3e;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        
        .forgot-password {
            text-align: right;
            margin-top: 0.5rem;
        }
        
        .forgot-password a {
            color: #718096;
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .forgot-password a:hover {
            color: #0070ba;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <svg width="120" height="30" viewBox="0 0 120 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 5H100" stroke="#169bd7" stroke-width="10" stroke-linecap="round"/>
                <path d="M20 15H100" stroke="#263b80" stroke-width="10" stroke-linecap="round"/>
                <path d="M20 25H100" stroke="#169bd7" stroke-width="10" stroke-linecap="round"/>
            </svg>
        </div>
        <h2>Welcome Back</h2>
        
        <?php if (isset($error_message)): ?>
            <div class="form-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="example@email.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <div class="forgot-password">
                    <a href="forgot-password.php">Forgot password?</a>
                </div>
            </div>
            
            <button type="submit">Log In</button>
        </form>
        
        <div class="signup-link">
            Don't have an account? <a href="signup.php">Sign up</a>
        </div>
    </div>

    <script>
        // Email validation
        document.getElementById('email').addEventListener('blur', function(e) {
            const email = e.target.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                // If we wanted to show an error message inline, we could add it here
                e.target.style.borderColor = '#f56565';
            } else {
                e.target.style.borderColor = '#e2e8f0';
            }
        });
    </script>
</body>
</html>
