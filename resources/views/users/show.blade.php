@extends('layout')

@section('content')
    <div class="descripcion-usuario">
        <h2>Usuario #{{ $user->id }}</h2>

        <p>Nombre del usuario: {{ $user->name }}</p>
        <p>Correo electrónico: {{ $user->email }}</p>

        <p>
            <a href="{{ route('users') }}">Regresar</a>
        </p>
    </div>
@endsection


