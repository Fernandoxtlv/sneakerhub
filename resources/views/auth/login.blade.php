<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - SneakerHub</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="font-sans antialiased bg-gradient-to-br from-sneaker-dark via-sneaker-gray to-sneaker-dark min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center justify-center mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="SneakerHub" class="h-24 w-auto drop-shadow-lg">
        </a>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-8">
            <h1 class="text-2xl font-bold text-gray-900 text-center mb-2">¡Bienvenido de vuelta!</h1>
            <p class="text-gray-500 text-center mb-8">Ingresa a tu cuenta para continuar</p>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="form-input" placeholder="tu@email.com">
                </div>

                <div>
                    <label for="password" class="form-label">Contraseña</label>
                    <input id="password" type="password" name="password" required class="form-input"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember"
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-600">Recordarme</span>
                    </label>
                    <a href="#" class="text-sm text-primary-600 hover:text-primary-700 font-medium">¿Olvidaste tu
                        contraseña?</a>
                </div>

                <button type="submit" class="btn btn-primary w-full">
                    Iniciar Sesión
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-gray-500">¿No tienes una cuenta?</p>
                <a href="{{ route('register') }}"
                    class="text-primary-600 hover:text-primary-700 font-semibold">Regístrate aquí</a>
            </div>
        </div>

        <p class="text-center text-gray-500 text-sm mt-8">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">← Volver a la tienda</a>
        </p>
    </div>
</body>

</html>