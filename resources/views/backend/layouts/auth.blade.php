<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurelia Estates | Admin Login</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <style>
        :root {
            --primary: #D4AF37; /* Gold */
            --bg-color: #0b0f19;
            --surface: rgba(20, 25, 35, 0.6);
            --surface-border: rgba(255, 255, 255, 0.1);
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Ambient animated background blobs */
        .ambient-blob {
            position: absolute;
            filter: blur(100px);
            border-radius: 50%;
            z-index: -1;
            animation: float 20s infinite ease-in-out alternate;
        }
        
        .blob-1 {
            width: 40vw;
            height: 40vw;
            background: rgba(212, 175, 55, 0.15);
            top: -10%;
            left: -10%;
        }
        
        .blob-2 {
            width: 30vw;
            height: 30vw;
            background: rgba(43, 88, 118, 0.2);
            bottom: -10%;
            right: -5%;
            animation-delay: -10s;
        }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(5%, 10%) scale(1.1); }
            100% { transform: translate(-5%, -10%) scale(0.9); }
        }

        @yield('styles')
    </style>
</head>
<body>
    <div class="ambient-blob blob-1"></div>
    <div class="ambient-blob blob-2"></div>

    <main class="w-100">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
