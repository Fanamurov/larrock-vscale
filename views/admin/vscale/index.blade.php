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

    @if( !$balance)
        <p class="uk-alert uk-alert-danger">Что-то пошло не так. Данные от API Vscale не получены, убедитесь в
            наличии подключения к интернету и корректности API-token в VSCALE_TOKEN(.env)</p>
    @else
        <p class="uk-alert @if($balance->summ < 0) uk-alert-danger @else uk-alert-success @endif">
            Баланс: {{ $balance->summ }} руб. Статус: {{ $balance->status }}</p>

        <div class="uk-margin-large-bottom ibox-content">
            <h3>Сервера:</h3>
            <table class="uk-table">
                <thead>
                <tr>
                    <th>Имя сервера</th>
                    <th>ID</th>
                    <th>Дата создания</th>
                    <th>Hostname</th>
                    <th>Тариф</th>
                    <th>Статус</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($scalets as $scalet)
                    <tr>
                        <td>{{ $scalet->name }}</td>
                        <td>{{ $scalet->ctid }}</td>
                        <td>{{ $scalet->created }}</td>
                        <td>{{ $scalet->hostname }}</td>
                        <td>{{ $scalet->rplan }}</td>
                        <td>{{ $scalet->status }}</td>
                        <td>
                            <a href="{{ route('admin.vscale.backup', ['ctid' => $scalet->ctid]) }}" class="uk-button uk-button-default uk-button-small">Создать бекап</a>
                        </td>
                    </tr>
                    @if(\count($backups) > 0)
                        @foreach($backups as $backup)
                            <tr>
                                <td colspan="7">
                                    @if($backup->scalet === $scalet->ctid)
                                        <table class="uk-table">
                                            <thead>
                                            <tr>
                                                <th><strong>Бекап:</strong> дата создания</th>
                                                <th>Название</th>
                                                <th>Размер</th>
                                                <th>Статус</th>
                                                <th>Активность</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>Бекап {{ $backup->created }}</td>
                                                <td>{{ $backup->name }}</td>
                                                <td>{{ $backup->size }}GB</td>
                                                <td>{{ $backup->status }}</td>
                                                <td>{{ $backup->active }}</td>
                                                <td>
                                                    <a href="{{ route('admin.vscale.rebuild', ['ctid' => $backup->scalet]) }}" class="uk-button uk-button-danger uk-button-small">Восстановить</a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <p class="uk-alert uk-alert-danger">Бекапов нет</p>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="uk-margin-large-bottom ibox-content">
            <h3>Сrontab. Создание бекапов серверов каждое первое число любого месяца</h3>
            <p>Выполните "crontab -e" в консоли и внесите в конец списка следующую команду:</p>
            @foreach($scalets as $scalet)
                <p>Сервер {{ $scalet->name }}</p>
                <div class="uk-form">
                    <input class="uk-input uk-form-large uk-width-1-1" type="text" disabled
                           value="@monthly {{ route('vscale.backup', ['APP_KEY' => md5(env('APP_KEY')), 'ctid' => $scalet->ctid]) }}">
                    <p>По-молчанию скрипт удаляет бекапы созданные более {{ env('VSCALE_MONTH_DELETE', 3) }} месяцев назад</p>
                </div>
            @endforeach
        </div>
    @endif
@endsection