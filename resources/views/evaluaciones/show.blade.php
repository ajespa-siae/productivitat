@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm text-amber-600 hover:text-amber-500">
                <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Tornar al Dashboard
            </a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Detalls de l'Avaluació
                </h3>
            </div>

            <div class="px-4 py-5 sm:p-6">
                <!-- Información general -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mb-8">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Avaluat/da</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $evaluacion->evaluado->nombre }} {{ $evaluacion->evaluado->apellidos }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Data</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $evaluacion->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                <!-- Lista de resultados -->
                <div class="mt-8">
                    <h4 class="text-base font-medium text-gray-900 mb-4">Resultats Registrats</h4>
                    <div class="bg-gray-50 rounded-lg overflow-hidden">
                        <ul class="divide-y divide-gray-200">
                            @foreach($resultadosPorIndicador as $indicadorId => $resultados)
                            <li class="px-4 py-4">
                                <div class="mb-3">
                                    <h5 class="text-sm font-medium text-gray-900">
                                        {{ $resultados->first()->indicador->nombre }}
                                    </h5>
                                </div>
                                @foreach($resultados as $resultado)
                                <div class="ml-4 mb-2 last:mb-0">
                                    <div class="sm:flex sm:items-center sm:justify-between">
                                        <div class="mb-2 sm:mb-0">
                                            <p class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($resultado->created_at)->format('d/m/Y H:i') }}
                                                <span class="text-gray-400 ml-2">·</span>
                                                <span class="ml-2">{{ $evaluacion->evaluador->nombre }} {{ $evaluacion->evaluador->apellidos }}</span>
                                            </p>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                                $resultado->indicador->sentido === 'Positiu' 
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-red-100 text-red-800'
                                            }}">
                                                {{ $resultado->puntuacion == 1 ? number_format($resultado->puntuacion) . ' punt' : number_format($resultado->puntuacion) . ' punts' }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($resultado->comentario)
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-600">
                                            {{ $resultado->comentario }}
                                        </p>
                                    </div>
                                    @endif

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $resultado->puntuacion }}
                                        
                                        @if($evaluacion->evaluado->nif === Auth::user()->nif && !$resultado->alegacion)
                                            <button onclick="document.getElementById('modal-alegar-{{ $resultado->id }}').classList.remove('hidden')" 
                                                    class="ml-2 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-amber-700 bg-amber-100 hover:bg-amber-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                                Alegar
                                            </button>

                                            <!-- Modal Alegar -->
                                            <div id="modal-alegar-{{ $resultado->id }}" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                        <form action="{{ route('alegaciones.store', $resultado) }}" method="POST">
                                                            @csrf
                                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                                <h3 class="text-lg font-medium text-gray-900 mb-4">Presentar alegación</h3>
                                                                <div class="mb-4">
                                                                    <label for="texto_alegacion" class="block text-sm font-medium text-gray-700">Texto de la alegación</label>
                                                                    <textarea name="texto_alegacion" id="texto_alegacion" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 p-3" required minlength="10"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 sm:w-auto sm:text-sm">Enviar alegación</button>
                                                                <button type="button" onclick="document.getElementById('modal-alegar-{{ $resultado->id }}').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($resultado->alegacion)
                                            <div class="mt-2 text-sm">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $resultado->alegacion->estado === 'Pendent' ? 'bg-yellow-100 text-yellow-800' : ($resultado->alegacion->estado === 'Acceptada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                                    Al·legació: {{ $resultado->alegacion->estado === 'Pendent' ? 'Pendent' : ($resultado->alegacion->estado === 'Acceptada' ? 'Acceptada' : 'Rebutjada') }}
                                                </span>
                                            </div>
                                            
                                            @if($evaluacion->evaluador->nif === Auth::user()->nif && $resultado->alegacion->estado === 'Pendent')
                                                <div class="flex items-center space-x-4 mt-2 bg-gray-50 p-2 rounded-md">
                                                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($resultado->alegacion->fecha_alegacion)->format('d/m/Y H:i') }}</span>
                                                    <p class="text-sm text-gray-600 flex-1">{{ $resultado->alegacion->texto_alegacion }}</p>
                                                    <button onclick="document.getElementById('modal-responder-{{ $resultado->alegacion->id }}').classList.remove('hidden')" 
                                                            class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-amber-700 bg-amber-100 hover:bg-amber-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 whitespace-nowrap">
                                                        Respondre
                                                    </button>
                                                </div>

                                                <!-- Modal Responder -->
                                                <div id="modal-responder-{{ $resultado->alegacion->id }}" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                            <form action="{{ route('alegaciones.responder', $resultado->alegacion) }}" method="POST">
                                                                @csrf
                                                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Respondre a l'al·legació</h3>
                                                                    <div class="mb-4">
                                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Estat de l'al·legació</label>
                                                                        <div class="space-y-2">
                                                                            <div class="flex items-center">
                                                                                <input type="radio" id="aceptar" name="estado" value="Acceptada" class="focus:ring-amber-500 h-4 w-4 text-amber-600 border-gray-300" required>
                                                                                <label for="aceptar" class="ml-2 block text-sm text-gray-700">Acceptar</label>
                                                                            </div>
                                                                            <div class="flex items-center">
                                                                                <input type="radio" id="rechazar" name="estado" value="Rebutjada" class="focus:ring-amber-500 h-4 w-4 text-amber-600 border-gray-300" required>
                                                                                <label for="rechazar" class="ml-2 block text-sm text-gray-700">Rebutjar</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-4">
                                                                        <label for="respuesta" class="block text-sm font-medium text-gray-700">Resposta</label>
                                                                        <textarea name="respuesta" id="respuesta" rows="4" class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50" required minlength="10"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 sm:w-auto sm:text-sm">Enviar resposta</button>
                                                                    <button type="button" onclick="document.getElementById('modal-responder-{{ $resultado->alegacion->id }}').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel·lar</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Detalles de la alegación y respuesta -->
                                            <div class="mt-4 space-y-3 text-sm">
                                                @if($resultado->alegacion->estado !== 'Pendent')
                                                <div>
                                                    <div class="flex items-center justify-between mb-1">
                                                        <p class="font-medium text-gray-700">Al·legació:</p>
                                                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($resultado->alegacion->fecha_alegacion)->format('d/m/Y H:i') }}</span>
                                                    </div>
                                                    <p class="text-gray-600 {{ $resultado->alegacion->estado === 'Acceptada' ? 'bg-green-50' : 'bg-red-50' }} p-3 rounded-md">{{ $resultado->alegacion->texto_alegacion }}</p>
                                                </div>
                                                @endif

                                                @if($resultado->alegacion->estado !== 'Pendent' && $resultado->alegacion->respuesta)
                                                    <div>
                                                        <div class="flex items-center justify-between mb-1">
                                                            <p class="font-medium text-gray-700">Resposta de l'avaluador:</p>
                                                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($resultado->alegacion->fecha_respuesta)->format('d/m/Y H:i') }}</span>
                                                        </div>
                                                        <p class="text-gray-600 {{ $resultado->alegacion->estado === 'Acceptada' ? 'bg-green-50' : 'bg-red-50' }} p-3 rounded-md">{{ $resultado->alegacion->respuesta }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>                            
                                </div>
                                @endforeach
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
