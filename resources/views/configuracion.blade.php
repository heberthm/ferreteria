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
    .nav-tabs .nav-link { color: #495057; font-weight: 500; }
    .nav-tabs .nav-link.active { color: #007bff; border-bottom: 2px solid #007bff; }
    .form-group label { font-weight: 500; }
    .required:after { content: " *"; color: red; }
    .content-wrapper { min-height: auto !important; }
    .main-footer { position: relative; margin-top: 0; }
    .tab-content { margin-bottom: 0; padding-bottom: 0; }
    .container-fluid { padding-bottom: 0; }
    .input-group .toggle-password { cursor: pointer; transition: all 0.3s; }
    .input-group .toggle-password:hover { background-color: #e9ecef; }
    .valid-feedback { font-size: .8rem; color: #28a745; display: block; }
    .invalid-feedback { font-size: .8rem; color: #dc3545; display: block; }
    .is-valid { border-color: #28a745 !important; background-image: none !important; }
    .is-invalid { border-color: #dc3545 !important; background-image: none !important; }

   }

</style>
@stop

@section('content')
<br>
<div class="container-fluid" style="padding-bottom:0">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title"><i class="fas fa-cogs"></i> Configuración del Sistema</h1>
                </div>
                <div class="card-body" style="padding-bottom:0">

                    {{-- TABS --}}
                    <ul class="nav nav-tabs" id="configTabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#general" role="tab"><i class="fas fa-building"></i> General</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#facturacion" role="tab"><i class="fas fa-file-invoice-dollar"></i> Facturación</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#usuarios" role="tab"><i class="fas fa-users"></i> Usuarios/Roles</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#perfil" role="tab"><i class="fas fa-user-circle"></i> Mi Perfil</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#negocio" role="tab"><i class="fas fa-store"></i> Datos del Negocio</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#impuestos" role="tab"><i class="fas fa-percent"></i> Impuestos</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#alertas" role="tab"><i class="fas fa-bell"></i> Alertas</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#backup" role="tab"><i class="fas fa-database"></i> Respaldo</a></li>
                    </ul>

                    <div class="tab-content mt-3" style="padding-bottom:0">

                        {{-- ===== Tab General ===== --}}
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
                                        <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        {{-- ===== FIN Tab General ===== --}}

                        {{-- ===== Tab Facturación ===== --}}
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
                                                    <label>Consecutivo Actual</label>
                                                    <input type="number" name="consecutivo_actual" id="consecutivo_actual" class="form-control" readonly>
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
                                        <div class="row mt-2">
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
                        {{-- ===== FIN Tab Facturación ===== --}}

                        {{-- ===== Tab Usuarios y Roles ===== --}}
                        <div class="tab-pane fade" id="usuarios" role="tabpanel">
                            <div class="card config-card">
                                <div class="card-header bg-warning text-white">
                                    <h3 class="card-title">Gestión de Usuarios y Roles</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalNuevoUsuario">
                                                <i class="fas fa-user-plus"></i> Nuevo Usuario
                                            </button>
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalNuevoRol">
                                                <i class="fas fa-tag"></i> Nuevo Rol
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        {{-- Tabla Usuarios --}}
                                        <div class="col-md-6">
                                            <h4><i class="fas fa-users"></i> Usuarios del Sistema</h4>
                                            <div class="table-responsive" >
                                                <table class="table table-hover" ´id="tablaUsuarios">
                                                    <thead>
                                                        <tr><th>Usuario</th><th>Email</th><th>Rol</th><th>Acciones</th></tr>
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
                                                                <button class="btn btn-xs btn-info" title="Editar"
                                                                    style="padding:.2rem .3rem;font-size:.75rem"
                                                                    onclick="editarUsuario({{ $usuario->id }})">
                                                                    <i class="fas fa-edit" style="font-size:.7rem"></i>
                                                                </button>
                                                                @if($usuario->id != auth()->id())
                                                                <button class="btn btn-xs btn-danger" title="Eliminar"
                                                                    style="padding:.2rem .3rem;font-size:.75rem"
                                                                    onclick="eliminarUsuario({{ $usuario->id }}, '{{ addslashes($usuario->name) }}')">
                                                                    <i class="fas fa-trash" style="font-size:.7rem"></i>
                                                                </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        {{-- Tabla Roles --}}
                                        <div class="col-md-6">
                                            <h4><i class="fas fa-tags"></i> Roles del Sistema</h4>
                                            <div class="table-responsive">
                                                <table class="table table-hover" id="tablaRoles">
                                                    <thead>
                                                        <tr><th>Rol</th><th>Descripción</th><th>Usuarios</th><th>Acciones</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($roles as $rol)
                                                        <tr>
                                                            <td>{{ $rol->name }}</td>
                                                            <td>{{ $rol->description ?? 'Sin descripción' }}</td>
                                                            <td>{{ $rol->users->count() ?? 0 }}</td>
                                                            <td class="text-nowrap">
                                                                <button class="btn btn-xs btn-info" title="Editar rol"
                                                                    onclick="editarRol({{ $rol->id }})">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                               <button class="btn btn-xs btn-primary btn-permisos" title="Permisos"
                                                                   data-id="{{ $rol->id_rol }}"
                                                                    data-nombre="{{ $rol->name }}">
                                                                    <i class="fas fa-key"></i>
                                                                </button>
                                                                @if($rol->name != 'Administrador')
                                                                <button class="btn btn-xs btn-danger btn-eliminar" title="Eliminar rol"
                                                                    data-id="{{ $rol->id_rol }}" 
                                                                    data-nombre="{{ $rol->name }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr><td colspan="4" class="text-center">No hay roles definidos</td></tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- ===== FIN Tab Usuarios y Roles ===== --}}

                        {{-- ===== Tab Mi Perfil ===== --}}
                        <div class="tab-pane fade" id="perfil" role="tabpanel">
                            <div class="card config-card">
                                <div class="card-header bg-info text-white">
                                    <h3 class="card-title">Mi Perfil de Usuario</h3>
                                </div>
                                <div class="card-body text-center">
                                    @php $usuarioAuth = auth()->user(); @endphp
                                    <img src="{{ $usuarioAuth->avatar ? Storage::url($usuarioAuth->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($usuarioAuth->name).'&color=7F9CF5&background=EBF4FF&bold=true&size=120' }}"
                                        alt="Avatar" class="img-circle img-fluid mb-3"
                                        width="120" height="120"
                                        style="object-fit:cover;border-radius:50%">
                                    <h4>{{ $usuarioAuth->name }}</h4>
                                    <p class="text-muted">{{ $usuarioAuth->email }}</p>
                                    <p><strong>Rol:</strong> {{ $usuarioAuth->rol ?? 'Usuario' }}</p>
                                    <button type="button" class="btn btn-primary btn-lg" id="btnAbrirPerfil">
                                        <i class="fas fa-edit"></i> Editar Mi Perfil
                                    </button>
                                </div>
                            </div>
                        </div>
                        {{-- ===== FIN Tab Mi Perfil ===== --}}

                        {{-- ===== Tab Datos del Negocio ===== --}}
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
                                        <button type="submit" class="btn btn-primary">Guardar Información</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        {{-- ===== FIN Tab Datos del Negocio ===== --}}

                        {{-- ===== Tab Impuestos ===== --}}
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
                                        <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        {{-- ===== FIN Tab Impuestos ===== --}}

                        {{-- ===== Tab Alertas ===== --}}
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
                                        <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        {{-- ===== FIN Tab Alertas ===== --}}

                        {{-- ===== Tab Respaldo ===== --}}
                        <div class="tab-pane fade" id="backup" role="tabpanel">
                            <div class="card config-card">
                                <div class="card-header bg-dark text-white">
                                    <h3 class="card-title">Respaldo de la Base de Datos</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i> Realice respaldos periódicos para prevenir pérdida de información.
                                            </div>
                                            <button type="button" class="btn btn-success btn-lg btn-block" onclick="crearBackup()">
                                                <i class="fas fa-database"></i> Crear Respaldo Ahora
                                            </button>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header"><h4>Respaldos Recientes</h4></div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead>
                                                                <tr><th>Archivo</th><th>Fecha</th><th>Tamaño</th><th>Acción</th></tr>
                                                            </thead>
                                                            <tbody id="listaBackups">
                                                                <tr><td colspan="4" class="text-center">No hay respaldos disponibles</td></tr>
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
                                                <input type="time" name="hora_backup" class="form-control" style="max-width:200px">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- ===== FIN Tab Respaldo ===== --}}

                    </div>{{-- fin tab-content --}}
                </div>{{-- fin card-body --}}
            </div>{{-- fin card --}}
        </div>{{-- fin col-12 --}}
    </div>{{-- fin row --}}
</div>{{-- fin container-fluid --}}


{{-- ================================================================ --}}
{{--  MODALES — TODOS FUERA DEL CONTAINER, CADA UNO CERRADO CORRECTAMENTE --}}
{{-- ================================================================ --}}

{{-- ===== Modal Nuevo Usuario ===== --}}
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Nuevo Usuario</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formNuevoUsuario">
                    @csrf
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
{{-- ===== FIN Modal Nuevo Usuario ===== --}}

{{-- ===== Modal Nuevo Rol ===== --}}
<div class="modal fade" id="modalNuevoRol" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-tag"></i> Nuevo Rol</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formNuevoRol">
                    @csrf
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
                                    <input type="checkbox" class="custom-control-input" id="new_perm_ventas" name="permisos[]" value="ventas">
                                    <label class="custom-control-label" for="new_perm_ventas">Ventas</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="new_perm_compras" name="permisos[]" value="compras">
                                    <label class="custom-control-label" for="new_perm_compras">Compras</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="new_perm_inventario" name="permisos[]" value="inventario">
                                    <label class="custom-control-label" for="new_perm_inventario">Inventario</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="new_perm_clientes" name="permisos[]" value="clientes">
                                    <label class="custom-control-label" for="new_perm_clientes">Clientes</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="new_perm_reportes" name="permisos[]" value="reportes">
                                    <label class="custom-control-label" for="new_perm_reportes">Reportes</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="new_perm_configuracion" name="permisos[]" value="configuracion">
                                    <label class="custom-control-label" for="new_perm_configuracion">Configuración</label>
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
{{-- ===== FIN Modal Nuevo Rol ===== --}}

{{-- ===== Modal Editar Usuario ===== --}}
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <h5 class="modal-title"><i class="fas fa-user-edit"></i> Editar Usuario</h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editUsuarioId">
                <div class="form-group">
                    <label>Nombre de Usuario</label>
                    <input type="text" id="editUsuarioNombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="editUsuarioEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Rol</label>
                    <select id="editUsuarioRol" class="form-control">
                        <option value="vendedor">Vendedor</option>
                        <option value="almacenista">Almacenista</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> Para cambiar la contraseña, el usuario debe hacerlo desde su perfil.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarUsuarioEditado()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>
{{-- ===== FIN Modal Editar Usuario ===== --}}

{{-- ===== Modal Editar Rol ===== --}}
<div class="modal fade" id="modalEditarRol" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-tag"></i> Editar Rol</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editRolId">
                <div class="form-group">
                    <label>Nombre del Rol</label>
                    <input type="text" id="editRolNombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea id="editRolDescripcion" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarRolEditado()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>
{{-- ===== FIN Modal Editar Rol ===== --}}

{{-- ===== Modal Permisos del Rol ===== --}}
<div class="modal fade" id="modalPermisosRol" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-key"></i> Permisos del Rol: <span id="permisosRolNombre"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="permisosRolId">
                <div class="row">
                    <div class="col-md-6">
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input permiso-checkbox" id="perm_ventas" data-permiso="ventas">
                            <label class="custom-control-label" for="perm_ventas"><i class="fas fa-shopping-cart"></i> Ventas</label>
                        </div>
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input permiso-checkbox" id="perm_compras" data-permiso="compras">
                            <label class="custom-control-label" for="perm_compras"><i class="fas fa-truck"></i> Compras</label>
                        </div>
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input permiso-checkbox" id="perm_inventario" data-permiso="inventario">
                            <label class="custom-control-label" for="perm_inventario"><i class="fas fa-boxes"></i> Inventario</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input permiso-checkbox" id="perm_clientes" data-permiso="clientes">
                            <label class="custom-control-label" for="perm_clientes"><i class="fas fa-users"></i> Clientes</label>
                        </div>
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input permiso-checkbox" id="perm_reportes" data-permiso="reportes">
                            <label class="custom-control-label" for="perm_reportes"><i class="fas fa-chart-bar"></i> Reportes</label>
                        </div>
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input permiso-checkbox" id="perm_configuracion" data-permiso="configuracion">
                            <label class="custom-control-label" for="perm_configuracion"><i class="fas fa-cogs"></i> Configuración</label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> Los permisos determinan qué acciones puede realizar este rol.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarPermisosRol()">Guardar Permisos</button>
            </div>
        </div>
    </div>
</div>
{{-- ===== FIN Modal Permisos del Rol ===== --}}

{{-- ===== Modal Editar Perfil ===== --}}

<div class="modal fade" id="modalEditarPerfil" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-user-edit"></i> Editar Mi Perfil</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="formEditarPerfil" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <img src="" alt="Avatar" class="img-circle img-fluid mb-2"
                                width="120" height="120" id="modalAvatarPreview"
                                style="object-fit:cover;border-radius:50%">
                            <input type="file" name="avatar" id="modalAvatar" accept="image/*" style="display:none">
                            <button type="button" class="btn btn-sm btn-secondary d-block mx-auto mt-1" id="modalBtnCambiarFoto">
                                <i class="fas fa-camera"></i> Cambiar Foto
                            </button>
                            <small class="text-muted d-block mt-1">JPG, PNG, GIF (Máx. 2MB)</small>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nombre de Usuario</label>
                                        <input type="text" name="username" id="modalUsername" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" id="modalEmail" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nombre Completo</label>
                                        <input type="text" name="nombre_completo" id="modalNombreCompleto" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <input type="text" name="telefono" id="modalTelefono" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <hr>
                                    <h5><i class="fas fa-key"></i> Cambiar Contraseña</h5>
                                    <small class="text-muted">Deje los campos vacíos si no desea cambiar la contraseña</small>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nueva Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" name="password_nueva" id="modalPasswordNueva" class="form-control" placeholder="Mínimo 6 caracteres">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="modalPasswordNueva">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Confirmar Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmacion" id="modalPasswordConfirmacion" class="form-control" placeholder="Repita la contraseña">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="modalPasswordConfirmacion">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="modalPassError" class="invalid-feedback" style="display:none">
                                            <i class="fas fa-times-circle"></i> Las contraseñas no coinciden
                                        </div>
                                        <div id="modalPassOk" class="valid-feedback" style="display:none">
                                            <i class="fas fa-check-circle"></i> Las contraseñas coinciden
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- ===== FIN Modal Editar Perfil ===== --}}

@stop

@push('js')
<script>

// =====================================================================
// TOASTR CONFIGURATION
// =====================================================================
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    preventDuplicates: true,
    timeOut: "4000"
};

// =====================================================================
// TOGGLE PASSWORD
// =====================================================================
$(document).on('click', '.toggle-password', function() {
    var id = $(this).data('target');
    var inp = $('#' + id);
    var icon = $(this).find('i');
    
    if (inp.attr('type') === 'password') {
        inp.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        inp.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
});

// =====================================================================
// PASSWORD VALIDATION
// =====================================================================
$(document).on('keyup', '#modalPasswordNueva, #modalPasswordConfirmacion', function() {
    var p = $('#modalPasswordNueva').val();
    var c = $('#modalPasswordConfirmacion').val();
    
    if (!p && !c) {
        $('#modalPassError, #modalPassOk').hide();
        return;
    }
    
    if (p === c && p.length > 0) {
        $('#modalPassError').hide();
        $('#modalPassOk').show();
    } else {
        $('#modalPassOk').hide();
        $('#modalPassError').show();
    }
});



// =====================================================================
// LOAD CONFIGURATIONS
// =====================================================================
function cargarConfiguraciones() {
    $.ajax({
        url: '/configuracion/cargar-configuraciones',
        type: 'GET',
        success: function(r) {
            if (r.general) {
                $('#nombre_sistema').val(r.general.nombre_sistema || '');
                $('#version').val(r.general.version || '1.0.0');
                $('#zona_horaria').val(r.general.zona_horaria || 'America/Bogota');
                $('#formato_fecha').val(r.general.formato_fecha || 'd/m/Y');
                $('#moneda').val(r.general.moneda || 'COP');
                $('#simbolo_moneda').val(r.general.simbolo_moneda || '$');
            }
            if (r.facturacion) {
                $('#prefijo_factura').val(r.facturacion.prefijo_factura || '');
                $('#consecutivo_inicial').val(r.facturacion.consecutivo_inicial || '');
                $('#consecutivo_actual').val(r.facturacion.consecutivo_actual || '');
                $('#proximo_numero').val(r.facturacion.proximo_numero || '');
                $('#longitud_numero').val(r.facturacion.longitud_numero || '6');
                $('#formato_factura').val(r.facturacion.formato_factura || 'simple');
                $('#autogenerar').prop('checked', r.facturacion.autogenerar === '1');
                $('#validar_duplicados').prop('checked', r.facturacion.validar_duplicados === '1');
                $('#factura_electronica').prop('checked', r.facturacion.factura_electronica === '1');
            }
            if (r.negocio) {
                $('[name="nombre_negocio"]').val(r.negocio.nombre_negocio || '');
                $('[name="nit"]').val(r.negocio.nit || '');
                $('[name="direccion"]').val(r.negocio.direccion || '');
                $('[name="telefono_negocio"]').val(r.negocio.telefono_negocio || '');
                $('[name="email_negocio"]').val(r.negocio.email_negocio || '');
                $('[name="website"]').val(r.negocio.website || '');
                $('[name="mensaje_factura"]').val(r.negocio.mensaje_factura || '');
            }
            if (r.impuestos) {
                $('[name="iva"]').val(r.impuestos.iva || '');
                $('#incluir_iva').prop('checked', r.impuestos.incluir_iva === '1');
                $('#mostrar_iva').prop('checked', r.impuestos.mostrar_iva === '1');
            }
            if (r.alertas) {
                $('[name="stock_minimo_alerta"]').val(r.alertas.stock_minimo_alerta || '');
                $('[name="dias_vencimiento"]').val(r.alertas.dias_vencimiento || '');
                $('#alertar_stock').prop('checked', r.alertas.alertar_stock === '1');
                $('#alertar_vencimiento').prop('checked', r.alertas.alertar_vencimiento === '1');
            }
        },
        error: function() {
            toastr.error('Error al cargar configuraciones');
        }
    });
}


// =====================================================================
// PERFIL MODAL - SOLUCIÓN DEFINITIVA
// =====================================================================
$(document).ready(function() {

    // Enlazar directamente al botón que abre el modal
    $(document).on('click', '[data-target="#modalEditarPerfil"]', function() {
     //   console.log('🟢 Botón perfil clickeado');
        cargarDatosPerfil();
    });

    $(document).on('click', '#btnAbrirPerfil', function() {
  //  console.log('🟢 Botón perfil clickeado');
    cargarDatosPerfil();
});

});

function cargarDatosPerfil() {
  //  console.log('📡 Iniciando carga de datos del perfil...');

    $('#modalUsername').val('Cargando...');
    $('#modalEmail').val('Cargando...');
    $('#modalNombreCompleto').val('Cargando...');
    $('#modalTelefono').val('');

    $.ajax({
        url: '{{ url("/configuracion/usuario-actual") }}',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('✅ Datos cargados:', response);

            if (response.success && response.user) {
                var user = response.user;

                $('#modalUsername').val(user.name || '');
                $('#modalEmail').val(user.email || '');
                $('#modalNombreCompleto').val(user.name || '');
                $('#modalTelefono').val(user.telefono || '');

                var avatarUrl = user.avatar
                    ? '/storage/' + user.avatar
                    : 'https://ui-avatars.com/api/?name='
                        + encodeURIComponent(user.name || 'U')
                        + '&color=7F9CF5&background=EBF4FF&bold=true&size=120';

                $('#modalAvatarPreview').attr('src', avatarUrl);

                // Abrir modal si no está abierto
                if (!$('#modalEditarPerfil').hasClass('show')) {
                    $('#modalEditarPerfil').modal('show');
                }
            } else {
                toastr.error('Error al cargar datos: ' + (response.message || ''));
            }
        },
        error: function(xhr) {
            console.error('❌ Error:', xhr.status, xhr.responseText);
            toastr.error('Error ' + xhr.status + ' al cargar perfil');
        }
    });

    // Limpiar contraseñas
    $('#modalPasswordNueva, #modalPasswordConfirmacion').val('').attr('type', 'password');
    $('#modalPassError, #modalPassOk').hide();
    $('.toggle-password i').removeClass('fa-eye-slash').addClass('fa-eye');
}

$(document).on('click', '#modalBtnCambiarFoto', function() {
    $('#modalAvatar').click();
});

$(document).on('change', '#modalAvatar', function() {
    var file = this.files[0];
    if (!file) return;

    var allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    if (allowed.indexOf(file.type) === -1) {
        toastr.error('Solo se permiten imágenes JPG, PNG o GIF');
        this.value = '';
        return;
    }
    
    if (file.size > 2 * 1024 * 1024) {
        toastr.error('La imagen no debe superar los 2MB');
        this.value = '';
        return;
    }

    var reader = new FileReader();
    reader.onload = function(e) {
        $('#modalAvatarPreview').attr('src', e.target.result);
    };
    reader.readAsDataURL(file);
});

$(document).on('submit', '#formEditarPerfil', function(e) {
    e.preventDefault();
    
    var p = $('#modalPasswordNueva').val();
    var c = $('#modalPasswordConfirmacion').val();
    
    if (p || c) {
        if (p !== c) {
            toastr.error('Las contraseñas no coinciden');
            return;
        }
        if (p.length < 6) {
            toastr.error('La contraseña debe tener al menos 6 caracteres');
            return;
        }
    }
    
    var fd = new FormData(this);
    var btn = $(this).find('[type="submit"]');
    var orig = btn.html();
    
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-pulse"></i> Guardando...');
    
    $.ajax({
        url: '/configuracion/actualizar-perfil',
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function(r) {
            toastr.success(r.message || 'Perfil actualizado correctamente');
            $('#modalEditarPerfil').modal('hide');
            setTimeout(function() { location.reload(); }, 1200);
        },
        error: function(xhr) {
            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error al actualizar';
            toastr.error(msg);
        },
        complete: function() {
            btn.prop('disabled', false).html(orig);
        }
    });
});

// =====================================================================
// USERS MANAGEMENT - VERSIÓN RENOVADA
// =====================================================================
function editarUsuario(id) {
    $.ajax({
        url: '/configuracion/listar-usuarios',
        type: 'GET',
        dataType: 'json',
        success: function(r) {
            if (!r.success) { 
                toastr.error('Error al cargar datos'); 
                return; 
            }
            
            var user = r.usuarios.find(function(u) { return u.id == id; });
            
            if (user) {
                $('#editUsuarioId').val(user.id);
                $('#editUsuarioNombre').val(user.name);
                $('#editUsuarioEmail').val(user.email);
                $('#editUsuarioRol').val(user.rol || 'vendedor');
                $('#modalEditarUsuario').modal('show');
            } else {
                toastr.error('Usuario no encontrado');
            }
        },
        error: function() { 
            toastr.error('Error de conexión'); 
        }
    });
}

function guardarUsuarioEditado() {
    var id = $('#editUsuarioId').val();
    var nombre = $.trim($('#editUsuarioNombre').val());
    var email = $.trim($('#editUsuarioEmail').val());
    var rol = $('#editUsuarioRol').val();

    if (!nombre || !email) { 
        toastr.error('Todos los campos son requeridos'); 
        return; 
    }

    var btn = $('#modalEditarUsuario').find('.btn-primary').last();
    var origText = btn.html();
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-pulse"></i> Guardando...');

    $.ajax({
        url: '/configuracion/actualizar-usuario/' + id,
        type: 'PUT',
        data: {
            nombre: nombre,
            email: email,
            rol: rol,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(r) {
            if (r.success) {
                toastr.success('Usuario actualizado correctamente');
                $('#modalEditarUsuario').modal('hide');
                setTimeout(function() { location.reload(); }, 1000);
            } else {
                toastr.error(r.message || 'Error al actualizar usuario');
            }
        },
        error: function(xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error al actualizar usuario';
            toastr.error(msg);
        },
        complete: function() {
            btn.prop('disabled', false).html(origText);
        }
    });
}

function eliminarUsuario(id, nombre) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Deseas eliminar al usuario "' + nombre + '"?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (!result.isConfirmed) return;

        Swal.fire({
            title: 'Eliminando...',
            allowOutsideClick: false,
            didOpen: function() { Swal.showLoading(); }
        });

        $.ajax({
            url: '/eliminar-usuario/' + id,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(r) {
                if (r.success) {
                    Swal.fire({ 
                        title: '¡Eliminado!', 
                        text: 'Usuario eliminado correctamente', 
                        icon: 'success', 
                        timer: 1800, 
                        showConfirmButton: false 
                    });
                    setTimeout(function() { location.reload(); }, 1800);
                } else {
                    Swal.fire('Error', r.message || 'Error al eliminar usuario', 'error');
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error al eliminar usuario';
                Swal.fire('Error', msg, 'error');
            }
        });
    });
}

