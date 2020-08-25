@extends('layouts.panel')

@section('title', 'Mon profil')

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card position-relative">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Mes données</h6>
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="discordNickname">Nom d'utilisateur</span>
                    </div>
                    <input type="text" class="form-control" placeholder="{{ $user->nickname }}" aria-label="Nom d'utilisateur" aria-describedby="discordNickname" disabled>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="discordUsername">Pseudonyme</span>
                    </div>
                    <input type="text" class="form-control" placeholder="{{ $user->name }}" aria-label="Pseudonyme" aria-describedby="discordUsername" disabled>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="discordAvatar">Avatar</span>
                    </div>
                    <input type="text" class="form-control" placeholder="{{ $user->avatar }}" aria-label="Avatar" aria-describedby="discordAvatar" disabled>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01">Rôle</label>
                    </div>
                    <select class="custom-select" id="inputGroupSelect01" disabled>
                        <option {{ $user->rank === 1 ? 'selected' : '' }}>Invité</option>
                        <option {{ $user->rank === 2 ? 'selected' : '' }}>Membre</option>
                        <option {{ $user->rank === 3 ? 'selected' : '' }}>Staff</option>
                        <option {{ $user->rank === 4 ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <button type="button" class="btn btn-danger" disabled><i class="fas fa-trash-alt"></i> Supprimer mon compte (désactivé)</button>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card position-relative">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Authentification à deux facteurs</h6>
            </div>
            <div class="card-body">
                <p>Cette fonctionnalité n'est pas encore disponible.</p>
            </div>
        </div>
    </div>
</div>
@endsection
