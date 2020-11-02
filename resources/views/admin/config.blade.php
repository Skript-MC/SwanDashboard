@extends('layouts.panel')

@section('title', 'Configuration Dashboard')

@section('content')
    <div class="container-fluid">
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
                            <input type="hidden" name="name" value="pagination">
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
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Salons à archiver</h6>
                    </div>
                    <div class="card-body">
                        @if(session()->has('log_channels'))
                            <div class="alert alert-success">
                                {{ session()->get('log_channels') }}
                            </div>
                        @endif
                        <p>Vous pouvez personnaliser les salons auxquels Swan enregistre les messages. Vous pourrez par la suite consulter l'historique de ceux-ci dans la catégorie <a href="{{ route("history") }}">Historique</a>.</p>
                        <form method="POST" action="/config/dashboard/edit">
                            @csrf
                            <input type="hidden" name="name" value="log_channels">
                            <div id="log_channels">
                                @foreach($log_channels as $channel)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="log_channels[]" placeholder="Nom du salon" value="{{ $channel }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-danger link-remove" type="button">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mb-3">
                                <button type="button" id="addChannelButton" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus"></i> Ajouter
                                </button>
                            </div>
                            <button type="submit" class="btn btn-info"><i class="fas fa-save"></i> Sauvegarder les modifications</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function addButtonListener(button) {
            button.addEventListener('click', () => {
                const element = button.parentNode.parentNode;

                element.parentNode.removeChild(element);
            });
        }

        document.querySelectorAll('.link-remove').forEach((button) => addButtonListener(button));

        document.getElementById('addChannelButton').addEventListener('click', () => {
            const input = '<div class="input-group mb-2">\n' +
                '<input type="text" class="form-control" name="log_channels[]" placeholder="Nom du salon">\n' +
                '<div class="input-group-append">\n' +
                '<button class="btn btn-outline-danger link-remove" type="button">\n' +
                '<i class="fas fa-times"></i>\n' +
                '</button>\n' +
                '</div>\n' +
                '</div>'
            const newElement = document.createElement('div');
            newElement.innerHTML = input;
            addButtonListener(newElement.querySelector('.link-remove'));
            document.getElementById('log_channels').appendChild(newElement);
        })
    </script>
@endsection
