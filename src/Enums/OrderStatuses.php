<?php

namespace Heureka\Enums;

use HeurekaDeps\Hcapi\Codes\OrderStatus;

class OrderStatuses {

	public static function get() {
		return array(
			array(
				'label' => __( 'Shipped', 'heureka' ),
				'value' => OrderStatus::STATUS_SHIPPED,
			),
			array(
				'label' => __( 'Sent to shop', 'heureka' ),
				'value' => OrderStatus::STATUS_SENT_TO_SHOP,
			),
			array(
				'label' => __( 'Partially completed', 'heureka' ),
				'value' => OrderStatus::STATUS_PARTIALLY_COMPLETED,
			),
			array(
				'label' => __( 'Shop Confirmed', 'heureka' ),
				'value' => OrderStatus::STATUS_SHOP_CONFIRMED,
			),
			array(
				'label' => __( 'Shop Cancelled', 'heureka' ),
				'value' => OrderStatus::STATUS_CANCEL_SHOP,
			),
			array(
				'label' => __( 'Customer Cancelled', 'heureka' ),
				'value' => OrderStatus::STATUS_CANCEL_CUSTOMER,
			),
			array(
				'label' => __( 'Cancelled - Unpaid', 'heureka' ),
				'value' => OrderStatus::STATUS_CANCEL_UNPAID,
			),
			array(
				'label' => __( 'Returned', 'heureka' ),
				'value' => OrderStatus::STATUS_RETURNED,
			),
			array(
				'label' => __( 'Completed on Heureka', 'heureka' ),
				'value' => OrderStatus::STATUS_COMPLETED_ON_HEUREKA,
			),
			array(
				'label' => __( 'Delivered', 'heureka' ),
				'value' => OrderStatus::STATUS_DELIVERED,
			),
			array(
				'label' => __( 'Ready for pickup', 'heureka' ),
				'value' => OrderStatus::STATUS_READY_FOR_PICKUP,
			),
			array(
				'label' => __( 'Shipped to external point', 'heureka' ),
				'value' => OrderStatus::STATUS_SHIPPED_TO_EXTERNAL_POINT,
			),
		);
	}
}
