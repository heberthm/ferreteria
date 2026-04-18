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
                                                    <input type="text" name="nombre_sistema" id="nombre_sistema" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Versión</label>
                                                    <input type="text" name="version" id="version" class="form-control" readonly>
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
                                                    <input type="text" name="simbolo_moneda" id="simbolo_moneda" class="form-control">
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
                                                    <input type="text" name="prefijo_factura" id="prefijo_factura" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="required">Consecutivo Inicial</label>
                                                    <input type="number" name="consecutivo_inicial" id="consecutivo_inicial" class="form-control" min="1">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="required">Consecutivo Actual</label>
                                                    <input type="number" name="consecutivo_actual" id="consecutivo_actual" class="form-control" min="1" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Próximo Número</label>
                                                    <input type="number" name="proximo_numero" id="proximo_numero" class="form-control" readonly>
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
                                                        <input type="checkbox" class="custom-control-input" id="autogenerar" name="autogenerar">
                                                        <label class="custom-control-label" for="autogenerar">Auto-generar consecutivo automáticamente</label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="validar_duplicados" name="validar_duplicados">
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
                                                @php $usuario = auth()->user(); @endphp
                                                @if($usuario->avatar)
                                                    <img src="{{ Storage::url($usuario->avatar) }}" alt="Avatar" class="img-circle img-fluid" width="120" id="avatarPreview">
                                                @else
                                                    <div style="width: 120px; height: 120px; border-radius: 50%; background-color: #007bff; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; margin: 0 auto;">
                                                        {{ strtoupper(substr($usuario->name, 0, 1)) }}
                                                    </div>
                                                @endif
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
                                                        <input type="text" name="username" class="form-control" value="{{ $usuario->name }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Email</label>
                                                        <input type="email" name="email" class="form-control" value="{{ $usuario->email }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Nombre Completo</label>
                                                        <input type="text" name="nombre_completo" class="form-control" value="{{ $usuario->name }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Teléfono</label>
                                                        <input type="text" name="telefono" class="form-control" value="{{ $usuario->telefono ?? '' }}">
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
                                                    <input type="text" name="nombre_negocio" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>NIT / RUC</label>
                                                    <input type="text" name="nit" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Dirección</label>
                                                    <input type="text" name="direccion" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Teléfono</label>
                                                    <input type="text" name="telefono_negocio" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="email" name="email_negocio" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Sitio Web</label>
                                                    <input type="url" name="website" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Mensaje en Factura</label>
                                                    <textarea name="mensaje_factura" class="form-control" rows="3"></textarea>
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
                                                    <input type="number" name="iva" class="form-control" step="0.01" min="0" max="100">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="incluir_iva" name="incluir_iva">
                                                        <label class="custom-control-label" for="incluir_iva">Incluir IVA en precios</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="mostrar_iva" name="mostrar_iva">
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
                                                    <input type="number" name="stock_minimo_alerta" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="alertar_stock" name="alertar_stock">
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
                                                    <input type="number" name="dias_vencimiento" class="form-control">
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
                                                <input type="time" name="hora_backup" class="form-control">
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
    "preventDuplicates": true,
    "preventOpenDuplicates": true,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

 // Cargar todas las configuraciones
    cargarConfiguraciones();
    
    // Cargar datos del usuario actual
    cargarDatosUsuario();
    
    // Cargar lista de backups
    cargarListaBackups();
    
    // Actualizar próximo número cuando cambie el consecutivo actual
    $('#consecutivo_actual').on('change', function() {
        let longitud = $('#longitud_numero').val();
        let numero = parseInt($(this).val());
        if (!isNaN(numero)) {
            let proximo = numero + 1;
            let padded = proximo.toString().padStart(parseInt(longitud), '0');
            $('#proximo_numero').val(padded);
        }
    });
    
    // Actualizar cuando cambie la longitud del número
    $('#longitud_numero').on('change', function() {
        let longitud = parseInt($(this).val());
        let actual = $('#consecutivo_actual').val();
        if (actual && !isNaN(parseInt(actual))) {
            let padded = parseInt(actual).toString().padStart(longitud, '0');
            $('#consecutivo_actual').val(padded);
        }
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

// Cargar configuraciones guardadas desde la base de datos
function cargarConfiguraciones() {
    console.log('Cargando configuraciones desde la base de datos...');
    
    $.ajax({
        url: '/configuracion/cargar-configuraciones',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Configuraciones recibidas:', response);
            
            // Cargar configuración general
            if (response.general) {
                $('#nombre_sistema').val(response.general.nombre_sistema || '');
                $('#version').val(response.general.version || '1.0.0');
                $('#zona_horaria').val(response.general.zona_horaria || 'America/Bogota');
                $('#formato_fecha').val(response.general.formato_fecha || 'd/m/Y');
                $('#moneda').val(response.general.moneda || 'COP');
                $('#simbolo_moneda').val(response.general.simbolo_moneda || '$');
            }
            
            // Cargar configuración de facturación
            if (response.facturacion) {
                $('#prefijo_factura').val(response.facturacion.prefijo_factura || '');
                $('#consecutivo_inicial').val(response.facturacion.consecutivo_inicial || '');
                $('#consecutivo_actual').val(response.facturacion.consecutivo_actual || '');
                $('#proximo_numero').val(response.facturacion.proximo_numero || '');
                $('#longitud_numero').val(response.facturacion.longitud_numero || '6');
                $('#formato_factura').val(response.facturacion.formato_factura || 'simple');
                $('#tamaño_papel').val(response.facturacion.tamaño_papel || 'thermal');
                $('#copias').val(response.facturacion.copias || '1');
                
                // Checkboxes
                $('#autogenerar').prop('checked', response.facturacion.autogenerar === '1');
                $('#validar_duplicados').prop('checked', response.facturacion.validar_duplicados === '1');
                $('#factura_electronica').prop('checked', response.facturacion.factura_electronica === '1');
            }
            
            // Cargar datos del negocio
            if (response.negocio) {
                $('[name="nombre_negocio"]').val(response.negocio.nombre_negocio || '');
                $('[name="nit"]').val(response.negocio.nit || '');
                $('[name="direccion"]').val(response.negocio.direccion || '');
                $('[name="telefono_negocio"]').val(response.negocio.telefono_negocio || '');
                $('[name="email_negocio"]').val(response.negocio.email_negocio || '');
                $('[name="website"]').val(response.negocio.website || '');
                $('[name="mensaje_factura"]').val(response.negocio.mensaje_factura || '');
            }
            
            // Cargar configuración de impuestos
            if (response.impuestos) {
                $('[name="iva"]').val(response.impuestos.iva || '');
                $('#incluir_iva').prop('checked', response.impuestos.incluir_iva === '1');
                $('#mostrar_iva').prop('checked', response.impuestos.mostrar_iva === '1');
            }
            
            // Cargar configuración de alertas
            if (response.alertas) {
                $('[name="stock_minimo_alerta"]').val(response.alertas.stock_minimo_alerta || '');
                $('[name="dias_vencimiento"]').val(response.alertas.dias_vencimiento || '');
                $('#alertar_stock').prop('checked', response.alertas.alertar_stock === '1');
                $('#alertar_vencimiento').prop('checked', response.alertas.alertar_vencimiento === '1');
            }
            
            toastr.success('Configuraciones cargadas correctamente');
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar configuraciones:', error);
            console.error('Respuesta del servidor:', xhr.responseText);
            toastr.error('Error al cargar las configuraciones desde la base de datos');
        }
    });
}

function cargarDatosUsuario() {
    console.log('Cargando datos del usuario...');
    
     // ✅ Obtener el token CSRF del meta tag
    const token = $('meta[name="csrf-token"]').attr('content');
     // Mostrar indicador de carga
    $('#formPerfil').find('input, button[type="submit"]').prop('disabled', true);
    
    $.ajax({
        url: '/configuracion/usuario-actual',
        type: 'GET',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        success: function(response) {
            console.log('✅ Respuesta exitosa:', response);
            
            if (response && response.success === true && response.user) {
                const user = response.user;
                
                // Asignar valores
                $('[name="username"]').val(user.name || '');
                $('[name="email"]').val(user.email || '');
                $('[name="nombre_completo"]').val(user.name || '');
                $('[name="telefono"]').val(user.telefono || '');
                
                if (user.avatar) {
                    $('#avatarPreview').attr('src', '/storage/' + user.avatar);
                }
                
                toastr.success('Perfil cargado correctamente');
            } else {
                console.error('Respuesta inválida:', response);
                // Fallback: cargar datos desde elementos HTML
                cargarDatosUsuarioFallback();
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error AJAX:');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Status Code:', xhr.status);
            console.error('Response Text:', xhr.responseText);
            
            // Mostrar mensaje de error específico
            if (xhr.status === 404) {
                toastr.error('Ruta no encontrada. Verifica las rutas.');
            } else if (xhr.status === 401) {
                toastr.error('No autorizado. Inicia sesión nuevamente.');
            } else if (xhr.status === 500) {
                toastr.error('Error del servidor. Revisa los logs.');
            } else {
                toastr.error('Error al cargar el perfil: ' + error);
            }
            
            // Cargar datos usando fallback
            cargarDatosUsuarioFallback();
            // Cargar configuraciones
            cargarConfiguraciones();
            
            // Cargar datos del usuario vía AJAX
            cargarDatosUsuario();
            
            // Cargar lista de backups
            cargarListaBackups();
                }
    });
}

// Función de fallback que no depende de AJAX
function cargarDatosUsuarioFallback() {
    console.log('Usando método fallback para cargar datos del usuario');
    
    // Si hay datos del usuario en elementos data
    const userId = $('meta[name="user-id"]').attr('content');
    const userName = $('meta[name="user-name"]').attr('content');
    const userEmail = $('meta[name="user-email"]').attr('content');
    
    if (userName) {
        $('[name="username"]').val(userName);
        $('[name="nombre_completo"]').val(userName);
    }
    
    if (userEmail) {
        $('[name="email"]').val(userEmail);
    }
    
    // Si no hay datos en meta, intentar obtener del usuario autenticado de Laravel
    // Estos valores se inyectan directamente desde Blade
    @auth
        $('[name="username"]').val('{{ auth()->user()->name }}');
        $('[name="nombre_completo"]').val('{{ auth()->user()->name }}');
        $('[name="email"]').val('{{ auth()->user()->email }}');
        $('[name="telefono"]').val('{{ auth()->user()->telefono ?? "" }}');
    @endauth
}


// Función de respaldo para cargar datos desde Blade (PHP)
function cargarDatosUsuarioDesdeBlade() {
    console.log('Usando método alternativo para cargar datos del usuario');
    
    // Datos del usuario autenticado desde Blade (inyectados por Laravel)
    const userData = {
        name: '{{ auth()->user()->name ?? "" }}',
        email: '{{ auth()->user()->email ?? "" }}',
        telefono: '{{ auth()->user()->telefono ?? "" }}',
        avatar: '{{ auth()->user()->avatar ?? "" }}'
    };
    
    if ($('[name="username"]').length) {
        $('[name="username"]').val(userData.name);
    }
    
    if ($('[name="email"]').length) {
        $('[name="email"]').val(userData.email);
    }
    
    if ($('[name="nombre_completo"]').length) {
        $('[name="nombre_completo"]').val(userData.name);
    }
    
    if ($('[name="telefono"]').length) {
        $('[name="telefono"]').val(userData.telefono);
    }
    
    if (userData.avatar && $('#avatarPreview').length) {
        $('#avatarPreview').attr('src', '/storage/' + userData.avatar);
    }
}

function cargarConfiguraciones() {
    console.log('Cargando configuraciones desde la base de datos...');
    
    $.ajax({
        url: '/configuracion/cargar-configuraciones',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Configuraciones recibidas:', response);
            
            // Cargar configuración general
            if (response.general) {
                $('#nombre_sistema').val(response.general.nombre_sistema || '');
                $('#version').val(response.general.version || '1.0.0');
                $('#zona_horaria').val(response.general.zona_horaria || 'America/Bogota');
                $('#formato_fecha').val(response.general.formato_fecha || 'd/m/Y');
                $('#moneda').val(response.general.moneda || 'COP');
                $('#simbolo_moneda').val(response.general.simbolo_moneda || '$');
            }
            
            // Cargar configuración de facturación
            if (response.facturacion) {
                $('#prefijo_factura').val(response.facturacion.prefijo_factura || '');
                $('#consecutivo_inicial').val(response.facturacion.consecutivo_inicial || '');
                $('#consecutivo_actual').val(response.facturacion.consecutivo_actual || '');
                $('#proximo_numero').val(response.facturacion.proximo_numero || '');
                $('#longitud_numero').val(response.facturacion.longitud_numero || '6');
                $('#formato_factura').val(response.facturacion.formato_factura || 'simple');
                $('#tamaño_papel').val(response.facturacion.tamaño_papel || 'thermal');
                $('#copias').val(response.facturacion.copias || '1');
                
                // Checkboxes
                $('#autogenerar').prop('checked', response.facturacion.autogenerar === '1');
                $('#validar_duplicados').prop('checked', response.facturacion.validar_duplicados === '1');
                $('#factura_electronica').prop('checked', response.facturacion.factura_electronica === '1');
            }
            
            // Cargar datos del negocio
            if (response.negocio) {
                $('[name="nombre_negocio"]').val(response.negocio.nombre_negocio || '');
                $('[name="nit"]').val(response.negocio.nit || '');
                $('[name="direccion"]').val(response.negocio.direccion || '');
                $('[name="telefono_negocio"]').val(response.negocio.telefono_negocio || '');
                $('[name="email_negocio"]').val(response.negocio.email_negocio || '');
                $('[name="website"]').val(response.negocio.website || '');
                $('[name="mensaje_factura"]').val(response.negocio.mensaje_factura || '');
            }
            
            // Cargar configuración de impuestos
            if (response.impuestos) {
                $('[name="iva"]').val(response.impuestos.iva || '');
                $('#incluir_iva').prop('checked', response.impuestos.incluir_iva === '1');
                $('#mostrar_iva').prop('checked', response.impuestos.mostrar_iva === '1');
            }
            
            // Cargar configuración de alertas
            if (response.alertas) {
                $('[name="stock_minimo_alerta"]').val(response.alertas.stock_minimo_alerta || '');
                $('[name="dias_vencimiento"]').val(response.alertas.dias_vencimiento || '');
                $('#alertar_stock').prop('checked', response.alertas.alertar_stock === '1');
                $('#alertar_vencimiento').prop('checked', response.alertas.alertar_vencimiento === '1');
            }
            
            toastr.success('Configuraciones cargadas correctamente');
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar configuraciones:', error);
            console.error('Respuesta del servidor:', xhr.responseText);
            toastr.error('Error al cargar las configuraciones desde la base de datos');
        }
    });
}



