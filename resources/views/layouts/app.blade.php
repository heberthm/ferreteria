@extends('adminlte::page')

{{-- Extend and customize the browser title --}}

@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle')
        | @yield('subtitle')
    @endif
@stop

{{-- Extend and customize the page content header --}}

@section('content_header')
    @hasSection('content_header_title')
        <h1 class="text-muted">
            @yield('content_header_title')

            @hasSection('content_header_subtitle')
                <small class="text-dark">
                    <i class="fas fa-xs fa-angle-right text-muted"></i>
                    @yield('content_header_subtitle')
                </small>
            @endif
        </h1>
    @endif
@stop

 
{{-- Rename section content to content_body --}}

@section('content')
    @yield('content_body')
    @stack('js')
@stop

{{-- Create a common footer --}}

@section('footer')
    <div class="float-right">
        Version: {{ config('app.version', '1.0.0') }}
    </div>

    <strong>
        Copyright &copy; 2022 - <?php echo date('Y'); ?> <a href="#">hemTech</a>. Todos los derechos reservados.

    </strong>
@stop

{{-- Add common Javascript/Jquery code --}}




@push('js')

 @include('adminlte::plugins', ['type' => 'js'])
    @yield('js')

    <script>
      
         // Script para manejar el cierre de sesión
    $(document).ready(function() {
        // Manejador para el enlace de salir
        $(document).on('click', '.btn-logout, a[href*="logout"], a:contains("Salir")', function(e) {
            // Verificar si es nuestro enlace de salir
            if ($(this).text().trim() === 'Salir' || $(this).hasClass('btn-logout')) {
                e.preventDefault();
                confirmarCierreSesion(e);
            }
        });
    });

    function confirmarCierreSesion(event) {
        if (event) event.preventDefault();
        
        Swal.fire({
            title: '¿Cerrar Sesión?',
            text: '¿Estás seguro de que deseas salir del sistema?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Sí, salir',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                cerrarSesion();
            }
        });
    }

    function cerrarSesion() {
        // Mostrar loading
        Swal.fire({
            title: 'Cerrando sesión...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Crear y enviar formulario de logout
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("logout") }}';
        form.style.display = 'none';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }

    </script>
@endpush

{{-- Add common CSS customizations --}}

@push('css')

  @include('adminlte::plugins', ['type' => 'css'])
  @yield('css')


    <style type="text/css">
        {{-- You can add AdminLTE customizations here --}}      
       
        /*
                .card-header {
                    border-bottom: none;
                }
                .card-title {
                    font-weight: 600;
                }
                */
    </style>
@endpush

