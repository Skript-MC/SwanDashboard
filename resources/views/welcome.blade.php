@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
    <div class="p-5">
        <div class="text-center">
            <h1 class="h4 text-gray-900 mb-4">Authentification requise</h1>
            <p>Merci de vous authentifier avec votre compte Discord afin d'accéder au panel de Swan.</p>
        </div>
        <a href="{{ route('login') }}" class="btn btn-facebook btn-block"><i class="fab fa-discord fa-fw"></i> Connexion avec Discord</a>
    </div>
@endsection
