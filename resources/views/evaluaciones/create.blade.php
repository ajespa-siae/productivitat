<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nova Avaluació') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('evaluaciones.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Empleado -->
                    <div>
                        <x-label for="empleado_id" value="{{ __('Empleat') }}" />
                        <select id="empleado_id" name="empleado_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option value="">{{ __('Selecciona un empleat') }}</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id }}">{{ $empleado->nombre }} {{ $empleado->apellidos }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="empleado_id" class="mt-2" />
                    </div>

                    <!-- Indicador -->
                    <div>
                        <x-label for="indicador_id" value="{{ __('Indicador') }}" />
                        <select id="indicador_id" name="indicador_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option value="">{{ __('Selecciona un indicador') }}</option>
                            @foreach($indicadores as $indicador)
                                <option value="{{ $indicador->id }}">{{ $indicador->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="indicador_id" class="mt-2" />
                    </div>

                    <!-- Puntuación -->
                    <div>
                        <x-label for="puntuacion" value="{{ __('Puntuació') }}" />
                        <x-input id="puntuacion" type="number" name="puntuacion" class="block mt-1 w-full" min="0" max="10" step="0.1" required />
                        <x-input-error for="puntuacion" class="mt-2" />
                    </div>

                    <!-- Comentario -->
                    <div>
                        <x-label for="comentario" value="{{ __('Comentari') }}" />
                        <textarea id="comentario" name="comentario" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"></textarea>
                        <x-input-error for="comentario" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-button>
                            {{ __('Guardar Avaluació') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
