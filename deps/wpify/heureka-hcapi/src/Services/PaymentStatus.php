<?php

namespace HeurekaDeps\Hcapi\Services;

/**
 * @author Oldřich Taufer <oldrich.taufer@heureka.cz>
 */
class PaymentStatus extends AbstractService
{
    const KEY_STATUS = 'status';
    public function __construct()
    {
        $this->validator = new \HeurekaDeps\Hcapi\Validators\PaymentStatus();
    }
}
