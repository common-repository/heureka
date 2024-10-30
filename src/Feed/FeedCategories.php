<?php

namespace Heureka\Feed;

class FeedCategories {

	/**
	 * @param $args
	 * @param $lang
	 *
	 * @return array
	 */
	public function get_options( $args, $lang = 'cs' ): array {
		$search     = $args['search'] ?? '';
		$value      = $args['value'][0] ?? '';
		$categories = $this->get_heureka_categories( $lang );

		$cats = array();
		foreach ( $categories as $item ) {
			$name = $item['category_fullname'];
			if ( ! $name ) {
				continue;
			}

			if ( $search ) {
				if ( strpos( strtolower( $name ), strtolower( $search ) ) !== false ) {
					$cats[] = array(
						'label' => $name,
						'value' => $name,
					);
				}
			} else {
				$cats[] = array(
					'label' => $name,
					'value' => $name,
				);
			}
		}

		// Put selected first
		$selected = array_filter( $cats, function ( $item ) use ( $value ) {
			return $item['value'] === $value;
		} );
		if ( ! empty( $selected ) ) {
			$selected_key = array_key_first( $selected );
			unset( $cats[ $selected_key ] );
			$cats = array_merge( $selected, $cats );
		}

		return array_slice( $cats, 0, 50 );
	}

	public function get_heureka_categories( $lang = 'cs', $force_refresh = false ) {
		$option_name = sprintf( 'wpify_woo_feeds_heureka_categories_%s', $lang );
		$categories  = get_option( $option_name, array() );

		if ( ! $force_refresh ) {
			return $categories;
		}

		$this->update_heureka_categories();

		return $categories;
	}

	public function update_heureka_categories() {
		$xmls = array(
			array(
				'lang'        => 'cs',
				'url'         => 'https://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml',
				'option_name' => 'wpify_woo_feeds_heureka_categories_cs',
			),
			array(
				'lang'        => 'sk',
				'url'         => 'https://www.heureka.sk/direct/xml-export/shops/heureka-sekce.xml',
				'option_name' => 'wpify_woo_feeds_heureka_categories_sk',
			),
		);

		foreach ( $xmls as $xml ) {
			$this->temp_categories = array();
			$response_xml_data     = file_get_contents( $xml['url'] );
			if ( ! $response_xml_data ) {
				wp_die( __( 'Downloading of the categories XML failed, please contact your hosting provider.', 'heureka' ) );
			}

			$feed = simplexml_load_string( $response_xml_data );
			foreach ( $feed->CATEGORY as $first_level ) {
				$id                                                = (string) $first_level->CATEGORY_ID;
				$name                                              = (string) $first_level->CATEGORY_NAME;
				$this->temp_categories[ $id ]['category_id']       = $id;
				$this->temp_categories[ $id ]['category_name']     = $name;
				$this->temp_categories[ $id ]['category_fullname'] = '';
				$this->build_categories( $first_level->CATEGORY, $id );
			}
			update_option( $xml['option_name'], $this->temp_categories, 'no' );
		}
	}

	public function build_categories( $data, $category_id ) {
		if ( ! empty( $data ) ) {
			foreach ( $data as $item ) {
				$item_id       = (string) $item->CATEGORY_ID;
				$item_name     = (string) $item->CATEGORY_NAME;
				$item_fullname = (string) $item->CATEGORY_FULLNAME;

				if ( ! empty( $item_fullname ) ) {
					$this->temp_categories[ $item_id ]['category_id']       = $item_id;
					$this->temp_categories[ $item_id ]['category_name']     = $item_name;
					$this->temp_categories[ $item_id ]['category_fullname'] = $item_fullname;
				}

				$this->build_categories( $item->CATEGORY, $category_id );
			}
		}
	}

	public function categories_loop( $data, $categories ) {
		foreach ( $data as $item ) {
			if ( ! empty( $item->children ) ) {
				$categories = $this->categories_loop( $item->children, $categories );
			} else {
				$categories[] = $item->categoryText;
			}
		}

		return $categories;
	}
}