function guardarUsuario() {
    var btn = $('#modalNuevoUsuario').find('.btn-primary').last();
    var origText = btn.html();
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-pulse"></i> Guardando...');

    $.ajax({
        url: '/configuracion/guardar-usuario',
        type: 'POST',
        data: $('#formNuevoUsuario').serialize(),
        success: function(r) {
            if (r.success) {
                toastr.success('Usuario creado correctamente');
                $('#modalNuevoUsuario').modal('hide');
                $('#formNuevoUsuario')[0].reset();
                setTimeout(function() { location.reload(); }, 1000);
            } else {
                toastr.error(r.message || 'Error al guardar usuario');
            }
        },
        error: function() { 
            toastr.error('Error al guardar usuario'); 
        },
        complete: function() { 
            btn.prop('disabled', false).html(origText); 
        }
    });
}

    // Botón Permisos
   $(document).on('click', '.btn-permisos', function() {
    var id     = $(this).data('id');
    var nombre = $(this).data('nombre');

    console.log('Botón permisos clickeado → id:', id, '| nombre:', nombre);

    gestionarPermisos(id, nombre);
});

    // Botón Eliminar
    $(document).on('click', '.btn-eliminar', function() {
        var id     = $(this).data('id');
        var nombre = $(this).data('nombre');
        eliminarRol(id, nombre);
    });

    // Botón Editar (si tienes la función editarRol)
    $(document).on('click', '.btn-editar', function() {
        var id = $(this).data('id');
        editarRol(id);
    });



