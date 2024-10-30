<?php

use HeurekaDeps\DI\Definition\Helper\CreateDefinitionHelper;
use HeurekaDeps\Wpify\CustomFields\CustomFields;
use HeurekaDeps\Wpify\Log\RotatingFileLog;
use HeurekaDeps\Wpify\PluginUtils\PluginUtils;

return array(
	CustomFields::class    => ( new CreateDefinitionHelper() )
		->constructor( plugins_url( 'deps/wpify/custom-fields', __FILE__ ) ),
	PluginUtils::class     => ( new CreateDefinitionHelper() )
		->constructor( __DIR__ . '/heureka.php' ),
	RotatingFileLog::class => ( new CreateDefinitionHelper() )
		->constructor( 'heureka' ),
);
