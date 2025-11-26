<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('user_dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groomily - Dog Grooming Booking System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .hero {
            text-align: center;
            padding: 60px 20px;
            color: white;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            color: white;
            margin-bottom: 20px;
            animation: fadeInDown 1s ease-out;
        }
        
        .hero p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            animation: fadeInUp 1s ease-out;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin: 50px 0;
        }
        
        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .floating-dog {
            font-size: 5rem;
            position: fixed;
            animation: float 6s ease-in-out infinite;
            opacity: 0.2;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }
    </style>
</head>
<body>
    <div class="floating-dog" style="top: 10%; left: 10%;">🐕</div>
    <div class="floating-dog" style="top: 60%; right: 10%; animation-delay: 2s;">🦴</div>
    <div class="floating-dog" style="bottom: 10%; left: 50%; animation-delay: 4s;">🐾</div>
    
    <div class="container">
        <div class="hero">
            <div class="logo" style="font-size: 4rem;">Groomily</div>
            <h1>Premium Dog Grooming Services</h1>
            <p>Book your furry friend's pampering session today!</p>
            
            <div class="cta-buttons">
                <a href="register.php" class="btn btn-primary" style="font-size: 1.2rem; padding: 15px 40px;">Get Started 🐾</a>
                <a href="login.php" class="btn btn-secondary" style="font-size: 1.2rem; padding: 15px 40px;">Login</a>
            </div>
        </div>
        
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">✨</div>
                <h3>Professional Groomers</h3>
                <p>Experienced and certified pet groomers who love what they do</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Easy Booking</h3>
                <p>Schedule appointments online in just a few clicks</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🛁</div>
                <h3>Premium Services</h3>
                <p>From basic grooming to spa treatments for your furry friend</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">💳</div>
                <h3>Flexible Payment</h3>
                <p>Pay with cash, card, or online payment methods</p>
            </div>
        </div>
        
        <div class="card" style="margin-top: 50px; text-align: center;">
            <h2>Why Choose Groomily?</h2>
            <p style="font-size: 1.1rem; line-height: 1.8; color: #555; margin-top: 20px;">
                We understand that your pets are family. That's why we provide the best grooming experience 
                with love, care, and professional expertise. Our state-of-the-art facility and friendly staff 
                ensure your dog feels comfortable and pampered throughout their visit.
            </p>
            <div style="margin-top: 30px;">
                <a href="register.php" class="btn btn-primary btn-full">Book Your First Appointment</a>
            </div>
        </div>
    </div>
    
    <footer style="text-align: center; padding: 30px; color: white; margin-top: 50px;">
        <p>&copy; 2024 Groomily. All rights reserved. Made with ❤️ for dogs</p>
        <p style="margin-top: 10px; font-size: 0.9rem;">
            <a href="admin_secret.php" style="color: rgba(255,255,255,0.3); text-decoration: none;">Admin</a>
        </p>
    </footer>
    <script type="module">
  // Import the functions you need from the SDKs you need
  import { initializeApp } from "https://www.gstatic.com/firebasejs/12.6.0/firebase-app.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/12.6.0/firebase-analytics.js";
  // TODO: Add SDKs for Firebase products that you want to use
  // https://firebase.google.com/docs/web/setup#available-libraries

  // Your web app's Firebase configuration
  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
  const firebaseConfig = {
    apiKey: "AIzaSyA_Eln6hTHyTnlviEwzp3YGEGeWyzY2_Og",
    authDomain: "groomily-44fe6.firebaseapp.com",
    projectId: "groomily-44fe6",
    storageBucket: "groomily-44fe6.firebasestorage.app",
    messagingSenderId: "181088047070",
    appId: "1:181088047070:web:f94c682ecc1ee80f9d974f",
    measurementId: "G-5EGKYSW2WM"
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const analytics = getAnalytics(app);
</script>
</body>
</html>