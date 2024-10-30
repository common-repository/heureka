<?php

namespace Heureka\Enums;

use HeurekaDeps\Hcapi\Codes\DeliveryType;

class DeliveryTypes {

	static function get() {
		return array(
			array(
				'label' => __( 'Personal pickup', 'heureka' ),
				'value' => DeliveryType::DELIVERY_TYPE_PERSONAL_PICKUP,
			),
			array(
				'label' => __( 'Czech post', 'heureka' ),
				'value' => DeliveryType::DELIVERY_TYPE_CZECH_POST,
			),
			array(
				'label' => __( 'PPL / DPD / Přepravní služba', 'heureka' ),
				'value' => DeliveryType::DELIVERY_TYPE_PPL_DPD,
			),
			array(
				'label' => __( 'Express delivery', 'heureka' ),
				'value' => DeliveryType::DELIVERY_TYPE_EXPRESS,
			),
			array(
				'label' => __( 'Special delivery', 'heureka' ),
				'value' => DeliveryType::DELIVERY_TYPE_SPECIAL,
			),
//			array(
//				'label' => __( 'Czech post - Na poštu', 'heureka' ),
//				'value' => DeliveryType::DELIVERY_TYPE_CZECH_POST_SPECIAL,
//			),
			array(
				'label' => __( 'Depot API Delivery', 'heureka' ),
				'value' => 9,
			),
		);
	}
}
