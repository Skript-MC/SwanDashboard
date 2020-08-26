@extends('layouts.app')

@section('content')
    <div class="p-5">
        <div class="text-center">
            <h1 class="h4 text-gray-900 mb-4"><i class="fas fa-@yield('fa')"></i> @yield('title')</h1>
            <p>@yield('message')</p>
        </div>
        @if(view()->hasSection('redirect'))
        <a href="{{ url()->previous() }}" class="btn btn-facebook btn-block"><i class="fas fa-long-arrow-alt-left"></i> Retour à la page précédente</a>
        @endif
    </div>
@endsection
