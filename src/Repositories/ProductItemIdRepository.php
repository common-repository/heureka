<?php

namespace Heureka\Repositories;

use Heureka\Models\ProductItemIdModel;
use HeurekaDeps\Wpify\Model\Abstracts\AbstractDbTableRepository;

class ProductItemIdRepository extends AbstractDbTableRepository {

	const COLUMN_ID = 'id';
	const COLUMN_PRODUCT_ID = 'product_id';
	const COLUMN_ITEM_ID = 'item_id';
	const COLUMN_META_KEY = 'meta_key';
	const COLUMN_CREATED_AT = 'created_at';
	const COLUMN_UPDATED_AT = 'updated_at';
	const COLUMN_DELETED_AT = 'deleted_at';

	public static $table = 'heureka_product_item_ids';

	public static function table(): string {
		global $wpdb;

		return $wpdb->prefix . self::$table;
	}

	public function create_table() {
		$charset_collate = $this->db->get_charset_collate();
		$table           = $this->table();

		$sql = "CREATE TABLE `$table` (
			`id` int NOT NULL AUTO_INCREMENT,
			`product_id` int(11) NOT NULL,
			`item_id` varchar(255) NOT NULL,
			`meta_key` varchar(255) NOT NULL,
			`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
			`deleted_at` datetime DEFAULT NULL,
			PRIMARY KEY (`id`)
		  ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	public function drop_table() {
		$table_name = $this->table();
		$sql        = "DROP TABLE IF EXISTS $table_name;";
		$this->db->query( $sql );
	}

	/**
	 * @param $post_id
	 *
	 * @return ProductItemIdModel|null
	 */
	public function get_by_product_id( $post_id ) {
		$data = $this->get_by( self::COLUMN_PRODUCT_ID, $post_id );
		if ( is_array( $data ) && ! empty( $data ) ) {
			return $data[0];
		}

		return null;
	}

	/**
	 * @param $item_id
	 *
	 * @return ProductItemIdModel|null
	 */
	public function get_by_item_id( $item_id ) {
		$data = $this->get_by( self::COLUMN_ITEM_ID, $item_id );
		if ( is_array( $data ) && ! empty( $data ) ) {
			return $data[0];
		}

		return null;
	}

	public function model(): string {
		return ProductItemIdModel::class;
	}
}
