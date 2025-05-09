@extends('layouts.app')



@section('title', 'Registrar Nueva Mascota')

    @section('content_header')
        <h1>Registro de Nueva Mascota</h1>
    @stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-ligh">
                <div class="card-header">
                    <h3 class="card-title"> <i class="fas fa-paw"></i> Información de la Mascota</h3>
                </div>
                <!-- /.card-header -->

                <form method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombre de la Mascota</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Ingrese el nombre"
                                        value="{{ old('name') }}">
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="species">Especie</label>
                                    <select class="form-control @error('species') is-invalid @enderror" id="species"
                                        name="species">
                                        <option value="">Seleccione una especie</option>
                                        <option value="perro" {{ old('species') == 'perro' ? 'selected' : '' }}>Perro
                                        </option>
                                        <option value="gato" {{ old('species') == 'gato' ? 'selected' : '' }}>Gato
                                        </option>
                                        <option value="ave" {{ old('species') == 'ave' ? 'selected' : '' }}>Ave</option>
                                        <option value="roedor" {{ old('species') == 'roedor' ? 'selected' : '' }}>Roedor
                                        </option>
                                        <option value="reptil" {{ old('species') == 'reptil' ? 'selected' : '' }}>Reptil
                                        </option>
                                        <option value="otro" {{ old('species') == 'otro' ? 'selected' : '' }}>Otro
                                        </option>
                                    </select>
                                    @error('species')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="breed">Raza</label>
                                    <input type="text" class="form-control @error('breed') is-invalid @enderror"
                                        id="breed" name="breed" placeholder="Ingrese la raza"
                                        value="{{ old('breed') }}">
                                    @error('breed')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="gender">Género</label>
                                    <div class="d-flex">
                                        <div class="custom-control custom-radio mr-4">
                                            <input class="custom-control-input" type="radio" id="male" name="gender"
                                                value="macho" {{ old('gender') == 'macho' ? 'checked' : '' }}>
                                            <label for="male" class="custom-control-label">Macho</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="female" name="gender"
                                                value="hembra" {{ old('gender') == 'hembra' ? 'checked' : '' }}>
                                            <label for="female" class="custom-control-label">Hembra</label>
                                        </div>
                                    </div>
                                    @error('gender')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="birth_date">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                        id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                                    @error('birth_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="weight">Peso (kg)</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('weight') is-invalid @enderror" id="weight"
                                        name="weight" placeholder="Ingrese el peso en kg" value="{{ old('weight') }}">
                                    @error('weight')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="color">Color</label>
                                    <input type="text" class="form-control @error('color') is-invalid @enderror"
                                        id="color" name="color" placeholder="Ingrese el color"
                                        value="{{ old('color') }}">
                                    @error('color')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="microchip">Número de Microchip</label>
                                    <input type="text" class="form-control @error('microchip') is-invalid @enderror"
                                        id="microchip" name="microchip" placeholder="Opcional"
                                        value="{{ old('microchip') }}">
                                    @error('microchip')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Observaciones</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                placeholder="Observaciones sobre la mascota">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="owner_id">Propietario</label>
                            <select class="form-control @error('owner_id') is-invalid @enderror" id="owner_id"
                                name="owner_id">
                                <option value="">Seleccione un propietario</option>


                            </select>
                            @error('owner_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                <a href="" target="_blank">
                                    <i class="fas fa-plus-circle"></i> Registrar nuevo propietario
                                </a>
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="photo">Foto de la Mascota</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('photo') is-invalid @enderror"
                                        id="photo" name="photo">
                                    <label class="custom-file-label" for="photo">Elegir archivo</label>
                                </div>
                            </div>
                            @error('photo')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                        <a href="" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Mostrar el nombre del archivo seleccionado en el input file
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });

            // Inicializar select2 para el dropdown de propietarios si está disponible
            if ($.fn.select2) {
                $('#owner_id').select2({
                    placeholder: 'Seleccione un propietario',
                    width: '100%'
                });
            }
        });
    </script>
@stop

