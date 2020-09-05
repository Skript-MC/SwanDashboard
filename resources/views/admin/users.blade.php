@extends('layouts.panel')

@section('title', 'Gestion des utilisateurs')

@section('content')
    <div class="card position-relative">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Base de donnée des utilisateurs</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Nom d'utilisateur</th>
                        <th scope="col">Rôle</th>
                        <th scope="col">Identifiant Discord</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td><img class="img-profile rounded-circle img-fluid h-25" src="{{ $user->avatar }}" alt="User avatar"> {{ $user->name }}</td>
                        <td>{{ \App\Utils\BadgeUtil::getBadge($user->rank) }}</td>
                        <td>{{ $user->id }}</td>
                        <td><a href="/users/{{ $user->id }}" type="button" class="btn btn-info"><i class="fas fa-user-edit"></i></a></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