// =====================================================================
// ROLES MANAGEMENT - VERSIÓN RENOVADA
// =====================================================================
function editarRol(id) {
    $.ajax({
        url: '/configuracion/listar-roles',
        type: 'GET',
        dataType: 'json',
        success: function(r) {
            if (!r.success) { 
                toastr.error('Error al cargar datos'); 
                return; 
            }
            
            var rol = r.roles.find(function(rl) { return rl.id == id; });
            
            if (rol) {
                $('#editRolId').val(rol.id);
                $('#editRolNombre').val(rol.name);
                $('#editRolDescripcion').val(rol.description || '');
                $('#modalEditarRol').modal('show');
            } else {
                toastr.error('Rol no encontrado');
            }
        },
        error: function() { 
            toastr.error('Error de conexión'); 
        }
    });
}

function eliminarRol(id, nombre) {
    Swal.fire({
        title: '¿Estás seguro?',
        html: '¿Deseas eliminar el rol <strong>"' + nombre + '"</strong>?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (!result.isConfirmed) return;

        Swal.fire({
            title: 'Eliminando...',
            allowOutsideClick: false,
            didOpen: function() { Swal.showLoading(); }
        });

        $.ajax({
            url: '/configuracion/eliminar-rol/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(r) {
                if (r.success) {
                    Swal.fire({ 
                        title: '¡Eliminado!', 
                        text: 'Rol eliminado correctamente', 
                        icon: 'success', 
                        timer: 1800, 
                        showConfirmButton: false 
                    });
                    setTimeout(function() { location.reload(); }, 1800);
                } else {
                    Swal.fire('Error', r.message || 'Error al eliminar rol', 'error');
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error al eliminar rol';
                Swal.fire('Error', msg, 'error');
            }
        });
    });
}

