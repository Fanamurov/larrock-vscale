<?php

namespace Larrock\ComponentVscale;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Larrock\ComponentVscale\Helpers\Vscale;

class VscaleController extends Controller
{
    /**
     * Создание бекапа
     *
     * @param $appKey
     * @param $ctid
     * @throws \Exception
     */
    public function backup($ctid, $appKey)
    {
        if($appKey !== md5(env('APP_KEY'))){
            throw new \InvalidArgumentException('Не все параметры переданы верно');
        }

        \Cache::forget(sha1('vscaleBackups'));
        $vscale = new Vscale();
        $this->removeBackups($appKey);
        if($vscale->backup($ctid)){
            echo 'Backup for '. $ctid .' successfully created';
        }
        \Cache::forget(sha1('vscaleBackups'));
    }

    /**
     * Удаление старых бекапов
     *
     * @param $appKey
     * @throws \InvalidArgumentException
     */
    public function removeBackups($appKey)
    {
        if($appKey !== md5(env('APP_KEY'))){
            throw new \InvalidArgumentException('Не все параметры переданы верно');
        }

        $vscale = new Vscale();
        $backups = $vscale->backups();
        foreach ($backups as $backup){
            //По-молчанию скрипт удаляет бекапы созданные более 3 месяцев назад
            $created = Carbon::parse($backup->created)->addMonth(env('VSCALE_MONTH_DELETE', 3));
            if(Carbon::now() > $created && $vscale->removeBackup($backup->id)){
                echo 'BACKUP '. $backup->id .' removed';
            }
        }
    }
}