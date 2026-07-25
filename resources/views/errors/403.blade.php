<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden | Access Denied</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary: #D4AF37;
            --bg-color: #0b1120;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        /* Background decorative circles */
        .circle-1 {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(212,175,55,0.08) 0%, rgba(212,175,55,0) 70%);
            top: -100px;
            left: -100px;
            border-radius: 50%;
        }
        .circle-2 {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(212,175,55,0.05) 0%, rgba(212,175,55,0) 70%);
            bottom: -150px;
            right: -150px;
            border-radius: 50%;
        }
        .error-card {
            background-color: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 45px 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            text-align: center;
            backdrop-filter: blur(10px);
            z-index: 10;
        }
        .lock-icon-container {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 90px;
            height: 90px;
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 50%;
            margin-bottom: 25px;
            color: var(--primary);
            font-size: 3rem;
            animation: pulse 2s infinite;
        }
        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }
        p {
            color: var(--text-muted);
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn-gold {
            background-color: var(--primary);
            border: 1px solid var(--primary);
            color: #000;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-gold:hover {
            background-color: #b8962c;
            border-color: #b8962c;
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
        }
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.4);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(212, 175, 55, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(212, 175, 55, 0);
            }
        }
    </style>
</head>
<body>

    <div class="circle-1"></div>
    <div class="circle-2"></div>

    <div class="error-card">
        <div class="lock-icon-container">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h1>Access Denied</h1>
        <p>Sorry, your account does not have the required permissions to access this page. If you believe this is an error, please contact your system administrator.</p>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-gold">
            <i class="bi bi-arrow-left me-2"></i> Return to Dashboard
        </a>
    </div>

</body>
</html>
