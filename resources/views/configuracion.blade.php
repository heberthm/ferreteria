{{-- resources/views/configuracion/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Configuración del Sistema')

@section('css')
    <style>
        .config-card {
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .config-card:hover {
            transform: translateY(-2px);
            transition: all 0.3s;
        }
        .nav-tabs .nav-link {
            color: #495057;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            color: #007bff;
            border-bottom: 2px solid #007bff;
        }
        .form-group label {
            font-weight: 500;
        }
        .required:after {
            content: " *";
            color: red;
        }
        /* Eliminar espacio extra */
        .content-wrapper {
            min-height: auto !important;
        }
        .main-footer {
            position: relative;
            margin-top: 0;
        }
        .tab-content {
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .container-fluid {
            padding-bottom: 0;
        }
    </style>
@stop

@section('content')
<br>
<div class="container-fluid" style="padding-bottom: 0;">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title">
                        <i class="fas fa-cogs"></i> Configuración del Sistema
                    </h1>
                </div>
                <div class="card-body" style="padding-bottom: 0;">
                    <!-- Navegación por Tabs -->
                    <ul class="nav nav-tabs" id="configTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">
                                <i class="fas fa-building"></i> General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="facturacion-tab" data-toggle="tab" href="#facturacion" role="tab">
                                <i class="fas fa-file-invoice-dollar"></i> Facturación
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="usuarios-tab" data-toggle="tab" href="#usuarios" role="tab">
                                <i class="fas fa-users"></i> Usuarios/Roles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="perfil-tab" data-toggle="tab" href="#perfil" role="tab">
                                <i class="fas fa-user-circle"></i> Mi Perfil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="negocio-tab" data-toggle="tab" href="#negocio" role="tab">
                                <i class="fas fa-store"></i> Datos del Negocio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="impuestos-tab" data-toggle="tab" href="#impuestos" role="tab">
                                <i class="fas fa-percent"></i> Impuestos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="alertas-tab" data-toggle="tab" href="#alertas" role="tab">
                                <i class="fas fa-bell"></i> Alertas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="backup-tab" data-toggle="tab" href="#backup" role="tab">
                                <i class="fas fa-database"></i> Respaldo
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" style="padding-bottom: 0;">
                        <!-- Tab General -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="card config-card">
                                <div class="card-header bg-primary text-white">
                                    <h3 class="card-title">Configuración General del Sistema</h3>
                                </div>
                                <div class="card-body">
                                    <form id="formConfigGeneral">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="required">Nombre del Sistema</label>
                                                    <input type="text" name="nombre_sistema" id="nombre_sistema" class="form-control" value="" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Versión</label>
                                                    <input type="text" name="version" id="version" class="form-control" value="1.0.0" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Zona Horaria</label>
                                                    <select name="zona_horaria" id="zona_horaria" class="form-control">
                                                        <option value="America/Bogota">America/Bogota</option>
                                                        <option value="America/Mexico_City">America/Mexico_City</option>
                                                        <option value="America/Lima">America/Lima</option>
                                                        <option value="America/Santiago">America/Santiago</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Formato de Fecha</label>
                                                    <select name="formato_fecha" id="formato_fecha" class="form-control">
                                                        <option value="d/m/Y">DD/MM/YYYY</option>
                                                        <option value="m/d/Y">MM/DD/YYYY</option>
                                                        <option value="Y-m-d">YYYY-MM-DD</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Moneda</label>
                                                    <select name="moneda" id="moneda" class="form-control">
                                                        <option value="COP">Peso Colombiano (COP)</option>
                                                        <option value="USD">Dólar Americano (USD)</option>
                                                        <option value="MXN">Peso Mexicano (MXN)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Símbolo de Moneda</label>
                                                    <input type="text" name="simbolo_moneda" id="simbolo_moneda" class="form-control" value="$">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Facturación -->
                        <div class="tab-pane fade" id="facturacion" role="tabpanel">
                            <div class="card config-card">
                                <div class="card-header bg-success text-white">
                                    <h3 class="card-title">Configuración de Facturación</h3>
                                </div>
                                <div class="card-body">
                                    <form id="formFacturacion">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="required">Prefijo Factura</label>
                                                    <input type="text" name="prefijo_factura" id="prefijo_factura" class="form-control" value="FAC">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="required">Consecutivo Inicial</label>
                                                    <input type="number" name="consecutivo_inicial" id="consecutivo_inicial" class="form-control" value="1" min="1">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="required">Consecutivo Actual</label>
                                                    <input type="number" name="consecutivo_actual" id="consecutivo_actual" class="form-control" value="1" min="1" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Próximo Número</label>
                                                    <input type="number" name="proximo_numero" id="proximo_numero" class="form-control" value="1" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Longitud del Número</label>
                                                    <select name="longitud_numero" id="longitud_numero" class="form-control">
                                                        <option value="6">6 dígitos (000001)</option>
                                                        <option value="8">8 dígitos (00000001)</option>
                                                        <option value="10">10 dígitos (0000000001)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Formato de Factura</label>
                                                    <select name="formato_factura" id="formato_factura" class="form-control">
                                                        <option value="simple">Simple</option>
                                                        <option value="detallada">Detallada</option>
                                                        <option value="profesional">Profesional</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="autogenerar" name="autogenerar" checked>
                                                        <label class="custom-control-label" for="autogenerar">Auto-generar consecutivo automáticamente</label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="validar_duplicados" name="validar_duplicados" checked>
                                                        <label class="custom-control-label" for="validar_duplicados">Validar números duplicados</label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="factura_electronica" name="factura_electronica">
                                                        <label class="custom-control-label" for="factura_electronica">Habilitar facturación electrónica</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-warning" onclick="reiniciarConsecutivo()">
                                                    <i class="fas fa-sync-alt"></i> Reiniciar Consecutivo
                                                </button>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <button type="submit" class="btn btn-success">Guardar Configuración</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Formatos de Impresión -->
                            <div class="card config-card">
                                <div class="card-header bg-info text-white">
                                    <h3 class="card-title">Formatos de Impresión</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Tamaño de Papel</label>
                                                <select name="tamaño_papel" id="tamaño_papel" class="form-control">
                                                    <option value="thermal">Térmico (80mm)</option>
                                                    <option value="carta">Carta</option>
                                                    <option value="a4">A4</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Copias a Imprimir</label>
                                                <select name="copias" id="copias" class="form-control">
                                                    <option value="1">1 copia</option>
                                                    <option value="2">2 copias</option>
                                                    <option value="3">3 copias</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Logo en Factura</label>
                                                <input type="file" name="logo_factura" id="logo_factura" class="form-control-file" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Usuarios y Roles -->
                        <div class="tab-pane fade" id="usuarios" role="tabpanel">
                            <div class="card config-card">
                                <div class="card-header bg-warning text-white">
                                    <h3 class="card-title">Gestión de Usuarios y Roles</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalNuevoUsuario">
                                                <i class="fas fa-user-plus"></i> Nuevo Usuario
                                            </button>
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalNuevoRol">
                                                <i class="fas fa-tag"></i> Nuevo Rol
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Lista de Usuarios -->
                                        <div class="col-md-6">
                                            <h4><i class="fas fa-users"></i> Usuarios del Sistema</h4>
                                            <div class="table-responsive">
                                                <table class="table table-hover" id="tablaUsuarios">
                                                    <thead>
                                                        <tr>
                                                            <th>Usuario</th>
                                                            <th>Email</th>
                                                            <th>Rol</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($usuarios as $usuario)
                                                        <tr>
                                                            <td>{{ $usuario->name }}</td>
                                                            <td>{{ $usuario->email }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $usuario->rol == 'Administrador' ? 'danger' : ($usuario->rol == 'Vendedor' ? 'info' : 'warning') }}">
                                                                    {{ $usuario->rol ?? 'Sin rol' }}
                                                                </span>
                                                            </td>
                                                            <td class="text-nowrap">
                                                                <button class="btn btn-xs btn-info btn-editar-usuario" title="Editar usuario" data-id="{{ $usuario->id }}" style="padding: 0.2rem 0.3rem; font-size: 0.75rem;">
                                                                    <i class="fas fa-edit" style="font-size: 0.7rem;"></i>
                                                                </button>
                                                                @if($usuario->id != auth()->id())
                                                                <button class="btn btn-xs btn-danger btn-eliminar-usuario" title="Eliminar usuario" data-id="{{ $usuario->id }}" style="padding: 0.2rem 0.3rem; font-size: 0.75rem;">
                                                                    <i class="fas fa-trash" style="font-size: 0.7rem;"></i>
                                                                </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        
                                        <!-- Lista de Roles -->
                                        <div class="col-md-6">
                                            <h4><i class="fas fa-tags"></i> Roles del Sistema</h4>
                                            <div class="table-responsive">
                                                <table class="table table-hover" id="tablaRoles">
                                                    <thead>
                                                        <tr>
                                                            <th>Rol</th>
                                                            <th>Descripción</th>
                                                            <th>Usuarios</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Administrador</td>
                                                            <td>Acceso total al sistema</td>
                                                            <td>1</td>
                                                            <td class="text-nowrap">
                                                                <button class="btn btn-xs btn-info" title="Editar rol">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button class="btn btn-xs btn-primary" title="Permisos del rol">
                                                                    <i class="fas fa-key"></i>
                                                                </button>
                                                            </td>
                                                              
                                                        </tr>
                                                        <tr>
                                                            <td>Almacenista</td>
                                                            <td>Gestión de inventario</td>
                                                            <td>0</td>
                                                            <td class="text-nowrap">
                                                                <button class="btn btn-xs btn-info" title="Editar rol">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button class="btn btn-xs btn-primary" title="Permisos del rol">
                                                                    <i class="fas fa-key"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Mi Perfil -->
                        <div class="tab-pane fade" id="perfil" role="tabpanel">
                            <div class="card config-card">
                                <div class="card-header bg-info text-white">
                                    <h3 class="card-title">Mi Perfil de Usuario</h3>
                                </div>
                                <div class="card-body">
                                    <form id="formPerfil">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3 text-center">
                                                <div class="profile-picture mb-3">
                                                    <img src="{{ asset('img/default-avatar.png') }}" alt="Avatar" class="img-circle img-fluid" width="120" id="avatarPreview">
                                                    <div class="mt-2">
                                                        <input type="file" name="avatar" id="avatar" accept="image/*" style="display:none;">
                                                        <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('avatar').click()">
                                                            <i class="fas fa-camera"></i> Cambiar Foto
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Nombre de Usuario</label>
                                                            <input type="text" name="username" class="form-control" value="{{ auth()->user()->name }}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Email</label>
                                                            <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Nombre Completo</label>
                                                            <input type="text" name="nombre_completo" class="form-control" value="{{ auth()->user()->name }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Teléfono</label>
                                                            <input type="text" name="telefono" class="form-control" value="{{ auth()->user()->telefono ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <hr>
                                                        <h5>Cambiar Contraseña</h5>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Contraseña Actual</label>
                                                            <input type="password" name="password_actual" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Nueva Contraseña</label>
                                                            <input type="password" name="password_nueva" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Confirmar Contraseña</label>
                                                            <input type="password" name="password_confirmacion" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Resto de tabs (Negocio, Impuestos, Alertas, Backup) se mantienen igual -->
                        <!-- Tab Datos del Negocio -->
                        <div class="tab-pane fade" id="negocio" role="tabpanel">
                            <div class="card config-card">
                                <div class="card-header bg-secondary text-white">
                                    <h3 class="card-title">Información de la Ferretería</h3>
                                </div>
                                <div class="card-body">
                                    <form id="formNegocio">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="required">Nombre del Negocio</label>
                                                    <input type="text" name="nombre_negocio" class="form-control" value="Ferretería El Martillo" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>NIT / RUC</label>
                                                    <input type="text" name="nit" class="form-control" value="900.000.000-1">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Dirección</label>
                                                    <input type="text" name="direccion" class="form-control" value="Calle Principal #123">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Teléfono</label>
                                                    <input type="text" name="telefono_negocio" class="form-control" value="(601) 123-4567">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="email" name="email_negocio" class="form-control" value="info@ferreteria.com">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Sitio Web</label>
                                                    <input type="url" name="website" class="form-control" value="www.ferreteria.com">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Mensaje en Factura</label>
                                                    <textarea name="mensaje_factura" class="form-control" rows="3">Gracias por su compra. ¡Vuelva pronto!</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Logo del Negocio</label>
                                                    <input type="file" name="logo_negocio" class="form-control-file" accept="image/*">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">Guardar Información</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Impuestos -->
                        <div class="tab-pane fade" id="impuestos" role="tabpanel">
                            <div class="card config-card">
                                <div class="card-header bg-danger text-white">
                                    <h3 class="card-title">Configuración de Impuestos</h3>
                                </div>
                                <div class="card-body">
                                    <form id="formImpuestos">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>IVA (%)</label>
                                                    <input type="number" name="iva" class="form-control" value="19" step="0.01" min="0" max="100">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="incluir_iva" name="incluir_iva" checked>
                                                        <label class="custom-control-label" for="incluir_iva">Incluir IVA en precios</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="mostrar_iva" name="mostrar_iva" checked>
                                                        <label class="custom-control-label" for="mostrar_iva">Mostrar IVA en factura</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Alertas -->
                        <div class="tab-pane fade" id="alertas" role="tabpanel">
                            <div class="card config-card">
                                <div class="card-header bg-warning text-white">
                                    <h3 class="card-title">Configuración de Alertas</h3>
                                </div>
                                <div class="card-body">
                                    <form id="formAlertas">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Stock Mínimo de Alerta</label>
                                                    <input type="number" name="stock_minimo_alerta" class="form-control" value="5">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="alertar_stock" name="alertar_stock" checked>
                                                        <label class="custom-control-label" for="alertar_stock">Alertar sobre stock bajo</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="alertar_vencimiento" name="alertar_vencimiento">
                                                        <label class="custom-control-label" for="alertar_vencimiento">Alertar sobre productos próximos a vencer</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Días de anticipación para vencimiento</label>
                                                    <input type="number" name="dias_vencimiento" class="form-control" value="30">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Respaldo -->
                        <div class="tab-pane fade" id="backup" role="tabpanel">
                            <div class="card config-card">
                                <div class="card-header bg-dark text-white">
                                    <h3 class="card-title">Respaldo de la Base de Datos</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i> Realice respaldos periódicos de su base de datos para prevenir pérdida de información.
                                            </div>
                                            <button type="button" class="btn btn-success btn-lg btn-block" onclick="crearBackup()">
                                                <i class="fas fa-database"></i> Crear Respaldo Ahora
                                            </button>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4>Respaldos Recientes</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead>
                                                                <tr><th>Archivo</th><th>Fecha</th><th>Tamaño</th><th>Acción</th></tr>
                                                            </thead>
                                                            <tbody id="listaBackups">
                                                                <tr>
                                                                    <td colspan="4" class="text-center">No hay respaldos disponibles</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="backup_automatico" name="backup_automatico">
                                                    <label class="custom-control-label" for="backup_automatico">Habilitar respaldo automático diario</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Hora del respaldo automático</label>
                                                <input type="time" name="hora_backup" class="form-control" value="02:00">
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
    </div>
</div>

<!-- Modales -->
<!-- Modal Nuevo Usuario -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Nuevo Usuario</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formNuevoUsuario">
                    <div class="form-group">
                        <label>Usuario</label>
                        <input type="text" name="usuario" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Rol</label>
                        <select name="rol" class="form-control">
                            <option value="vendedor">Vendedor</option>
                            <option value="almacenista">Almacenista</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarUsuario()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Rol -->
<div class="modal fade" id="modalNuevoRol" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Nuevo Rol</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formNuevoRol">
                    <div class="form-group">
                        <label>Nombre del Rol</label>
                        <input type="text" name="nombre_rol" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Permisos</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="perm_ventas">
                                    <label class="custom-control-label" for="perm_ventas">Ventas</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="perm_compras">
                                    <label class="custom-control-label" for="perm_compras">Compras</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="perm_inventario">
                                    <label class="custom-control-label" for="perm_inventario">Inventario</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="perm_clientes">
                                    <label class="custom-control-label" for="perm_clientes">Clientes</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="perm_reportes">
                                    <label class="custom-control-label" for="perm_reportes">Reportes</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="perm_configuracion">
                                    <label class="custom-control-label" for="perm_configuracion">Configuración</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarRol()">Guardar</button>
            </div>
        </div>
    </div>
</div>
@stop

@push('js')
<script>

// Configuración global de Toastr para evitar duplicados
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": true,        // ✅ Evita duplicados
    "preventOpenDuplicates": true,    // ✅ Evita abrir duplicados
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};



// Cargar configuraciones guardadas ´
function cargarConfiguraciones() {
    $.ajax({
        url: '/configuracion/cargar-configuraciones',
        type: 'GET',
        success: function(configs) {
            // Recorrer cada grupo (general, facturacion, negocio, etc.)
            $.each(configs, function(grupo, items) {
                // Recorrer cada clave-valor dentro del grupo
                $.each(items, function(clave, valor) {
                    const $el = $('#' + clave);
                    if ($el.length) {
                        if ($el.is(':checkbox')) {
                            $el.prop('checked', valor === '1');
                        } else {
                            $el.val(valor);
                        }
                    }
                });
            });
            
            // Cargar configuraciones de facturación
            if (configs.facturacion) {
                $.each(configs.facturacion, function(clave, valor) {
                    if (clave === 'autogenerar' && valor === '1') 
                        $('#autogenerar').prop('checked', true);
                    else if (clave === 'validar_duplicados' && valor === '1')
                        $('#validar_duplicados').prop('checked', true);
                    else if (clave === 'factura_electronica' && valor === '1')
                        $('#factura_electronica').prop('checked', true);
                    else if (clave === 'tamaño_papel')
                        $(`#${clave}`).val(valor);
                    else if (clave === 'copias')
                        $(`#${clave}`).val(valor);
                    else if ($('#' + clave).length)
                        $(`#${clave}`).val(valor);
                });
            }
            
            // Cargar configuraciones del negocio
            if (configs.negocio) {
                $.each(configs.negocio, function(clave, valor) {
                    if (clave !== 'logo_negocio') {
                        $(`[name="${clave}"]`).val(valor);
                    }
                });
            }
            
            // Cargar configuraciones de impuestos
            if (configs.impuestos) {
                $.each(configs.impuestos, function(clave, valor) {
                    if (clave === 'incluir_iva' && valor === '1')
                        $('#incluir_iva').prop('checked', true);
                    else if (clave === 'mostrar_iva' && valor === '1')
                        $('#mostrar_iva').prop('checked', true);
                    else if ($(`[name="${clave}"]`).length)
                        $(`[name="${clave}"]`).val(valor);
                });
            }
            
            // Cargar configuraciones de alertas
            if (configs.alertas) {
                $.each(configs.alertas, function(clave, valor) {
                    if (clave === 'alertar_stock' && valor === '1')
                        $('#alertar_stock').prop('checked', true);
                    else if (clave === 'alertar_vencimiento' && valor === '1')
                        $('#alertar_vencimiento').prop('checked', true);
                    else if ($(`[name="${clave}"]`).length)
                        $(`[name="${clave}"]`).val(valor);
                });
            }
        },
        error: function(xhr) {
            console.error('Error al cargar configuraciones:', xhr.responseText);
        }
    });
}

// Guardar configuraciones
$('#formConfigGeneral').on('submit', function(e) {
    e.preventDefault();
    let formData = $(this).serialize();
    
    $.ajax({
        url: 'configuracion/guardar-general',
        type: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            toastr.success(response.message);
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Error al guardar');
        }
    });
});

// En tu archivo Blade/JS, actualiza las URLs:

function cargarConfiguracionFacturacion() {
    $.ajax({
        url: 'configuracion/facturacion',  // ✅ GET - para cargar
        type: 'GET',
        success: function(response) {
            // ... código existente
        },
        error: function(xhr) {
            console.error('Error al cargar configuración de facturación:', xhr.responseText);
        }
    });
}

let isSubmitting = false;

$('#formFacturacion').off('submit').on('submit', function(e) {
    e.preventDefault();
    
    // Evitar envíos múltiples
    if (isSubmitting) {
        toastr.warning('Ya se está procesando la solicitud', 'Espere');
        return false;
    }
    
    isSubmitting = true;
    
    let formData = new FormData(this);
    let submitBtn = $(this).find('button[type="submit"]');
    let originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-pulse"></i> Guardando...');
    
    $.ajax({
        url: '/configuracion/guardar-facturacion',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                // Limpiar toasts anteriores antes de mostrar uno nuevo
                toastr.clear();
                toastr.success(response.message, 'Éxito');
            }
        },
        error: function(xhr) {
            toastr.clear();
            toastr.error('Error al guardar', 'Error');
        },
        complete: function() {
            isSubmitting = false;
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
});


// Para incrementar consecutivo
function incrementarConsecutivo() {
    $.ajax({
        url: '/configuracion/facturacion/incrementar',  // ✅ Nueva URL
        type: 'POST',
        data: { _token: $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            toastr.success('Consecutivo incrementado');
        }
    });
}

$('#formFacturacion').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    
    $.ajax({
        url: '/configuracion/facturacion',
        type: 'GET',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            toastr.success(response.message);
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Error al guardar');
        }
    });
});