// Guardar configuraciones
$('#formConfigGeneral').on('submit', function(e) {
    e.preventDefault();
    let formData = $(this).serialize();
    
    $.ajax({
        url: '/configuracion/guardar-general',
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

let isSubmitting = false;

$('#formFacturacion').off('submit').on('submit', function(e) {
    e.preventDefault();
    
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
                toastr.clear();
                toastr.success(response.message, 'Éxito');
                cargarConfiguraciones(); // Recargar configuraciones
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
            cargarDatosUsuario(); // Recargar datos del usuario
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
            cargarConfiguraciones(); // Recargar configuraciones
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
            cargarConfiguraciones(); // Recargar configuraciones
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
            cargarConfiguraciones(); // Recargar configuraciones
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Error al guardar');
        }
    });
});

function reiniciarConsecutivo() {
    if(confirm('¿Está seguro de reiniciar el consecutivo? Esto puede causar duplicados.')) {
        $.ajax({
            url: '/configuracion/reiniciar-consecutivo',
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                $('#consecutivo_actual').val(1);
                $('#proximo_numero').val(2);
                toastr.success(response.message);
                cargarConfiguraciones(); // Recargar configuraciones
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
    window.location.href = `/configuracion/descargar-backup/${filename}`;
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

// Inicializar
$(document).ready(function() {
    cargarConfiguraciones();  // Cargar configuraciones de la BD
    cargarDatosUsuario();     // Cargar datos del usuario actual
    cargarListaBackups();     // Cargar lista de backups
    
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
        if (actual) {
            let padded = actual.toString().padStart(longitud, '0');
            $('#consecutivo_actual').val(padded);
        }
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