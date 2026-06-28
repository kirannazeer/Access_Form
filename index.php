<?php
session_start();
include('config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AccessForm - Inclusive & Accessible Survey Platform</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root {
            --primary: #2563eb;
            --accent: #10b981;
            --dark: #0f172a;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc; 
            color: #334155; 
        }

        .hero-section {
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(37, 99, 235, 0.85)), 
                        url('https://images.unsplash.com/photo-1516321497487-e288fb19713f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80') center/cover;
            padding: 130px 0 100px;
            color: white;
            text-align: center;
        }

        .search-container {
            max-width: 900px;
            margin: -70px auto 0;
            background: rgba(255, 255, 255, 0.97);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
        }

        .feature-box {
            text-align: center;
            padding: 40px 25px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
        }
        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(37, 99, 235, 0.1);
        }
        .feature-box i {
            font-size: 3rem;
            margin-bottom: 20px;
            color: var(--primary);
        }

        .badge-accessibility {
            background: linear-gradient(90deg, #10b981, #34d399);
            color: white;
            font-weight: 600;
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<!-- Hero Section -->
<header class="hero-section">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeInDown text-white">
            AccessForm
        </h1>
        <p class="lead mb-4 col-lg-9 mx-auto animate__animated animate__fadeInUp">
            Pakistan’s First <strong>Fully Accessible</strong> Survey &amp; Form Platform
        </p>
        <p class="mb-5 opacity-90">
            Built for everyone — Supporting High Contrast, Dyslexia-Friendly, Voice Input &amp; Screen Reader
        </p>

        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="login.php" class="btn btn-light btn-lg px-5 py-3 fw-semibold">
                <i class="fas fa-pen-to-square me-2"></i> Fill a Form
            </a>
            <a href="login.php" class="btn btn-outline-light btn-lg px-5 py-3 fw-semibold">
                <i class="fas fa-user-shield me-2"></i> Creator Login
            </a>
        </div>

        <div class="mt-5">
            <span class="badge badge-accessibility px-4 py-2 fs-6">
                <i class="fas fa-universal-access me-2"></i> WCAG 2.2 Compliant
            </span>
        </div>
    </div>
</header>

<main class="container">


    <!-- Features -->
    <section class="py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why AccessForm?</h2>
            <p class="text-muted">Designed with accessibility at its core</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fas fa-headphones"></i>
                    <h5>Voice Input</h5>
                    <p class="text-muted">Speak your answers — Powered by Web Speech API</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fas fa-eye-low-vision"></i>
                    <h5>High Contrast &amp; Dyslexia Mode</h5>
                    <p class="text-muted">Special themes for visually impaired and dyslexic users</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fas fa-universal-access"></i>
                    <h5>Full Keyboard Navigation</h5>
                    <p class="text-muted">Complete screen reader compatibility with ARIA support</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <div class="bg-primary text-white rounded-4 p-5 text-center my-5">
        <h3 class="mb-3">Ready to Create or Participate?</h3>
        <p class="lead mb-4">Join thousands of users who believe in inclusive digital experiences.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="register.php" class="btn btn-light btn-lg">Register Now</a>
            <a href="login.php" class="btn btn-outline-light btn-lg">Browse Forms</a>
        </div>
    </div>

</main>

<footer class="bg-dark text-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5 class="fw-bold text-white">AccessForm</h5>
                <p class="small opacity-75">Making digital forms accessible for everyone in Pakistan.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="small opacity-75 mb-0">
                    &copy; 2026 AccessForm | Virtual University Project<br>
                    Built with ❤️ for Inclusive Education
                </p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>