<?php

namespace Heureka\Feed;

use Heureka\Settings;

class FeedProductSk extends FeedProduct {

	const FEED_NAME = 'heureka_sk';
	const FEED_TITLE = 'Heureka SK';

	public function global_settings_section() {
		return Settings::SECTION_FEED_PRODUCTS_SK;
	}

	public function language_suffix() {
		return 'sk';
	}

	public function feed_title() {
		return self::FEED_TITLE;
	}

	public function feed_name() {
		return self::FEED_NAME;
	}

	public function category_key() {
		return 'category_sk_';
	}

	public function get_country_code(): string {
		return 'SK';
	}
}
