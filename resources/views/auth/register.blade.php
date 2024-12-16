<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0"> --}}
    <title>Daftar - NiraTube</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-10">
        <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-6 text-center">Daftar di NiraTube</h1>

            <form action="{{ route('register') }}" method="POST" onsubmit="return validateForm()">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="mt-1 p-2 border border-gray-300 rounded-md w-full" placeholder="Masukkan nama Anda" />
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p id="error-name" class="text-red-600 text-sm mt-1 hidden">Nama wajib diisi.</p>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="mt-1 p-2 border border-gray-300 rounded-md w-full" placeholder="Masukkan email Anda" />
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p id="error-email" class="text-red-600 text-sm mt-1 hidden">Email wajib diisi.</p>
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password"
                        class="mt-1 p-2 border border-gray-300 rounded-md w-full"
                        placeholder="Masukkan password Anda" />
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p id="error-password" class="text-red-600 text-sm mt-1 hidden">Password wajib diisi.</p>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi
                        Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="mt-1 p-2 border border-gray-300 rounded-md w-full"
                        placeholder="Konfirmasi password Anda" />
                    @error('password_confirmation')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p id="error-password-confirmation" class="text-red-600 text-sm mt-1 hidden">Konfirmasi password
                        wajib diisi.</p>
                </div>

                <button type="submit"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg w-full hover:bg-red-700 transition">Daftar</button>
            </form>

            <div class="mt-4 text-center">
                <p>Sudah punya akun? <a href="{{ route('login') }}" class="text-red-600">Login sekarang</a></p>
            </div>
        </div>
    </div>

    <script>
        function validateForm() {
            let isValid = true;

            // Reset error messages
            document.getElementById('error-name').classList.add('hidden');
            document.getElementById('error-email').classList.add('hidden');
            document.getElementById('error-password').classList.add('hidden');
            document.getElementById('error-password-confirmation').classList.add('hidden');

            // Get values
            let name = document.getElementById('name').value;
            let email = document.getElementById('email').value;
            let password = document.getElementById('password').value;
            let passwordConfirmation = document.getElementById('password_confirmation').value;

            // Check fields
            if (!name) {
                document.getElementById('error-name').classList.remove('hidden');
                isValid = false;
            }

            if (!email) {
                document.getElementById('error-email').classList.remove('hidden');
                isValid = false;
            }

            if (!password) {
                document.getElementById('error-password').classList.remove('hidden');
                isValid = false;
            }

            if (!passwordConfirmation) {
                document.getElementById('error-password-confirmation').classList.remove('hidden');
                isValid = false;
            } else if (password !== passwordConfirmation) {
                alert("Password dan Konfirmasi Password tidak sama.");
                isValid = false;
            }

            return isValid;
        }
    </script>
</body>

</html>
