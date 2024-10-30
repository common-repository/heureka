<?php

namespace Heureka\Enums;

class WeekDays {

	public static function get() {
		return array(
			array(
				'value' => 1,
				'label' => __( 'Monday', 'heureka' ),
			),
			array(
				'value' => 2,
				'label' => __( 'Tuesday', 'heureka' ),
			),
			array(
				'value' => 3,
				'label' => __( 'Wednesday', 'heureka' ),
			),
			array(
				'value' => 4,
				'label' => __( 'Thursday', 'heureka' ),
			),
			array(
				'value' => 5,
				'label' => __( 'Friday', 'heureka' ),
			),
			array(
				'value' => 6,
				'label' => __( 'Saturday', 'heureka' ),
			),
			array(
				'value' => 7,
				'label' => __( 'Sunday', 'heureka' ),
			),
		);
	}
}
