<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white h-screen shadow-lg relative">
        <div class="p-4 text-center">
            <a href="{{ route('home') }}" class="text-xl font-bold text-gray-700">Niratube</a>
        </div>
        <nav class="mt-8">
            <ul>
                <li>
                    <a href="#my-videos" class="sidebar-link block py-2 px-4 text-gray-700 hover:bg-blue-100 transition"
                        data-tab="my-videos">
                        Video Saya
                    </a>
                </li>
                <li>
                    <a href="#liked-videos"
                        class="sidebar-link block py-2 px-4 text-gray-700 hover:bg-blue-100 transition"
                        data-tab="liked-videos">
                        Video yang Disukai
                    </a>
                </li>
            </ul>
        </nav>
        <form action="{{ route('logout') }}" method="POST" class="min-w-full absolute bottom-0 px-4 py-2">
            @csrf
            <button type="submit"
                class="min-w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">Logout</button>
        </form>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>

    @yield('scripts')
</body>

</html>
