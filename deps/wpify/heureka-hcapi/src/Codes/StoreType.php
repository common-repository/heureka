<?php

namespace HeurekaDeps\Hcapi\Codes;

/**
 * @author Oldřich Taufer <oldrich.taufer@heureka.cz>
 */
class StoreType extends AbstractCodes
{
    const STORE_TYPE_INTERNAL = 1;
    const STORE_TYPE_HEUREKA_POINT = 2;
    const STORE_TYPE_DEPOT_API = 3;
    /**
     * Gets constants as array
     *
     * @return array
     */
    function getConstants()
    {
        return [self::STORE_TYPE_INTERNAL, self::STORE_TYPE_HEUREKA_POINT, self::STORE_TYPE_DEPOT_API];
    }
}
