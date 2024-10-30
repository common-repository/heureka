<?php

namespace Heureka\Feed;

use Heureka\Repositories\SettingsRepository;
use Heureka\Settings;

class FeedProductCz extends FeedProduct {

	const FEED_NAME = 'heureka_cz';
	const FEED_TITLE = 'Heureka CZ';

	public function global_settings_section() {
		return Settings::SECTION_FEED_PRODUCTS_CZ;
	}

	public function language_suffix() {
		return 'cs';
	}

	public function feed_title() {
		return self::FEED_TITLE;
	}

	public function feed_name() {
		return self::FEED_NAME;
	}

	public function get_country_code(): string {
		return 'CZ';
	}
}
