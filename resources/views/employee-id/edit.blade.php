<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Actualitzar dades d'empleat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-96 bg-white shadow-lg rounded-lg p-6">
            <div class="mb-6">
                <h2 class="text-center text-xl font-bold text-gray-900">
                    Actualitzar Dades
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    El teu NIF no apareix a la nostra base de dades,<br> introdueix-lo a continuació.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 p-3 rounded">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm text-red-700">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('employee-id.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div>
                    <input id="employeeid" 
                           name="employeeid" 
                           type="text" 
                           required 
                           class="block w-full px-3 py-2 border border-gray-300 rounded text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"
                           value="{{ old('employeeid') }}"
                           placeholder="Introdueix el teu NIF">
                </div>

                <button type="submit" 
                        class="mt-4 w-full py-2 px-4 border border-transparent rounded shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors duration-200">
                    Actualitzar
                </button>
            </form>
        </div>
    </div>
</body>
</html>