// =====================================================================
// PERMISOS MODAL - VERSIÓN COMPLETAMENTE RENOVADA
// =====================================================================

function gestionarPermisos(id, nombre) {
    // ── 1. Validar que llegó el id ──────
    if (!id) {
        toastr.error('Error: ID de rol no encontrado');
        console.error('gestionarPermisos: id está vacío');
        return;
    }

    console.log('gestionarPermisos → id:', id, '| nombre:', nombre);

    // ── 2. Guardar en el input oculto ────
    $('#permisosRolId').val(id);
    $('#permisosRolNombre').text(nombre);

    // ── 3. Confirmar que se guardó ────
    console.log('Input permisosRolId ahora vale:', $('#permisosRolId').val());

    // ── 4. Resetear checkboxes ───
    $('.permiso-checkbox').prop('checked', false);

    // ── 5. Cargar permisos actuales via AJAX ───´
    /*
    Swal.fire({
        title: 'Cargando permisos...',
        allowOutsideClick: false,
        didOpen: function() { Swal.showLoading(); }
    });

    */

    $.ajax({
        url: '/configuracion/rol/' + id + '/permisos',  // ← id directo, no del input
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        success: function(r) {
            Swal.close();
            if (r.success && r.permisos) {
                r.permisos.forEach(function(permiso) {
                    $('.permiso-checkbox[data-permiso="' + permiso + '"]').prop('checked', true);
                });
            }
            $('#modalPermisosRol').modal('show');
        },
        error: function(xhr) {
            Swal.close();
            console.error('Error al cargar permisos:', xhr);
            toastr.error('Error al cargar los permisos del rol');
            $('#modalPermisosRol').modal('show');
        }
    });
}

