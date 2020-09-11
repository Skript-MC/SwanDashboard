@extends("layouts.panel")

@section('title', 'Exceptions Sentry')

@section('content')
    <div class="row">
        <div class="col-lg-12 mb-3">
            <div class="card position-relative">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Rapports enregistrés par Sentry</h6>
                </div>
                <div class="card-body">
                    <p>Les rapports enregistrés par Sentry non traités sont affichés ici ; mis en cache pendant 10 minutes. Pour accéder à plus de détails, cliquez sur le rapport en question qui vous redirigera vers son récapitulatif Sentry.</p>
                    <ul class="list-group list-group-flush">
                        @foreach($reports as $report)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ $report['permalink'] }}"> {{ $report['title'] }}</a>
                                <span class="badge badge-pill badge-{{ $report['level'] == 'warning' ? 'warning' : 'danger' }}">{{ $report['count'] }} {{ $report['level'] == 'warning' ?  'avertissements' : 'erreurs' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
