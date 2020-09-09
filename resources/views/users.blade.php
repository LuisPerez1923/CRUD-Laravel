@extends('layout')

@section('content')


    <h1>{{ $title }}</h1>
    <ul>
        @if (! empty($users))
            <ul>
                @foreach ($users as $user)
                    <li>
                        {{ $user->name }}
                        <a href="{{ route('users.show', $user) }}">Ver detalles</a> |    
                        <a href="{{ route('users.edit', $user) }}">Editar</a> 
                        <form action="{{ route('users.destroy', $user) }}" method = "POST">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type ="submit">Eliminar</button>
                        </form>
                    </li>    
                @endforeach
            </ul>
        @else
            <p class="oculto">No hay usuarios registrados</p>
        @endif
    </ul>
    
@endsection