<?php

namespace Larrock\ComponentVscale;

use Larrock\Core\Component;
use Larrock\ComponentVscale\Facades\LarrockVscale;

class VscaleComponent extends Component
{
    public function __construct()
    {
        $this->name = 'vscale';
        $this->title = 'Vscale API';
        $this->description = 'Мост к Vscale API';
    }

    public function renderAdminMenu()
    {
        return view('larrock::admin.sectionmenu.types.default',
            ['app' => LarrockVscale::getConfig(), 'url' => '/admin/'.LarrockVscale::getName()]);
    }
}