$('#formPerfil').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    
    $.ajax({
        url: '/configuracion/actualizar-perfil',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            toastr.success(response.message);
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Error al actualizar');
        }
    });
});

$('#formNegocio').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    
    $.ajax({
        url: '/configuracion/negocio',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            toastr.success(response.message);
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Error al guardar');
        }
    });
});

$('#formImpuestos').on('submit', function(e) {
    e.preventDefault();
    let formData = $(this).serialize();
    
    $.ajax({
        url: '/configuracion/impuestos',
        type: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            toastr.success(response.message);
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Error al guardar');
        }
    });
});

$('#formAlertas').on('submit', function(e) {
    e.preventDefault();
    let formData = $(this).serialize();
    
    $.ajax({
        url: '/configuracion/alertas',
        type: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            toastr.success(response.message);
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Error al guardar');
        }
    });
});

// Funciones actualizadas
function reiniciarConsecutivo() {
    if(confirm('¿Está seguro de reiniciar el consecutivo? Esto puede causar duplicados.')) {
        $.ajax({
            url: '/configuracion/reiniciar-consecutivo',
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                $('#consecutivo_actual').val(1);
                $('#proximo_numero').val(1);
                toastr.success(response.message);
            },
            error: function() {
                toastr.error('Error al reiniciar el consecutivo');
            }
        });
    }
}

