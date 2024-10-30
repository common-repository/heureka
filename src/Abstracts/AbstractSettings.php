<?php

namespace Heureka\Abstracts;

use Heureka\Repositories\SettingsRepository;
use HeurekaDeps\Wpify\CustomFields\CustomFields;
use WP_Term;

abstract class AbstractSettings {

	public $wcf;
	public $settings_repository;

	public function __construct( CustomFields $wcf, SettingsRepository $settings_repository ) {
		$this->wcf                 = $wcf;
		$this->settings_repository = $settings_repository;
		$this->setup();
	}

	abstract public function setup();

	/**
	 * Get category full path.
	 *
	 * @param WP_Term $category Category.
	 *
	 * @return string
	 */
	public function get_category_full_path( WP_Term $category ): string {
		$path   = $this->get_category_parent_path( $category->parent );
		$path[] = $category->name;

		return implode( ' -> ', $path );
	}

	/**
	 * Get category parent path.
	 *
	 * @param int $parent_id Parent category ID.
	 *
	 * @return array
	 */
	private function get_category_parent_path( int $parent_id ): array {
		$path = array();
		if ( 0 !== $parent_id ) {
			$parent_category = get_term( $parent_id, 'product_cat' );

			if ( 0 !== $parent_category->parent ) {
				$path = array_merge( $this->get_category_parent_path( $parent_category->parent ), $path );

			}
			$path[] = $parent_category->name;
		}

		return $path;
	}

}
