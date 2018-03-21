@extends('larrock::admin.main')
@section('title') {{ $app->name }} admin @endsection

@section('content')
    <div class="container-head uk-margin-bottom">
        <div class="uk-grid">
            <div class="uk-width-expand">
                {!! Breadcrumbs::render('admin.'. $app->name .'.index') !!}
            </div>
            <div class="uk-width-auto"></div>
        </div>
    </div>

    <div class="uk-margin-large-bottom ibox-content">
        <p>TEST</p>
    </div>
@endsection