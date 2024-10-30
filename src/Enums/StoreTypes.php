<?php

namespace Heureka\Enums;

use HeurekaDeps\Hcapi\Codes\StoreType;

class StoreTypes {

	public static function get() {
		return array(
			array(
				'label' => __( 'Internal', 'heureka' ),
				'value' => StoreType::STORE_TYPE_INTERNAL,
			),
			array(
				'label' => __( 'Pickup point', 'heureka' ),
				'value' => StoreType::STORE_TYPE_HEUREKA_POINT,
			),
			array(
				'label' => __( 'Depot API', 'heureka' ),
				'value' => StoreType::STORE_TYPE_DEPOT_API,
			),
		);
	}
}
