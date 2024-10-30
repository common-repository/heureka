<?php

namespace Heureka\Managers;

use Heureka\Feed\FeedAvailability;
use Heureka\Feed\FeedProductCz;
use Heureka\Feed\FeedProductSk;

class FeedsManager {

	private FeedProductCz $feed_product_cz;
	private FeedProductSk $feed_product_sk;
	private FeedAvailability $feed_availability;

	public function __construct(
		FeedProductCz $feed_product_cz,
		FeedProductSk $feed_product_sk,
		FeedAvailability $feed_availability
	) {
		$this->feed_product_cz   = $feed_product_cz;
		$this->feed_product_sk   = $feed_product_sk;
		$this->feed_availability = $feed_availability;
	}

	public function get_feeds() {
		$reflect = new \ReflectionClass( $this );
		$props   = $reflect->getProperties( \ReflectionProperty::IS_PRIVATE );
		$feeds   = [];
		foreach ( $props as $prop ) {
			$prop->setAccessible( true );
			$feeds[] = $prop->getValue( $this );
		}

		return $feeds;
	}

	public function get_feed_by_id( string $id ) {
		$reflect = new \ReflectionClass( $this );
		$props   = $reflect->getProperties( \ReflectionProperty::IS_PRIVATE );
		foreach ( $props as $prop ) {
			$prop->setAccessible( true );
			$object = $prop->getValue( $this );
			if ( $id === $object->feed_name() ) {
				return $object;
			}
		}

		return null;
	}
}
