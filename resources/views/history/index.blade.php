@extends("layouts.panel")

@section('title', '')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3 mb-3">
                <div class="card shadow position-relative">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Salons</h6>
                    </div>

                    <div class="list-group list-group-flush">
                        @foreach($channels as $channel)
                            <a href="{{ route('history') }}/{{ $channel }}" class="list-group-item list-group-item-action {{ request()->route()->parameter('channelName') === $channel ? 'active' : '' }}">
                                #{{ $channel }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="card shadow position-relative">
                    <div class="card-body">
                        @if(count($messages ?? []) != 0)
                            @foreach($messages as $message)
                                <div class="px-0">
                                    <div class="px-4 py-2 bg-white">
                                        <div class="media table-responsive">
                                            <img src="{{ $message->avatarUrl }}" width="50" class="rounded-circle">
                                            <div class="ml-3">
                                                <div class="bg-light rounded py-2 px-3 mb-2">
                                                    <p class="text-small mb-0">{{ $message->content }}</p>
                                                </div>
                                                <p class="small"><strong>{{ $message->userName }}</strong> ({{ (int) $message->userId }}), <strong>le {{ date("d/m/yy à h:m A", \App\Utils\DiscordSnowflake::getTimestampOfSnowflake($message->messageId)) }}</strong> ({{ (int) $message->messageId }})</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            {{ $messages->links() }}
                        @else
                            Aucun message disponible.
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
