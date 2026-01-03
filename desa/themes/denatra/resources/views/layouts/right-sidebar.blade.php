@extends('theme::template')

@includeIf('theme::commons.right_sidebar_css')

@section('layout')
<div class="default-row mt-20">
	<div class="container-custom" style="margin-bottom:10px;">
        <div class="row-custom mlr-min-20">
            @if(!in_array(request()->segment(1), ['', 'first', 'index']))
                <div class="pageleft">
                    @yield('content')
                </div>
                <div class="pageright">
                    @includeIf('theme::partials.home.widgets')
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
