@extends("layouts.panel")

@section('title', 'Exceptions Sentry')

@section('content')
    <div class="row">
        <div class="col-lg-12 mb-3">
            <div class="card position-relative">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Exceptions enregistrées par Sentry</h6>
                </div>
                <div class="card-body">
                    <p>Les exceptions enregistrées par Sentry non traitées sont affichées ici ; mises en cache pendant 10 minutes. Pour accéder à plus de détails, cliquez sur l'exception qui vous redirigera vers le tableau de bord de Sentry.</p>
                    <ul class="list-group list-group-flush">
                        @foreach($exceptions as $exception)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ $exception['permalink'] }}"> {{ $exception['title'] }}</a>
                                <span class="badge badge-pill badge-{{ $exception['level'] == 'warning' ? 'warning' : 'danger' }}">{{ $exception['count'] }} {{ $exception['level'] == 'warning' ?  'avertissements' : 'erreurs' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
