<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery (latest only) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Axios CSRF setup -->
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] =
            document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>

    <!-- Add date-fns CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/date-fns/4.1.0/cdn.min.js"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-6">

        {{-- Flash success message --}}
        @if (session('success'))
            <div id="flash-message" role="alert"
                class="bg-green-500 text-white p-3 mb-4 rounded flex items-center justify-between shadow">
                <span>{{ session('success') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="white" class="w-5 h-5 cursor-pointer"
                    onclick="document.getElementById('flash-message').remove()">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
        @endif

        {{-- Main content area --}}
        @yield('content')
    </div>

    {{-- Auto fade-out flash message --}}
    <script>
        const flash = document.getElementById('flash-message');
        if (flash) {
            setTimeout(() => {
                flash.style.transition = 'opacity 0.5s ease';
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 500);
            }, 3000);
        }
    </script>
</body>

</html>
