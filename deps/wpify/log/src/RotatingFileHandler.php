<?php

namespace HeurekaDeps\Wpify\Log;

class RotatingFileHandler extends \HeurekaDeps\Monolog\Handler\RotatingFileHandler
{
    public function get_glob_pattern() : string
    {
        return $this->getGlobPattern();
    }
}
