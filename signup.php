<?php
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$name, $email, $password])) {
        header('Location: login.php');
        exit;
    } else {
        echo "Error creating account!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayClone - Sign Up</title>
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
        
        .signup-container {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 450px;
            padding: 2.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
        }
        
        .signup-container:hover {
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
        
        .login-link {
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #718096;
        }
        
        .login-link a {
            color: #0070ba;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        
        .login-link a:hover {
            color: #003087;
            text-decoration: underline;
        }

        .password-strength {
            height: 5px;
            background: #e2e8f0;
            margin-top: 5px;
            border-radius: 2px;
            overflow: hidden;
            position: relative;
        }

        .password-strength-meter {
            height: 100%;
            width: 0%;
            border-radius: 2px;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        
        .form-error {
            color: #e53e3e;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: none;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="logo">
            <svg width="120" height="30" viewBox="0 0 120 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 5H100" stroke="#169bd7" stroke-width="10" stroke-linecap="round"/>
                <path d="M20 15H100" stroke="#263b80" stroke-width="10" stroke-linecap="round"/>
                <path d="M20 25H100" stroke="#169bd7" stroke-width="10" stroke-linecap="round"/>
            </svg>
        </div>
        <h2>Create Your Account</h2>
        
        <form method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="example@email.com" required>
                <div class="form-error" id="email-error">Please enter a valid email address</div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a secure password" required>
                <div class="password-strength">
                    <div class="password-strength-meter" id="password-meter"></div>
                </div>
            </div>
            
            <button type="submit">Create Account</button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>

    <script>
        // Simple password strength meter
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const meter = document.getElementById('password-meter');
            
            // Simple strength calculation
            let strength = 0;
            if (password.length > 6) strength += 25;
            if (password.length > 10) strength += 15;
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 20;
            
            // Update meter
            meter.style.width = `${Math.min(strength, 100)}%`;
            
            // Color based on strength
            if (strength < 40) {
                meter.style.backgroundColor = '#f56565';
            } else if (strength < 70) {
                meter.style.backgroundColor = '#ed8936';
            } else {
                meter.style.backgroundColor = '#48bb78';
            }
        });

        // Email validation
        document.getElementById('email').addEventListener('blur', function(e) {
            const email = e.target.value;
            const error = document.getElementById('email-error');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                error.style.display = 'block';
            } else {
                error.style.display = 'none';
            }
        });
    </script>
</body>
</html>
