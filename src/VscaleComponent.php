<?php

namespace Larrock\ComponentVscale;

use Larrock\Core\Component;

class VscaleComponent extends Component
{
    public function __construct()
    {
        $this->name = 'vscale';
        $this->title = 'Vscale API';
        $this->description = 'Мост к Vscale API';
    }
}