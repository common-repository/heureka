<?php

namespace Heureka\Enums;

use HeurekaDeps\Hcapi\Codes\PaymentType;

class PaymentTypes {

	public static function get() {
		return array(
			array(
				'label' => __( 'Cash on delivery', 'heureka' ),
				'value' => PaymentType::PAYMENT_CASH_ON_DELIVERY,
			),
			array(
				'label' => __( 'Cash', 'heureka' ),
				'value' => PaymentType::PAYMENT_CASH,
			),
			array(
				'label' => __( 'Credit Card', 'heureka' ),
				'value' => PaymentType::PAYMENT_CREDIT_CARD,
			),
			array(
				'label' => __( 'Bank Transfer', 'heureka' ),
				'value' => PaymentType::PAYMENT_BANK_TRANSFER,
			),
		);
	}
}
