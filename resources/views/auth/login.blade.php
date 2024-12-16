<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - NiraTube</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-10">
        <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-6 text-center">Login ke NiraTube</h1>

            @if ($errors->any())
                <div class="mb-4">
                    <div class="text-red-600">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required
                        class="mt-1 p-2 border border-gray-300 rounded-md w-full" placeholder="Masukkan email Anda" />
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required
                        class="mt-1 p-2 border border-gray-300 rounded-md w-full"
                        placeholder="Masukkan password Anda" />
                </div>
                <button type="submit"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg w-full hover:bg-red-700 transition">Login</button>
            </form>

            <div class="mt-4 text-center">
                <p>Belum punya akun? <a href="{{ route('register') }}" class="text-red-600">Daftar sekarang</a></p>
            </div>
        </div>
    </div>
</body>

</html>
