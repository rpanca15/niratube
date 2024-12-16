<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0"> --}}
    <title>
        @yield('title')
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @yield('style')
</head>

<body class="bg-gray-100">
    <nav class="w-full h-[80px] bg-white shadow px-8 flex items-center justify-between fixed z-20">
        <a href="{{ route('home') }}" class="text-xl font-bold text-gray-700">NiraTube</a>
        <div class="flex items-center justify-center gap-2">
            @guest
                <a href="{{ route('login') }}"
                    class="bg-blue-500 text-white font-semibold px-4 py-2 rounded-lg hover:bg-blue-600 transition">Masuk
                    <i class="fas fa-sign-in-alt ms-1"></i></a>
                <a href="{{ route('register') }}"
                    class="bg-green-500 text-white font-semibold px-4 py-2 rounded-lg hover:bg-green-600 transition">Daftar
                    <i class="fas fa-user-plus ms-1"></i></a>
            @endguest

            @auth
                <span class="text-gray-700 font-semibold">{{ Auth::user()->name }}</span>
                @if (!request()->routeIs('videos.index'))
                    <a href="{{ route('videos.index') }}"
                        class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition">Video Saya</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">Logout</button>
                </form>
            @endauth
        </div>
    </nav>

    <main class="container mx-auto p-6">
        @yield('content')
    </main>

    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        //message with sweetalert
        @if (session('success'))
            Swal.fire({
                icon: "success",
                title: "BERHASIL",
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @elseif (session('error'))
            Swal.fire({
                icon: "error",
                title: "GAGAL!",
                text: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @endif
    </script> --}}

    @yield('scripts')
</body>

</html>
