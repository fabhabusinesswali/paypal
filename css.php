<?php
// Set content type to CSS
header("Content-type: text/css");

// Define PayPal-inspired color palette
$primaryColor = "#0070BA";  // PayPal Blue
$secondaryColor = "#00457C"; // Dark Blue
$accentColor = "#FFC439";  // PayPal Yellow
$backgroundColor = "#F5F7FA"; // Light Background
$textColor = "#001C33"; // Dark Text
?>

/* PayPal Clone - Inspired Design */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: <?php echo $backgroundColor; ?>; 
    color: <?php echo $textColor; ?>;
    line-height: 1.6;
    overflow-x: hidden;
}

/* Navigation Bar */
.navbar {
    background: <?php echo $primaryColor; ?>;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.navbar a {
    color: white;
    text-decoration: none;
    font-weight: bold;
    font-size: 18px;
    transition: 0.3s;
}

.navbar a:hover {
    color: <?php echo $accentColor; ?>;
}

/* Hero Section */
.hero {
    text-align: center;
    padding: 80px 20px;
    background: linear-gradient(135deg, <?php echo $primaryColor; ?>, <?php echo $secondaryColor; ?>);
    color: white;
}

.hero h1 {
    font-size: 48px;
    margin-bottom: 20px;
}

.hero p {
    font-size: 20px;
    margin-bottom: 30px;
}

/* Call to Action Button */
.btn {
    background-color: <?php echo $accentColor; ?>;
    color: <?php echo $textColor; ?>;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    cursor: pointer;
    transition: 0.4s ease;
}

.btn:hover {
    background-color: <?php echo $primaryColor; ?>;
    color: white;
    transform: scale(1.05);
}

/* Cards Section */
.cards-container {
    display: flex;
    justify-content: center;
    gap: 30px;
    padding: 40px 20px;
    flex-wrap: wrap;
}

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    padding: 30px;
    width: 320px;
    transition: transform 0.3s;
    text-align: center;
}

.card h3 {
    color: <?php echo $primaryColor; ?>;
    font-size: 28px;
}

.card p {
    font-size: 16px;
}

.card:hover {
    transform: translateY(-8px);
}

/* Footer */
.footer {
    background: <?php echo $secondaryColor; ?>;
    color: white;
    text-align: center;
    padding: 20px 0;
}

.footer p {
    margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero h1 {
        font-size: 32px;
    }

    .hero p {
        font-size: 16px;
    }

    .btn {
        padding: 10px 20px;
        font-size: 16px;
    }
}
