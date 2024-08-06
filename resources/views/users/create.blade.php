@extends('layouts.app')

@section('titulo', 'Crear Usuario')

@section('css')

@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Crear Usuarios</h3>
                    <p class="text-subtitle text-muted">Formulario para registrar a un usuario/empleado.</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('users.index')}}">Usuarios</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear usuario</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('user.store')}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Nombre:</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name') }}" name="name">
                            @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="surname">Apellidos:</label>
                            <input type="text" class="form-control @error('surname') is-invalid @enderror" id="surname" value="{{ old('surname') }}" name="surname">
                            @error('surname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="username">Nombre de Usuario:</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" value="{{ old('username') }}" name="username">
                            @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email') }}" name="email">
                            @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" autocomplete="new-password" value="{{ old('password') }}" name="password">
                            @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password-confirm">{{ __('Confirmar Password') }}</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                        </div>
                        <div class="form-group">
                                <label for="access_level_id">{{ __('Rol de la App') }}</label>
                                <select class="form-select @error('access_level_id') is-invalid @enderror" id="access_level_id" name="access_level_id">
                                    <option>Seleccione el rol del usuario</option>
                                    @foreach ( $role as $rol )
                                        <option @if($rol->id == old('access_level_id')) selected @endif value="{{$rol->id}}">{{$rol->name}}</option>
                                    @endforeach
                                </select>
                                @error('access_level_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                        <div class="form-group">
                                <label for="admin_user_department_id">{{ __('Departamento del Empleado') }}</label>
                                <select class="form-select @error('admin_user_department_id') is-invalid @enderror" id="admin_user_department_id" name="admin_user_department_id">
                                    <option>Seleccione el departamento del usuario</option>
                                    @foreach ( $departamentos as $departamento )
                                        <option @if($departamento->id == old('admin_user_department_id')) selected @endif value="{{$departamento->id}}">{{$departamento->name}}</option>
                                    @endforeach
                                </select>
                                @error('admin_user_department_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                        <div class="form-group">
                                <label for="admin_user_position_id">{{ __('Posicion del Usuario') }}</label>
                                <select class="form-select @error('admin_user_position_id') is-invalid @enderror" id="admin_user_position_id" name="admin_user_position_id">
                                    <option>Seleccione la posicion del usuario</option>
                                    @foreach ( $posiciones as $posicion )
                                        <option @if($posicion->id == old('admin_user_position_id')) selected @endif value="{{$posicion->id}}">{{$posicion->name}}</option>
                                    @endforeach
                                </select>
                                @error('admin_user_position_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                        <div class="form-group mt-5">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Registrar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

@endsection

@section('scripts')

@endsection

