<?php

namespace IRFANM\SIMAHU\Model;

use IRFANM\SIMAHU\Domain\Hutang;

class HutangCreateResponse
{
    public Hutang $hutang;
    
    public function __construct(Hutang $hutang)
    {
        $this->hutang = $hutang;
    }
}