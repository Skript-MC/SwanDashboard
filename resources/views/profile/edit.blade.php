@extends("layouts.panel")

@section('title', 'Édition de profil')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow position-relative">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Données de l'utilisateur</h6>
                    </div>
                    <div class="card-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success">
                                {{ session()->get('success') }}
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger">
                                {{ session()->get('error') }}
                            </div>
                        @endif
                        <form method="POST" action="/users/{{ $user->id }}/edit">
                            @csrf
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="discordId">Identifiant</span>
                                </div>
                                <input type="text" name="discordId" class="form-control" value="{{ $user->id }}" aria-label="Identifiant" aria-describedby="discordId" readonly>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="discordNickname">Nom d'utilisateur</span>
                                </div>
                                <input type="text" name="discordNickname" class="form-control" value="{{ $user->nickname }}" aria-label="Nom d'utilisateur" aria-describedby="discordNickname">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="discordUsername">Pseudonyme</span>
                                </div>
                                <input type="text" name="discordUsername" class="form-control" value="{{ $user->name }}" aria-label="Pseudonyme" aria-describedby="discordUsername">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="discordAvatar">Avatar</span>
                                </div>
                                <input type="text" name="discordAvatar" class="form-control" value="{{ $user->avatar }}" aria-label="Avatar" aria-describedby="discordAvatar">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="dashRole">Rôle</label>
                                </div>
                                <select name="dashRole" class="custom-select" id="dashRole">
                                    <option value="1" {{ $user->rank === 1 ? 'selected' : '' }}>Invité</option>
                                    <option value="2" {{ $user->rank === 2 ? 'selected' : '' }}>Membre</option>
                                    <option value="3" {{ $user->rank === 3 ? 'selected' : '' }}>Staff</option>
                                    <option value="4" {{ $user->rank === 4 ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info"><i class="fas fa-save"></i> Sauvegarder les modifications</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow position-relative">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Authentification à deux facteurs</h6>
                    </div>
                    <div class="card-body">
                        <p>Cette fonctionnalité n'est pas encore disponible.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
