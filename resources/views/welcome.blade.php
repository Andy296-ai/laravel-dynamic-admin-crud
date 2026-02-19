<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - {{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon_logo.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <div class="flex justify-center mb-5">
                <img src="{{ asset('favicon_logo.svg') }}" alt="Logo" class="h-20 w-20 rounded-2xl bg-white p-3 shadow">
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Welcome to Laravel Admin Panel</h1>
            <p class="text-gray-600 mb-8">Dynamic CRUD Admin Panel</p>
            <a href="{{ route('login') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Go to Admin Panel
            </a>
        </div>
    </div>
</body>
</html>
