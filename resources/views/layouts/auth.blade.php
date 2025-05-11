<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Klasifikasi Minyak</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-blue-100">
    <div class="min-h-screen flex">
        <!-- Left side with image -->
        <div class="w-1/2 bg-center relative" >
            <div class="absolute inset-0 bg-black bg-opacity-10"></div>
            <div class="relative z-10 flex items-center justify-center h-full text-center p-8">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-4">Klasifikasi Minyak</h1>
                    <p class="text-xl text-white">Sistem klasifikasi minyak berdasarkan karakteristik fisik dan kimia.</p>
                </div>
            </div>
        </div>

        <!-- Right side with form -->
        <div class="w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
                @yield('content')
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html> 