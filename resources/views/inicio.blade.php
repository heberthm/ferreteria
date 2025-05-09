@extends('layouts.app')

@section('content')
    <style>
        .main-content {
            padding: 10px;
        }

        .card-dashboard {
            border-left: 4px solid;
            transition: transform 0.3s;
        }

        .card-dashboard:hover {
            transform: translateY(-5px);
        }

        .card-dashboard.primary {
            border-left-color: #0d6efd;
        }

        .card-dashboard.success {
            border-left-color: #198754;
        }

        .card-dashboard.warning {
            border-left-color: #ffc107;
        }

        .card-dashboard.danger {
            border-left-color: #dc3545;
        }

        .card-dashboard .card-icon {
            font-size: 2rem;
            opacity: 0.8;
        }
    </style>




    <div class="container">

        <!-- Contenido principal -->
        <div class="col-md-12 main-content">
            <div class="tab-content">

                <!-- Dashboard -->
                <div class="tab-pane fade show active" id="dashboard">
                    <h2 class="mb-4">Dashboard</h2>

                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <div class="card card-dashboard primary">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-1 text-muted">Citas Hoy</h6>
                                        <h3 class="card-title mb-0">12</h3>
                                    </div>
                                    <div class="card-icon text-primary">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card card-dashboard success">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-1 text-muted">Ventas del Día</h6>
                                        <h3 class="card-title mb-0">$825.00</h3>
                                    </div>
                                    <div class="card-icon text-success">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card card-dashboard warning">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-1 text-muted">Pacientes</h6>
                                        <h3 class="card-title mb-0">184</h3>
                                    </div>
                                    <div class="card-icon text-warning">
                                        <i class="fas fa-paw"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card card-dashboard danger">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-1 text-muted">Alertas Stock</h6>
                                        <h3 class="card-title mb-0">7</h3>
                                    </div>
                                    <div class="card-icon text-danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fa fa-calendar" aria-hidden="true"></i> Próximas Citas
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Hora</th>
                                                    <th>Paciente</th>
                                                    <th>Dueño</th>
                                                    <th>Tipo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>09:00</td>
                                                    <td>Max</td>
                                                    <td>María Gómez</td>
                                                    <td><span class="badge bg-info">Consulta</span></td>
                                                </tr>
                                                <tr>
                                                    <td>10:30</td>
                                                    <td>Luna</td>
                                                    <td>Pedro López</td>
                                                    <td><span class="badge bg-warning">Vacunación</span></td>
                                                </tr>
                                                <tr>
                                                    <td>11:15</td>
                                                    <td>Rocky</td>
                                                    <td>Ana Torres</td>
                                                    <td><span class="badge bg-danger">Cirugía</span></td>
                                                </tr>
                                                <tr>
                                                    <td>14:00</td>
                                                    <td>Michi</td>
                                                    <td>Carlos Ruiz</td>
                                                    <td><span class="badge bg-success">Control</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fa fa-arrow-circle-down" aria-hidden="true"></i> Productos con Stock Bajo
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>Categoría</th>
                                                    <th>Stock</th>
                                                    <th>Mínimo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Antibiótico XYZ</td>
                                                    <td>Medicamentos</td>
                                                    <td><span class="text-danger">3</span></td>
                                                    <td>10</td>
                                                </tr>
                                                <tr>
                                                    <td>Alimento Premium</td>
                                                    <td>Alimentos</td>
                                                    <td><span class="text-danger">2</span></td>
                                                    <td>5</td>
                                                </tr>
                                                <tr>
                                                    <td>Jeringas 5ml</td>
                                                    <td>Insumos</td>
                                                    <td><span class="text-warning">8</span></td>
                                                    <td>15</td>
                                                </tr>
                                                <tr>
                                                    <td>Shampoo Medicado</td>
                                                    <td>Higiene</td>
                                                    <td><span class="text-warning">4</span></td>
                                                    <td>6</td>
                                                </tr>
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

   
@endsection
