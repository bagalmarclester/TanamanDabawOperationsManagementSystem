<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Tanaman')</title>

    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>

<body class="dashboard-body">

    @include('partials.navbar')

    <div class="dashboard-container">
        @include('partials.sidebar')

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    @include('partials.footer')
    @stack('scripts')
</body>
</html>
