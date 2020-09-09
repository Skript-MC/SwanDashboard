@extends('layouts.panel')

@section('title', 'Configuration Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pagination</h6>
                </div>
                <div class="card-body">
                    @if(session()->has('pagination'))
                        <div class="alert alert-success">
                            {{ session()->get('pagination') }}
                        </div>
                    @endif
                    <p>Afin d'accélérer le temps de chargement des pages et alléger les requêtes à la base de données, vous pouvez personnaliser ici le nombre d'éléments à afficher pour chaque page.</p>
                    <form method="POST" action="/config/dashboard/edit">
                        @csrf
                        <input type="hidden" name="name" value="pagination" >
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="pagination">Éléments par page</span>
                            </div>
                            <input type="text" name="pagination" class="form-control" value="{{ $pagination }}" aria-label="Éléments par page" aria-describedby="pagination">
                        </div>
                        <button type="submit" class="btn btn-info"><i class="fas fa-save"></i> Sauvegarder les modifications</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
