extends('layouts.app')

@section('title', 'Gestión de Caja Diaria')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-cash-register me-2"></i> Gestión de Caja Menor
                    </h5>
                    <div>
                        <span class="badge bg-info text-dark fs-9">
                            Fecha: {{ now()->format('d/m/Y') }}
                        </span>
                    </div>
                    <div class="d-flex">
                        @if(!$caja || $caja->estaCerrada() || $caja->estaEnRevision())
                        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalAbrirCaja">
                            <span class="fa fa-lock-open"></span> Abrir caja
                        </button>
                        @endif
                        
                        @if($caja && $caja->estaAbierta())
                        <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#modalCerrarCaja">
                            <span class="fa fa-lock"></span> Cerrar caja
                        </button>
                        @endif
                        
                        <a href="{{ route('caja.historial') }}" class="btn btn-info">
                            <span class="fa fa-history"></span> Historial
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Resumen de Caja -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-start border-primary border-4">
                                <div class="card-body">
                                    <h6 class="text-muted">Monto Inicial</h6>
                                    <h4 class="text-primary">S/. {{ number_format($caja->monto_inicial ?? 0, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card border-start border-success border-4">
                                <div class="card-body">
                                    <h6 class="text-muted">Ventas</h6>
                                    <h4 class="text-success">S/. {{ number_format($totalVentas ?? 0, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card border-start border-info border-4">
                                <div class="card-body">
                                    <h6 class="text-muted">Ingresos</h6>
                                    <h4 class="text-info">S/. {{ number_format($totalIngresos ?? 0, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card border-start border-warning border-4">
                                <div class="card-body">
                                    <h6 class="text-muted">Egresos</h6>
                                    <h4 class="text-danger">S/. {{ number_format($totalEgresos ?? 0, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-start border-dark border-4">
                                <div class="card-body">
                                    <h6 class="text-muted">Saldo Actual</h6>
                                    <h4 class="text-dark">S/. {{ number_format($saldoActual ?? 0, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <!-- Estado de Caja -->
                            <div class="card h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-tasks"></i> Estado de Caja</h6>
                                    @if($caja && Auth::user()->hasRole('admin'))
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <form action="{{ route('caja.cambiar-estado', $caja) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="estado" value="abierta">
                                                    <button type="submit" class="dropdown-item">Marcar como Abierta</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('caja.cambiar-estado', $caja) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="estado" value="cerrada">
                                                    <button type="submit" class="dropdown-item">Marcar como Cerrada</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('caja.cambiar-estado', $caja) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="estado" value="en_revision">
                                                    <button type="submit" class="dropdown-item">Marcar como En Revisión</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                                <div class="card-body text-center">
                                    <div id="estado-caja">
                                        @if($caja && $caja->estaAbierta())
                                        <div class="caja-status">
                                            <div class="text-center">
                                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                                <h4 class="text-success">CAJA ABIERTA</h4>
                                                <p><strong>Fecha:</strong> {{ $caja->fecha_apertura->format('d-m-Y') }}</p>
                                                <p><strong>Usuario:</strong> {{ $caja->usuario->name }}</p>
                                                <p><strong>Valor Inicial:</strong> S/. {{ number_format($caja->monto_inicial, 2) }}</p>
                                                <p><strong>Hora Apertura:</strong> {{ $caja->fecha_apertura->format('h:i a') }}</p>
                                                <p><strong>Duración:</strong> <span id="duracion-caja"></span></p>
                                            </div>
                                        </div>
                                        @elseif($caja && $caja->estaCerrada())
                                        <div class="caja-status">
                                            <div class="text-center">
                                                <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                                                <h4 class="text-primary">CAJA CERRADA</h4>
                                                <p><strong>Fecha:</strong> {{ $caja->fecha_apertura->format('d-m-Y') }}</p>
                                                <p><strong>Usuario:</strong> {{ $caja->usuario->name }}</p>
                                                <p><strong>Monto Cierre:</strong> S/. {{ number_format($caja->monto_cierre, 2) }}</p>
                                                <p><strong>Hora Cierre:</strong> {{ $caja->fecha_cierre->format('h:i a') }}</p>
                                            </div>
                                        </div>
                                        @elseif($caja && $caja->estaEnRevision())
                                        <div class="caja-status">
                                            <div class="text-center">
                                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                                <h4 class="text-warning">EN REVISIÓN</h4>
                                                <p><strong>Fecha:</strong> {{ $caja->fecha_apertura->format('d-m-Y') }}</p>
                                                <p><strong>Usuario:</strong> {{ $caja->usuario->name }}</p>
                                                <p><strong>Monto Cierre:</strong> S/. {{ number_format($caja->monto_cierre, 2) }}</p>
                                                <p><strong>Hora Cierre:</strong> {{ $caja->fecha_cierre->format('h:i a') }}</p>
                                                <p class="text-danger"><strong>Requiere verificación</strong></p>
                                            </div>
                                        </div>
                                        @else
                                        <div class="caja-status">
                                            <div class="text-center">
                                                <i class="fas fa-lock fa-3x text-secondary mb-3"></i>
                                                <h4 class="text-secondary">CAJA CERRADA</h4>
                                                <p>No hay caja abierta para hoy</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <!-- Movimientos de Caja -->
                            <div class="card h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-stream"></i> Movimientos de Caja</h6>
                                    <div>
                                        @if($caja && $caja->estaAbierta())
                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#nuevoMovimientoModal">
                                            <i class="fas fa-plus me-1"></i> Nuevo Movimiento
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Hora</th>
                                                    <th>Tipo</th>
                                                    <th>Descripción</th>
                                                    <th class="text-end">Monto</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="movimientos-body">
                                                <!-- Los movimientos se cargarán dinámicamente con JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales (mantener los mismos modales de la implementación anterior) -->
<!-- ... -->

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para calcular y actualizar la duración de la caja abierta
    function actualizarDuracionCaja() {
        const duracionElement = document.getElementById('duracion-caja');
        if (duracionElement) {
            const fechaApertura = new Date('{{ $caja->fecha_apertura ?? null }}');
            if (fechaApertura) {
                const ahora = new Date();
                const diffMs = ahora - fechaApertura;
                const diffHrs = Math.floor(diffMs / 3600000);
                const diffMins = Math.floor((diffMs % 3600000) / 60000);
                
                duracionElement.textContent = `${diffHrs}h ${diffMins}m`;
            }
        }
    }
    
    // Actualizar duración cada minuto
    if (document.getElementById('duracion-caja')) {
        actualizarDuracionCaja();
        setInterval(actualizarDuracionCaja, 60000);
    }
    
    // Cargar movimientos de caja
    function cargarMovimientos() {
        fetch('{{ route("caja.movimientos") }}')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('movimientos-body');
                tbody.innerHTML = '';
                
                if (data.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                No hay movimientos registrados hoy
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                data.forEach(movimiento => {
                    const fecha = new Date(movimiento.created_at);
                    const hora = fecha.toLocaleTimeString('es-PE', { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });
                    
                    const tipoClass = movimiento.tipo === 'ingreso' ? 'text-success' : 'text-danger';
                    const tipoIcon = movimiento.tipo === 'ingreso' ? 'fa-arrow-up' : 'fa-arrow-down';
                    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${hora}</td>
                        <td><span class="${tipoClass}"><i class="fas ${tipoIcon} me-1"></i> ${movimiento.tipo}</span></td>
                        <td>${movimiento.descripcion}</td>
                        <td class="text-end ${tipoClass}">S/. ${parseFloat(movimiento.monto).toFixed(2)}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            })
            .catch(error => console.error('Error:', error));
    }
    
    // Cargar movimientos al iniciar
    cargarMovimientos();
    
    // Recargar movimientos cada 30 segundos si la caja está abierta
    @if($caja && $caja->estaAbierta())
    setInterval(cargarMovimientos, 30000);
    @endif
    
    // Inicializar tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
5. Actualización de Rutas (web.php)
php
<?php

use App\Http\Controllers\CajaController;
use Illuminate\Support\Facades\Route;

// Rutas para la gestión de caja
Route::prefix('caja')->group(function () {
    Route::get('/', [CajaController::class, 'index'])->name('caja.index');
    Route::post('/abrir', [CajaController::class, 'abrirCaja'])->name('caja.abrir');
    Route::post('/cerrar', [CajaController::class, 'cerrarCaja'])->name('caja.cerrar');
    Route::post('/movimiento', [CajaController::class, 'registrarMovimiento'])->name('caja.movimiento');
    Route::get('/movimientos', [CajaController::class, 'obtenerMovimientos'])->name('caja.movimientos');
    Route::post('/{caja}/cambiar-estado', [CajaController::class, 'cambiarEstado'])->name('caja.cambiar-estado');
    Route::get('/historial', [CajaController::class, 'historial'])->name('caja.historial');
});