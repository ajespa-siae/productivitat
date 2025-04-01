<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Avaluació de Rendiment</h2>
            <p class="text-center text-gray-600 mb-8">{{ __('Login') }}</p>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-6">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('User') }}
                    </label>
                    <input id="username" 
                           name="username" 
                           type="text" 
                           class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 h-10" 
                           value="{{ old('username') }}" 
                           required 
                           autofocus>
                    @error('username')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('Password') }}
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 h-10" 
                           required>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" 
                               name="remember" 
                               class="rounded border-gray-300 text-amber-600 shadow-sm focus:ring-amber-500">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember') }}</span>
                    </label>
                    
                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="text-sm text-amber-600 hover:text-amber-500">
                        {{ __('Panel Admin') }}
                    </a>
                </div>

                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        {{ __('Login') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
