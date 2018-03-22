<?php

namespace Larrock\ComponentVscale\Helpers;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Cache;
use Larrock\Core\Helpers\MessageLarrock;

class Vscale
{
    protected $token;

    public function __construct()
    {
        $this->token = env('VSCALE_TOKEN');
    }

    /**
     * Billing - Просмотр информации о текущем состоянии баланса
     *
     * @see https://developers.vscale.io/documentation/api/v1/#api-Billing-GetBalance
     * @return mixed
     */
    public function balance()
    {
        $cache_key = sha1('vscaleBalance');
        return Cache::remember($cache_key, 60, function () {
            $client = new Client();
            $response = $client->get('https://api.vscale.io/v1/billing/balance', [
                'headers' => [
                    'Accept'     => 'application/json',
                    'X-Token'    => $this->token
                ]
            ]);

            if($response->getStatusCode() === 200){
                $body = json_decode($response->getBody()->getContents());
                $body->summ = (float) $body->summ / 100;
                $body->balance = (float) $body->balance / 100;
                return $body;
            }
            return null;
        });
    }

    /**
     * Servers - Получение списка серверов
     *
     * @see https://developers.vscale.io/documentation/api/v1/#api-Servers-GetScalet
     * @return mixed
     */
    public function scalets()
    {
        $cache_key = sha1('vscaleScalets');
        return Cache::remember($cache_key, 60, function () {
            $client = new Client();
            $response = $client->get('https://api.vscale.io/v1/scalets', [
                'headers' => [
                    'Accept'     => 'application/json',
                    'X-Token'    => $this->token
                ]
            ]);

            if($response->getStatusCode() === 200){
                return json_decode($response->getBody()->getContents());
            }
            return null;
        });
    }

    /**
     * Backups - Просмотр списка резервных копий
     *
     * @see https://developers.vscale.io/documentation/api/v1/#api-Backups-ViewBackupsList
     * @return mixed
     */
    public function backups()
    {
        $cache_key = sha1('vscaleBackups');
        return Cache::remember($cache_key, 60, function () {
            $client = new Client();
            $response = $client->get('https://api.vscale.io/v1/backups', [
                'headers' => [
                    'Accept'     => 'application/json',
                    'X-Token'    => $this->token
                ]
            ]);

            if($response->getStatusCode() === 200){
                $body = json_decode($response->getBody()->getContents());
                foreach ($body as $key => $item){
                    if($item->active === true){
                        $body[$key]->active = 'Активно';
                    }else{
                        $body[$key]->active = 'Не активно';
                    }
                }
                return $body;
            }
            return null;
        });
    }

    /**
     * Servers - Просмотр информации о сервере
     *
     * @see https://developers.vscale.io/documentation/api/v1/#api-Servers-GetScaletCtid
     * @param $ctid
     * @return mixed
     */
    public function scaletInfo($ctid)
    {
        $cache_key = sha1('vscaleScaletInfo'. $ctid);
        return Cache::remember($cache_key, 60, function () use ($ctid) {
            $client = new Client();
            $response = $client->get('https://api.vscale.io/v1/scalets/'. $ctid, [
                'headers' => [
                    'Accept'     => 'application/json',
                    'X-Token'    => $this->token
                ]
            ]);

            if($response->getStatusCode() === 200){
                return json_decode($response->getBody()->getContents());
            }
            return null;
        });
    }

    /**
     * Servers - Создание резервной копии
     *
     * @see https://developers.vscale.io/documentation/api/v1/#api-Servers-CreatingBackupCopy
     * @param $ctid
     * @return mixed|null
     * @throws \Exception
     */
    public function backup($ctid)
    {
        $client = new Client();
        $response = $client->post('https://api.vscale.io/v1/scalets/'. $ctid .'/backup', [
            'headers' => [
                'Accept'     => 'application/json',
                'X-Token'    => $this->token
            ],
            'json' => [
                'name' => 'backup'. date('YmdHis') .'_'. $ctid
            ]
        ]);

        if($response->getStatusCode() === 200){
            $body = json_decode($response->getBody()->getContents());
            MessageLarrock::success('Бекап '. $body->id .' создан ' . $body->created, TRUE);
            return $body;
        }
        return null;
    }


    /**
     * Servers - Восстановление сервера из резервной копии
     * TODO: нужно использовать этот метод через ajax запрос, затем начать раз в n-секунд проверять доступность сайта,
     * как только сайт станет доступен - перезагрузить страницу
     *
     * @see https://developers.vscale.io/documentation/api/v1/#api-Servers-RestoreServerBackup
     * @param $ctid
     * @return mixed|null
     * @throws \Exception
     */
    public function rebuild($ctid)
    {
        $client = new Client();
        $response = $client->patch('https://api.vscale.io/v1/scalets/'. $ctid .'/rebuild', [
            'headers' => [
                'Accept'     => 'application/json',
                'X-Token'    => $this->token
            ]
        ]);

        if($response->getStatusCode() === 200){
            //Особо не имеет смысла, некоторое время сервер будет недоступен
            $body = json_decode($response->getBody()->getContents());
            MessageLarrock::success('Сервер будет восстановлен из бекапа в течении минуты', TRUE);
            return $body;
        }
        return null;
    }

    /**
     * Backups - Удаление резервной копии
     *
     * @see https://developers.vscale.io/documentation/api/v1/#api-Backups-BackupDelete
     * @param $backupId
     * @return mixed|null
     */
    public function removeBackup($backupId)
    {
        $client = new Client();
        $response = $client->delete('https://api.vscale.io/v1/backups/'. $backupId, [
            'headers' => [
                'Accept'     => 'application/json',
                'X-Token'    => $this->token
            ]
        ]);

        if($response->getStatusCode() === 200){
            $body = json_decode($response->getBody()->getContents());
            MessageLarrock::success('Бекап '. $backupId .' удален', TRUE);
            return $body;
        }
        return null;
    }
}