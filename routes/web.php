<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmployeeIdController;
use App\Http\Controllers\EvaluacionController;
use App\Http\Controllers\AlegacionResultadoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Middleware\CheckEmployeeId;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Rutas de autenticación
Route::middleware('web')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);
    });
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
});

// Rutas protegidas
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/employee-id', [EmployeeIdController::class, 'edit'])->name('employee-id.edit');
    Route::put('/employee-id', [EmployeeIdController::class, 'update'])->name('employee-id.update');
    
    Route::middleware([CheckEmployeeId::class])->group(function () {
        // Rutas para alegaciones
        Route::post('/resultados/{resultado}/alegar', [AlegacionResultadoController::class, 'store'])->name('alegaciones.store');
        Route::post('/alegaciones/{alegacion}/responder', [AlegacionResultadoController::class, 'responder'])->name('alegaciones.responder');

        // Rutas para notificaciones
        Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
        Route::get('/notificaciones/no-leidas', [NotificacionController::class, 'noLeidas'])->name('notificaciones.no-leidas');
        Route::post('/notificaciones/{notificacion}/marcar-leida', [NotificacionController::class, 'marcarComoLeida'])->name('notificaciones.marcar-leida');
        Route::post('/notificaciones/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasComoLeidas'])->name('notificaciones.marcar-todas-leidas');
        Route::delete('/notificaciones/{notificacion}', [NotificacionController::class, 'eliminar'])->name('notificaciones.eliminar');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Rutas de evaluación
        Route::get('/evaluaciones/create', [EvaluacionController::class, 'create'])->name('evaluaciones.create');
        Route::get('/evaluaciones/indicadores', [EvaluacionController::class, 'getIndicadores'])->name('evaluaciones.indicadores');
        Route::post('/evaluaciones', [EvaluacionController::class, 'store'])->name('evaluaciones.store');
        Route::get('/evaluaciones/{evaluacion}', [EvaluacionController::class, 'show'])->name('evaluaciones.show');
    });
});
