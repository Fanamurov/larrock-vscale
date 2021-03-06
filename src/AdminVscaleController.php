<?php

namespace Larrock\ComponentVscale;

use LarrockVscale;
use Illuminate\Routing\Controller;
use Larrock\ComponentVscale\Helpers\Vscale;

class AdminVscaleController extends Controller
{
    protected $vscale;

    public function __construct()
    {
        $this->middleware(LarrockVscale::combineAdminMiddlewares());
        LarrockVscale::shareConfig();
        \Config::set('breadcrumbs.view', 'larrock::admin.breadcrumb.breadcrumb');
        $this->vscale = new Vscale();
    }

    public function index()
    {
        $data = [];
        if (env('VSCALE_TOKEN') !== null) {
            $data['balance'] = $this->vscale->balance();
            $data['scalets'] = $this->vscale->scalets();
            $data['backups'] = $this->vscale->backups();
        } else {
            $data['balance'] = null;
        }

        return view('larrock::admin.vscale.index', $data);
    }

    /**
     * Создание бекапа.
     *
     * @param $ctid
     * @return $this
     * @throws \Exception
     */
    public function backup($ctid)
    {
        $this->vscale->backup($ctid);

        return back()->withInput();
    }

    /**
     * Восстановление сервера из бекапа.
     *
     * @return $this
     * @throws \Exception
     */
    public function rebuild($ctid)
    {
        $this->vscale->rebuild($ctid);

        return back()->withInput();
    }
}
