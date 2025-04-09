@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6">
            <!-- Panel Principal -->
            <div class="w-full">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold text-gray-800">Les meves avaluacions</h2>
                            @if($user->is_mando)
                                <button type="button" onclick="openModal()" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 focus:bg-amber-700 active:bg-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Nova avaluació
                                </button>

                                <!-- Modal -->                                
                                <div id="evaluacionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                        <div class="mt-3">
                                            <h3 class="text-lg font-medium text-gray-900 mb-4">Nova Avaluació</h3>
                                            <form action="{{ route('evaluaciones.store') }}" method="POST" class="space-y-4">
                                                @csrf
                                                
                                                <!-- Empleado -->
                                                <div>
                                                    <x-label for="empleado_id" value="{{ __('Empleat') }}" />
                                                    <select id="empleado_id" name="empleado_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" required>
                                                        <option value="">{{ __('Selecciona un empleat') }}</option>
                                                        @foreach($empleadosEvaluables as $empleado)
                                                            <option value="{{ $empleado->id }}">{{ $empleado->nombre }} {{ $empleado->apellidos }}</option>
                                                        @endforeach
                                                    </select>
                                                    <x-input-error for="empleado_id" class="mt-2" />
                                                </div>

                                                <!-- Indicador -->
                                                <div>
                                                    <x-label for="indicador_id" value="{{ __('Indicador') }}" />
                                                    <select id="indicador_id" name="indicador_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" required>
                                                        <option value="">{{ __('Selecciona un indicador') }}</option>
                                                        @foreach($indicadores as $indicador)
                                                            <option value="{{ $indicador->id }}">{{ $indicador->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                    <x-input-error for="indicador_id" class="mt-2" />
                                                </div>

                                                <!-- Comentario -->
                                                <div>
                                                    <x-label for="comentario" value="{{ __('Comentari') }}" />
                                                    <textarea id="comentario" name="comentario" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"></textarea>
                                                    <x-input-error for="comentario" class="mt-2" />
                                                </div>

                                                <!-- Hidden input for puntuacion -->
                                                <input type="hidden" name="puntuacion" value="1">

                                                <div class="flex items-center justify-end mt-4 space-x-3">
                                                    <button type="button" onclick="closeModal()" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        {{ __('Cancel·lar') }}
                                                    </button>
                                                    <x-button>
                                                        {{ __('Guardar') }}
                                                    </x-button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                @push('scripts')
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        window.openModal = function() {
                                            document.getElementById('evaluacionModal').classList.remove('hidden');
                                        }

                                        window.closeModal = function() {
                                            document.getElementById('evaluacionModal').classList.add('hidden');
                                        }

                                        // Close modal when clicking outside
                                        const modal = document.getElementById('evaluacionModal');
                                        if (modal) {
                                            modal.addEventListener('click', function(e) {
                                                if (e.target === this) {
                                                    closeModal();
                                                }
                                            });
                                        }
                                    });
                                </script>
                                @endpush
                            @endif
                        </div>

                        <div class="space-y-4 mt-4">
                            <!-- Evaluaciones realizadas -->
                            <div>
                                <h3 class="text-base font-medium text-gray-900 mb-2">Avaluacions realitzades</h3>
                                @if($evaluacionesRecientes->count() > 0)
                                    <div class="space-y-1.5">
                                        @foreach($evaluacionesRecientes as $evaluacion)
                                            <div class="py-1.5 px-3 border border-gray-200 rounded hover:bg-gray-50 transition duration-150 ease-in-out">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $evaluacion->evaluado->nombre }} {{ $evaluacion->evaluado->apellidos }}</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">{{ $evaluacion->created_at->format('d/m/Y') }}</p>
                                                    </div>
                                                    <a href="{{ route('evaluaciones.show', $evaluacion) }}" class="inline-flex items-center px-3 py-1 bg-amber-50 border border-amber-200 rounded text-sm font-medium text-amber-900 hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        Veure detalls
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500">No has realitzat cap avaluació encara.</p>
                                @endif
                            </div>

                            <!-- Evaluaciones recibidas -->
                            <div>
                                <h3 class="text-base font-medium text-gray-900 mb-2">Avaluacions rebudes</h3>
                                @if($evaluacionesRecibidas->count() > 0)
                                    <div class="space-y-1.5">
                                        @foreach($evaluacionesRecibidas as $evaluacion)
                                            <div class="py-1.5 px-3 border border-gray-200 rounded hover:bg-gray-50 transition duration-150 ease-in-out">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">De: {{ $evaluacion->evaluador->nombre }} {{ $evaluacion->evaluador->apellidos }}</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">{{ $evaluacion->created_at->format('d/m/Y') }}</p>
                                                    </div>
                                                    <a href="{{ route('evaluaciones.show', $evaluacion) }}" class="inline-flex items-center px-3 py-1 bg-amber-50 border border-amber-200 rounded text-sm font-medium text-amber-900 hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        Veure detalls
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500">No has rebut cap avaluació encara.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
