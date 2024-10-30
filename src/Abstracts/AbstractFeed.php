<?php

namespace Heureka\Abstracts;

use DOMException;
use Heureka\Repositories\SettingsRepository;
use HeurekaDeps\Spatie\ArrayToXml\ArrayToXml;

/**
 * Class AbstractFeed
 */
abstract class AbstractFeed {

	public function __construct() {
		add_action( "{$this->feed_name()}_generate_feed", array( $this, 'generate_feed' ) );
		add_action( "template_redirect", array( $this, 'trigger_generate_feed' ) );
		add_action( "template_redirect", array( $this, 'schedule_generate_feed' ) );

		if ( (bool) heureka_container()->get( SettingsRepository::class )->get_feed_setting( 'auto_regenerate_on_product_update' ) === true ) {
			add_action( 'woocommerce_update_product', array( $this, 'schedule_feed_update' ) );
		}
		add_action( "{$this->feed_name()}_update_product", array( $this, 'update_product' ) );

		if ( 'heureka_availability' !== $this->feed_name() ) {
			add_action(
				'admin_init',
				function () {
					if ( as_has_scheduled_action( sprintf( '%s_generate_feed', $this->feed_name() ) ) ) {
						add_action( 'admin_notices', [ $this, 'render_feed_generating_notice' ] );
					}
				}
			);
		}
	}

	/**
	 * Schedule update of product in feed when product is updated.
	 *
	 * @param $product_id
	 *
	 * @return void
	 */
	public function schedule_feed_update( $product_id ) {
		// Save to transient, as the woocommerce_update_product is triggered multiple times during single request.
		$transient     = sprintf( '%s_update_product_%s', $this->feed_name(), $product_id );
		$update_action = sprintf( '%s_update_product', $this->feed_name() );
		if ( false === ( get_transient( $transient ) ) && false === as_next_scheduled_action( $update_action, array( 'product_id' => $product_id ) ) ) {
			// 5 sec should be enough
			as_schedule_single_action( strtotime( 'now + 5 seconds' ), $update_action, array( 'product_id' => $product_id ), 'heureka' );
			set_transient( $transient, $product_id, 2 ); // change 2 seconds if not enough
		}
	}

	/**
	 * Update single product in feed
	 *
	 * @param $product_id
	 *
	 * @return void
	 * @throws DOMException
	 */
	public function update_product( $product_id ) {
		$product           = wc_get_product( $product_id );
		$product_feed_data = $product->get_meta( '_heureka_feed' );

		if ( is_array( $product_feed_data ) && isset( $product_feed_data['exclude'] ) && (bool) $product_feed_data['exclude'] === true ) {
			return;
		}

		$data      = $this->data( array( $product ) );
		$tmp_data  = $this->get_tmp_data();
		$new_items = array();
		foreach ( $data as $data_key => $item ) {
			// Scrub through data and check, if we find the product. If so, update it's data.
			$found = false;
			foreach ( $tmp_data as $tmp_data_key => $tmp_item ) {
				if ( $tmp_item['feed_product_id'] === $item['feed_product_id'] ) {
					$tmp_data[ $tmp_data_key ] = $item;
					$found                     = true;
					$this->save_tmp_data( $tmp_data );
					break;
				}
			}

			// If the product is not in feed yet, add it later.
			if ( ! $found ) {
				$new_items[ $data_key ] = $item;
			}
		}
		// Add the new items to feed.
		if ( ! empty( $new_items ) ) {
			$tmp_data = $this->add_tmp_data( $new_items );
		}

		$this->save_feed( $this->get_xml_from_array( $tmp_data, $this->get_root_name() ) );
	}

	/**
	 * Save feed to specified folder
	 *
	 * @param $data
	 *
	 * @return false|int
	 */
	public function save_feed( $data ) {
		if ( wp_mkdir_p( $this->get_tmp_dir_path() ) ) {
			return file_put_contents( $this->get_xml_path(), $data );
		}

		return false;
	}

	/**
	 * Generate the feed.
	 *
	 * @return void
	 * @throws DOMException
	 */
	public function generate_feed() {
		$product_ids = $this->get_product_ids_to_generate();
		if ( empty( $product_ids ) ) {
			return;
		}

		// Get the chunk of products to generate
		$generate = array_splice( $product_ids, 0, $this->products_per_run() );

		// Get products data.
		$data = $this->get_data_for_products( $generate );

		// Add it to the tmp data
		$this->add_tmp_data( $data['data'] );

		// Update the chunk of products to generate
		if ( ! empty( $product_ids ) ) {
			$this->save_product_ids_to_generate( $product_ids );
			$this->schedule_feed_generation();
		} else {
			// We are done, save the feed
			$this->save_feed( $this->get_xml_from_array( $this->get_tmp_data(), $this->get_root_name() ) );
			$this->delete_product_ids_to_generate();
		}
	}

