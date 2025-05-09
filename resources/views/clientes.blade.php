@extends('layouts.app')



@section('title', 'Registrar Cliente')

@section('content_header')
    <h1>
        <i class="fas fa-user-plus"></i>  Registro de Nuevo Cliente
      
    </h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-ligh">
            <div class="card-header">
                <h3 class="card-title">Información Personal del Cliente</h3>
                
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            
            <div class="card-body">
                <form action= method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="document_type">Tipo de Documento</label>
                                <select class="form-control @error('document_type') is-invalid @enderror" id="document_type" name="document_type">
                                    <option value="">Seleccione...</option>
                                    <option value="DNI">DNI</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                    <option value="Cédula">Cédula</option>
                                    <option value="RUC">RUC</option>
                                </select>
                                @error('document_type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="document_number">Número de Documento</label>
                                <input type="text" class="form-control @error('document_number') is-invalid @enderror" id="document_number" name="document_number" placeholder="Ingrese número de documento">
                                @error('document_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name">Nombres</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" placeholder="Ingrese nombres">
                                @error('first_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name">Apellidos</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" placeholder="Ingrese apellidos">
                                @error('last_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Correo Electrónico</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="ejemplo@correo.com">
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Teléfono</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Ingrese número telefónico">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card card-ligh">
                <div class="card-header">
                    <h3 class="card-title">Dirección y Datos Adicionales</h3>
                    
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="address">Dirección</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="Ingrese dirección completa">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="city">Ciudad</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="Ingrese ciudad">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="postal_code">Código Postal</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="Código postal">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="birth_date">Fecha de Nacimiento</label>
                                <div class="input-group date" id="birth_date_picker" data-target-input="nearest">
                                    <div class="input-group-prepend" data-target="#birth_date_picker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fas fa-calendar"></i></div>
                                    </div>
                                    <input type="text" class="form-control datetimepicker-input" id="birth_date" name="birth_date" data-target="#birth_date_picker" placeholder="DD/MM/AAAA">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reference">Referencia</label>
                                <input type="text" class="form-control" id="reference" name="reference" placeholder="¿Cómo nos conoció?">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notas Adicionales</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Información adicional relevante..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card card-ligh">
                <div class="card-header">
                    <h3 class="card-title">Mascotas del Cliente</h3>
                    
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="callout callout-info">
                        <h5><i class="fas fa-info"></i> Nota:</h5>
                        <p>Después de registrar al cliente, podrá agregar sus mascotas desde la ficha de cliente.</p>
                    </div>
                    
                    <div class="text-center">
                        <button type="button" class="btn btn-primary btn-lg" disabled>
                            <i class="fas fa-paw"></i> Registrar Mascotas
                        </button>
                        <p class="text-muted mt-2">Esta opción estará disponible después de guardar el cliente</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <a href= class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar Cliente
                </button>
            </div>
        </div>
    </div>
</form>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css">
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>
    
    <script>
        $(function () {
            // Inicializar DateTimePicker para fecha de nacimiento
            $('#birth_date_picker').datetimepicker({
                format: 'L',
                locale: 'es'
            });
            
            // Validaciones del lado del cliente
            $('#document_number').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            
            $('#phone').on('input', function() {
                this.value = this.value.replace(/[^0-9+\-\s]/g, '');
            });
        });
    </script>
@stop