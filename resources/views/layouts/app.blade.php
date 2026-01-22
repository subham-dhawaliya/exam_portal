<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Exam Portal')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ios: {
                            blue: '#007AFF',
                            green: '#34C759',
                            red: '#FF3B30',
                            orange: '#FF9500',
                            yellow: '#FFCC00',
                            purple: '#AF52DE',
                            pink: '#FF2D55',
                            teal: '#5AC8FA',
                            indigo: '#5856D6',
                            gray: '#8E8E93',
                            'gray-2': '#AEAEB2',
                            'gray-3': '#C7C7CC',
                            'gray-4': '#D1D1D6',
                            'gray-5': '#E5E5EA',
                            'gray-6': '#F2F2F7',
                        }
                    },
                    fontFamily: {
                        'sf': ['Inter', '-apple-system', 'BlinkMacSystemFont', 'SF Pro Display', 'Segoe UI', 'Roboto', 'sans-serif'],
                    },
                    boxShadow: {
                        'ios': '0 2px 15px rgba(0, 0, 0, 0.08)',
                        'ios-lg': '0 8px 30px rgba(0, 0, 0, 0.12)',
                        'ios-xl': '0 15px 50px rgba(0, 0, 0, 0.15)',
                        'glow-blue': '0 0 40px rgba(0, 122, 255, 0.3)',
                        'glow-purple': '0 0 40px rgba(175, 82, 222, 0.3)',
                        'glow-green': '0 0 40px rgba(52, 199, 89, 0.3)',
                    },
                    backgroundImage: {
                        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                        'mesh-gradient': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    }
                }
            }
        }
    </script>
    <style>
        * { 
            -webkit-tap-highlight-color: transparent; 
            scroll-behavior: smooth;
        }
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
            min-height: 100vh;
        }
        
        /* Glassmorphism Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }
        
        /* Premium Button */
        .ios-btn {
            padding: 14px 28px;
            border-radius: 16px;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: -0.2px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .ios-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 100%);
            pointer-events: none;
        }
        .ios-btn:active {
            transform: scale(0.97);
        }
        
        /* Premium Input */
        .ios-input {
            width: 100%;
            padding: 16px 20px;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid transparent;
            border-radius: 16px;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }
        .ios-input:focus {
            outline: none;
            border-color: #007AFF;
            background: white;
            box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1), 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        .ios-input::placeholder {
            color: #AEAEB2;
        }
        
        /* Animations */
        .slide-up {
            animation: slideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.4s ease forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .scale-in {
            animation: scaleIn 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        /* iOS Card */
        .ios-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .ios-card:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #007AFF 0%, #AF52DE 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Shimmer Effect */
        .shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        /* Pulse Ring */
        .pulse-ring {
            animation: pulseRing 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulseRing {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #C7C7CC;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #8E8E93;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full font-sf antialiased text-gray-900">
    @yield('body')
    @stack('scripts')
</body>
</html>