function crearBackup() {
    let btn = $('button[onclick="crearBackup()"]');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-pulse"></i> Creando respaldo...');
    
    $.ajax({
        url: '/configuracion/crear-backup',
        type: 'POST',
        data: { _token: $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            toastr.success(response.message);
            cargarListaBackups();
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Error al crear el respaldo');
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="fas fa-database"></i> Crear Respaldo Ahora');
        }
    });
}

function cargarListaBackups() {
    $.ajax({
        url: '/configuracion/listar-backups',
        type: 'GET',
        success: function(backups) {
            let tbody = $('#listaBackups');
            if (backups.length === 0) {
                tbody.html('<tr><td colspan="4" class="text-center">No hay respaldos disponibles</td></tr>');
                return;
            }
            
            let html = '';
            backups.forEach(backup => {
                html += `
                    <tr>
                        <td>${backup.name}</td>
                        <td>${backup.date}</td>
                        <td>${backup.size} KB</td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="descargarBackup('${backup.name}')">
                                <i class="fas fa-download"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            tbody.html(html);
        }
    });
}

function descargarBackup(filename) {
    window.location.href = `/storage/backups/${filename}`;
}

function guardarUsuario() {
    let formData = $('#formNuevoUsuario').serialize();
    formData += '&_token=' + $('meta[name="csrf-token"]').attr('content');
    
    $.ajax({
        url: '/configuracion/guardar-usuario',
        type: 'POST',
        data: formData,
        success: function(response) {
            toastr.success(response.message);
            $('#modalNuevoUsuario').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Error al guardar usuario');
        }
    });
}

function guardarRol() {
    let formData = $('#formNuevoRol').serialize();
    formData += '&_token=' + $('meta[name="csrf-token"]').attr('content');
    
    $.ajax({
        url: '/configuracion/guardar-rol',
        type: 'POST',
        data: formData,
        success: function(response) {
            toastr.success(response.message);
            $('#modalNuevoRol').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Error al guardar rol');
        }
    });
}

// Función debounce para búsqueda
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Cargar usuarios y roles
function cargarUsuariosRoles(filtros = {}) {
    $.ajax({
        url: '/listar-usuarios',
        type: 'GET',
        data: filtros,
        success: function(response) {
            if (!response.success) return;
            renderTablaUsuarios(response.usuarios);
        },
        error: function(xhr) {
            toastr.error('Error al cargar usuarios');
        }
    });
}

function renderTablaUsuarios(usuarios) {
    // Implementar según necesidad
    console.log('Usuarios:', usuarios);
}

// Inicializar
$(document).ready(function() {
    cargarConfiguraciones();
    cargarListaBackups();
    
    // Actualizar próximo número
    $('#consecutivo_actual').on('change', function() {
        let longitud = $('#longitud_numero').val();
        let numero = parseInt($(this).val());
        let proximo = numero + 1;
        let padded = proximo.toString().padStart(longitud, '0');
        $('#proximo_numero').val(padded);
    });
    
    $('#longitud_numero').on('change', function() {
        let longitud = $(this).val();
        let actual = $('#consecutivo_actual').val();
        let padded = actual.toString().padStart(longitud, '0');
        $('#consecutivo_actual').val(padded);
    });
    
    // Vista previa de avatar
    $('#avatar').on('change', function(e) {
        const file = e.target.files[0];
        if(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#avatarPreview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
});

</script>
@endpush