	/**
	 * Get the ids of products to generate.
	 *
	 * @return array
	 */
	public function get_product_ids_to_generate(): array {
		return get_option( sprintf( '%s_product_ids_to_generate', $this->feed_name() ), array() );
	}

	/**
	 * Save IDs odf product to generate
	 *
	 * @param array $ids
	 *
	 * @return bool
	 */
	public function save_product_ids_to_generate( array $ids ): bool {
		return update_option( sprintf( '%s_product_ids_to_generate', $this->feed_name() ), $ids, 'no' );
	}

	/**
	 * Save IDs odf product to generate
	 *
	 * @return bool
	 */
	public function delete_product_ids_to_generate(): bool {
		return delete_option( sprintf( '%s_product_ids_to_generate', $this->feed_name() ) );
	}

	/**
	 * Init the feed generation.
	 *
	 * @return void
	 */
	public function start_feed_generation() {
		// Delete old tmp file.
		$this->delete_tmp_file();
		// Get product IDs to generate
		$args = array(
			'limit'      => - 1,
			'status'     => 'publish',
			'visibility' => 'visible',
			'return'     => 'ids',
		);

		$args = apply_filters( $this->feed_name() . '_get_product_ids_args', $args );

		$product_ids = apply_filters( $this->feed_name() . '_feed_product_ids', wc_get_products( $args ) );

		// Save the IDs.
		$this->save_product_ids_to_generate( $product_ids );

		// Schedule Action Scheduler queue.
		$this->schedule_feed_generation();
	}

	/**
	 * Schedule the AS feed generation queue
	 *
	 * @return void
	 */
	private function schedule_feed_generation() {
		as_schedule_single_action(
			time(),
			sprintf( '%s_generate_feed', $this->feed_name() ),
			array(),
			'heureka'
		);
	}

	/**
	 * Get feed name
	 *
	 * @return mixed
	 */
	abstract public function feed_name();

	/**
	 * Get feed title
	 *
	 * @return mixed
	 */
	abstract public function feed_title();

	public function get_data_for_products( array $product_ids ): ?array {
		$args = array(
			'limit'      => - 1,
			'include'    => $product_ids,
			'status'     => 'publish',
			'visibility' => 'visible',
		);

		$products = wc_get_products( $args );
		if ( empty( $products ) ) {
			return null;
		}

		return array(
			'data'  => $this->data( $products ),
			'count' => count( $products ),
		);
	}

	/**
	 * Get temp data
	 *
	 * @return array
	 */
	public function get_tmp_data(): array {
		if ( ! file_exists( $this->get_tmp_file_path() ) ) {
			return array();
		}

		$data = json_decode( file_get_contents( $this->get_tmp_file_path() ) ) ?: array();

		return $data ? json_decode( json_encode( $data ), true ) : array();
	}

	/**
	 * Get the path for saving the data
	 *
	 * @return string
	 */
	public function get_dir_path(): string {
		return trailingslashit( wp_upload_dir()['basedir'] ) . trailingslashit( $this->get_directory_name() );
	}

	/**
	 * Get feeds directory.
	 *
	 * @return mixed|void
	 */
	public function get_directory_name() {
		return apply_filters( $this->feed_name() . '_feed_directory', 'xml' );
	}

	/**
	 * Get XML path
	 *
	 * @return string
	 */
	public function get_xml_path(): string {
		return $this->get_dir_path() . $this->get_file_name();
	}

	/**
	 * Get feed filename
	 *
	 * @return string
	 */
	public function get_file_name(): string {
		return apply_filters(
			$this->feed_name() . '_feed_filename_' . $this->feed_name(),
			sprintf( '%s_%s.xml', $this->feed_name(), get_current_blog_id() ),
			$this->feed_name(),
			$this
		);
	}

	/**
	 * Get path for tmp file
	 *
	 * @return string
	 */
	public function get_tmp_file_path(): string {
		return $this->get_tmp_dir_path() . $this->get_tmp_file_name();
	}

	/**
	 * Get path for tmp dir
	 *
	 * @return string
	 */
	public function get_tmp_dir_path(): string {
		return trailingslashit( wp_upload_dir()['basedir'] ) . trailingslashit( $this->get_directory_name() ) . 'tmp/';
	}

	/**
	 * Get tmp filename.
	 *
	 * @return string
	 */
	public function get_tmp_file_name(): string {
		return sprintf( '%s_%s_tmp.json', $this->feed_name(), get_current_blog_id() );
	}

