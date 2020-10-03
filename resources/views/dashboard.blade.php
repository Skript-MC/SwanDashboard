@extends('layouts.panel')

@section('title', 'Tableau de bord')

@section('content')
    <div class="container-fluid">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Rapports Sentry</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $sentry }} rapport{{ $sentry > 1 ? 's' : '' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <script src="{{ asset(mix('js/chartjs.js')) }}"></script>
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="commands-stats" height="80"></canvas>
                        <script>
                            // Bar chart
                            new Chart(document.getElementById("commands-stats"), {
                                type: 'bar',
                                data: {
                                    labels: {!! json_encode($commands['names']) !!},
                                    datasets: [
                                        {
                                            label: "Nombre d'utilisation",
                                            data: {!! json_encode($commands['values']) !!}
                                        }
                                    ]
                                },
                                options: {
                                    legend: { display: false }
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
