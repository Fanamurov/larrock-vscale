<?php

namespace Larrock\ComponentVscale;

use Illuminate\Routing\Controller;
use Larrock\Core\Traits\AdminMethodsIndex;
use LarrockVscale;

class AdminVscaleController extends Controller
{
    //use AdminMethodsIndex;

    public function __construct()
    {
        $this->shareMethods();
        $this->middleware(LarrockVscale::combineAdminMiddlewares());
        $this->config = LarrockVscale::shareConfig();
        \Config::set('breadcrumbs.view', 'larrock::admin.breadcrumb.breadcrumb');
    }

    public function index()
    {
        $data = [];
        return view('larrock::admin.vscale.index', $data);
    }
}