	/**
	 * @param array $product
	 *
	 * @return array
	 */
	abstract public function data( array $product ): array;

	/**
	 * Add data to tmp data
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function add_tmp_data( array $data ): array {
		$data = array_merge( $this->get_tmp_data(), $data );
		$this->save_tmp_data( $data );

		return $data;
	}

	/**
	 * Save the tmp data
	 *
	 * @param array $data
	 *
	 * @return false|int
	 */
	public function save_tmp_data( array $data ) {
		if ( wp_mkdir_p( $this->get_tmp_dir_path() ) ) {
			return file_put_contents( $this->get_tmp_file_path(), json_encode( $data ) );
		}

		return false;
	}

	/**
	 * @param array $data
	 * @param string $root_name
	 * @param string $encoding
	 *
	 * @return string
	 * @throws DOMException
	 */
	public function get_xml_from_array( array $data, string $root_name = 'root', string $encoding = 'UTF-8' ): string {
		$data = array_map(
			function ( $item ) {
				if ( isset( $item['feed_product_id'] ) ) {
					unset( $item['feed_product_id'] );
				}

				return $item;
			},
			$data
		);
		$data = apply_filters( $this->feed_name() . '_feed_data', $data, $this->feed_name() );
		$xml  = new ArrayToXml( $data, $root_name, true, $encoding );

		return $xml->prettify()->toXml();
	}

	/**
	 * Get root name of XML
	 *
	 * @return string
	 */
	public function get_root_name(): string {
		return 'root';
	}

	/**
	 * Delete the temp file
	 *
	 * @return void
	 */
	public function delete_tmp_file() {
		if ( file_exists( $this->get_tmp_file_path() ) ) {
			unlink( $this->get_tmp_file_path() );
		}
	}

	/**
	 * Get XML Uri
	 *
	 * @return string
	 */
	public function get_xml_url(): string {
		return $this->get_dir_url() . $this->get_file_name();
	}

	/**
	 * Get directory Uri
	 *
	 * @return string
	 */
	public function get_dir_url(): string {
		return trailingslashit( wp_upload_dir()['baseurl'] ) . trailingslashit( $this->get_directory_name() );
	}

	/**
	 * Get number of product per run
	 *
	 * @return int
	 */
	public function products_per_run(): int {
		return apply_filters( $this->feed_name() . '_products_per_run', 100 );
	}

	/**
	 * Show admin notice.
	 *
	 * @return void
	 */
	public function render_feed_generating_notice() {
		$actions = as_get_scheduled_actions(
			[
				'hook'   => sprintf( '%s_generate_feed', $this->feed_name() ),
				'status' => \ActionScheduler_Store::STATUS_PENDING,
			]
		);
		if ( empty( $actions ) ) {
			return;
		}

		$action_id   = array_key_first( $actions );
		$action_link = add_query_arg(
			array(
				'row_action' => 'run',
				'row_id'     => $action_id,
				'nonce'      => wp_create_nonce( 'run::' . $action_id ),
			),
			admin_url( 'admin.php?page=wc-status&tab=action-scheduler&status=pending' )
		);
		echo sprintf(
			__( '<div class="updated notice"><p>Feed %s is being generated on background. Number of remaining items: %s</p><p>To process next batch <a href="%s" target="_blank">click here</a>, or just wait a few moments.</p></div>', 'heureka' ),
			$this->feed_title(),
			count( $this->get_product_ids_to_generate() ),
			$action_link

		);
	}

	public function trigger_generate_feed() {
		if ( ! isset( $_GET['heureka-action'] ) || $_GET['heureka-action'] !== 'generate'
		     || ! isset( $_GET['heureka-feed'] )
		     || $_GET['heureka-feed'] !== $this->feed_name()
		) {
			return;
		}

		$actions = as_get_scheduled_actions( [ 'group' => 'heureka', 'status' => \ActionScheduler_Store::STATUS_PENDING ] );

		foreach ( $actions as $id => $action ) {
			\ActionScheduler_QueueRunner::instance()->process_action( $id, 'Admin List Table' );
		}

		die( sprintf( 'Feed %s generation triggered', $this->feed_name() ) );
	}

	public function schedule_generate_feed() {
		if ( ! isset( $_GET['heureka-action'] ) || $_GET['heureka-action'] !== 'schedule'
		     || ! isset( $_GET['heureka-feed'] )
		     || $_GET['heureka-feed'] !== $this->feed_name()
		) {
			return;
		}

		$this->start_feed_generation();
		die( sprintf( 'Feed %s scheduled for generation', $this->feed_name() ) );
	}
}