function guardarPermisosRol() {
    var id = $('#permisosRolId').val();
    
    // Recoger permisos seleccionados
    var permisos = [];
    $('.permiso-checkbox:checked').each(function() {
        permisos.push($(this).data('permiso'));
    });
    
    console.log('Guardando permisos para rol ID:', id);
    console.log('Permisos seleccionados:', permisos);
    
    var btn = $('#modalPermisosRol').find('.btn-primary').last();
    var origText = btn.html();
    
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-pulse"></i> Guardando...');
    
    $.ajax({
        url: '/configuracion/rol/' + id + '/permisos',
        type: 'POST',
        data: {
            permisos: permisos,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(r) {
            if (r.success) {
                toastr.success('Permisos actualizados correctamente');
                $('#modalPermisosRol').modal('hide');
            } else {
                toastr.error(r.message || 'Error al actualizar permisos');
            }
        },
        error: function(xhr) {
            console.error('Error al guardar permisos:', xhr);
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error al actualizar permisos';
            toastr.error(msg);
        },
        complete: function() {
            btn.prop('disabled', false).html(origText);
        }
    });
}

function guardarRolEditado() {
    var id = $('#editRolId').val();
    var nombre_rol = $.trim($('#editRolNombre').val());
    var descripcion = $('#editRolDescripcion').val();

    if (!nombre_rol) { 
        toastr.error('El nombre del rol es requerido'); 
        return; 
    }

    var btn = $('#modalEditarRol').find('.btn-primary').last();
    var origText = btn.html();
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-pulse"></i> Guardando...');

    $.ajax({
        url: '/configuracion/actualizar-rol/' + id,
        type: 'PUT',
        data: {
            nombre_rol: nombre_rol,
            descripcion: descripcion,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(r) {
            if (r.success) {
                toastr.success('Rol actualizado correctamente');
                $('#modalEditarRol').modal('hide');
                setTimeout(function() { location.reload(); }, 1000);
            } else {
                toastr.error(r.message || 'Error al actualizar rol');
            }
        },
        error: function(xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error al actualizar rol';
            toastr.error(msg);
        },
        complete: function() {
            btn.prop('disabled', false).html(origText);
        }
    });
}

function guardarRol() {
    var btn = $('#modalNuevoRol').find('.btn-primary').last();
    var origText = btn.html();
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-pulse"></i> Guardando...');

    $.ajax({
        url: '/guardar-rol',
        type: 'POST',
        data: $('#formNuevoRol').serialize(),
        success: function(r) {
            if (r.success) {
                toastr.success('Rol creado correctamente');
                $('#modalNuevoRol').modal('hide');
                $('#formNuevoRol')[0].reset();
                setTimeout(function() { location.reload(); }, 1000);
            } else {
                toastr.error(r.message || 'Error al guardar rol');
            }
        },
        error: function(xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error al guardar rol';
            toastr.error(msg);
        },
        complete: function() {
            btn.prop('disabled', false).html(origText);
        }
    });
}

// =====================================================================
// BACKUP FUNCTIONS
// =====================================================================
function cargarListaBackups() {
    $.ajax({
        url: '/configuracion/listar-backups',
        type: 'GET',
        success: function(b) {
            var tbody = $('#listaBackups');
            if (!b || !b.length) {
                tbody.html('<tr><td colspan="4" class="text-center">No hay respaldos disponibles</td></tr>');
                return;
            }
            
            var html = '';
            for (var i = 0; i < b.length; i++) {
                html += '<tr>' +
                    '<td>' + b[i].name + '</td>' +
                    '<td>' + b[i].date + '</td>' +
                    '<td>' + b[i].size + ' KB</td>' +
                    '<td><button class="btn btn-sm btn-info" onclick="descargarBackup(\'' + b[i].name + '\')"><i class="fas fa-download"></i></button></td>' +
                    '</tr>';
            }
            tbody.html(html);
        },
        error: function() {
            $('#listaBackups').html('<tr><td colspan="4" class="text-center text-danger">Error al cargar respaldos</td></tr>');
        }
    });
}

function descargarBackup(filename) {
    window.location.href = '/configuracion/descargar-backup/' + filename;
}

function crearBackup() {
    var btn = $('button[onclick="crearBackup()"]');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-pulse"></i> Creando...');
    
    $.ajax({
        url: '/configuracion/crear-backup',
        type: 'POST',
        data: { _token: $('meta[name="csrf-token"]').attr('content') },
        success: function(r) { 
            toastr.success(r.message); 
            cargarListaBackups(); 
        },
        error: function() { 
            toastr.error('Error al crear el respaldo'); 
        },
        complete: function() { 
            btn.prop('disabled', false).html('<i class="fas fa-database"></i> Crear Respaldo Ahora'); 
        }
    });
}

// =====================================================================
// INITIALIZATION
// =====================================================================
$(document).ready(function() {
    cargarConfiguraciones();
    cargarListaBackups();
    
    console.log('Sistema de configuración inicializado correctamente');
});

</script>
@endpush