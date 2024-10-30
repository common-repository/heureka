<?php

namespace Heureka\Settings;

use Heureka\Abstracts\AbstractSettings;
use Heureka\Enums\WeekDays;
use Heureka\Feed\FeedAvailability;
use Heureka\Feed\FeedProductCz;
use Heureka\Feed\FeedProductSk;
use Heureka\Managers\FeedsManager;
use Heureka\Repositories\SettingsRepository;
use Heureka\Settings;
use HeurekaDeps\Wpify\CustomFields\CustomFields;

class FeedProductsSettings extends AbstractSettings {

	private FeedAvailability $feed_availability;
	private FeedProductCz $feed_product_cz;
	private FeedProductSk $feed_product_sk;
	private FeedsManager $feeds_manager;

	public function __construct(
		CustomFields $wcf,
		SettingsRepository $settings_repository,
		FeedAvailability $feed_availability,
		FeedProductCz $feed_product_cz,
		FeedProductSk $feed_product_sk,
		FeedsManager $feeds_manager
	) {
		$this->feed_availability = $feed_availability;
		$this->feed_product_cz   = $feed_product_cz;
		$this->feed_product_sk   = $feed_product_sk;
		$this->feeds_manager     = $feeds_manager;

		parent::__construct( $wcf, $settings_repository );
	}

	public function setup() {
		if ( ! $this->settings_repository->is_feature_enabled( GeneralSettings::FEATURE_FEEDS ) ) {
			return;
		}
		add_action( 'admin_action_heureka-generate-feed', array( $this, 'generate_feed' ) );

		$items = array(
			array(
				'id'      => 'business_days',
				'type'    => 'multi_select',
				'label'   => __( 'Business days', 'heureka' ),
				'desc'    => __( 'Select all your business days.', 'heureka' ),
				'default' => array(),
				'options' => WeekDays::get(),

			),
			array(
				'id'      => 'closing_hour',
				'type'    => 'text',
				'label'   => __( 'Closing hour', 'heureka' ),
				'desc'    => __( 'Enter business closing hour in HH:MM format.', 'heureka' ),
				'default' => '00:00',
			),
			array(
				'id'    => 'delivery',
				'type'  => 'number',
				'label' => __( 'Delivery time', 'heureka' ),
				'desc'  => __( 'Enter 0 for instock, 1-3 for 3 days, 4-7 for one week, 8-14 for two weeks, 15-30 for one month, 31 and more for month and more.', 'heureka' ),
			),
			array(
				'id'    => 'delivery_out_of_stock',
				'type'  => 'number',
				'label' => __( 'Delivery time for out of stock items', 'heureka' ),
				'desc'  => __( 'Enter 0 for instock, 1-3 for 3 days, 4-7 for one week, 8-14 for two weeks, 15-30 for one month, 31 and more for month and more.', 'heureka' ),
			),
			array(
				'id'    => 'exclude_outofstock',
				'type'  => 'switch',
				'label' => __( 'Exclude out of stock items', 'heureka' ),
				'desc'  => __( 'Check to exclude out of stock items.', 'heureka' ),
			),
			array(
				'id'    => 'exclude_without_category',
				'type'  => 'switch',
				'label' => __( 'Exclude items without category', 'heureka' ),
				'desc'  => __( 'Check to exclude items without mapped Heureka category.', 'heureka' ),
			),
			array(
				'id'    => 'item_id_custom_field',
				'type'  => 'text',
				'label' => __( 'ITEM_ID custom field', 'heureka' ),
				'desc'  => __( 'Product ID is used as default value for ITEM_ID. Enter custom field key if you want to use custom field value instead.', 'heureka' ),
			),
			array(
				'id'    => 'ean_custom_field',
				'type'  => 'text',
				'label' => __( 'EAN custom field', 'heureka' ),
				'desc'  => __( 'SKU is used as default value for EAN. Enter custom field key if you want to use custom field value instead.', 'heureka' ),
			),
			array(
				'id'    => 'brand',
				'type'  => 'text',
				'label' => __( 'Brand ', 'heureka' ),
				'desc'  => __( 'Enter text, custom field or taxonomy slug. You can also override the brand for each product.', 'heureka' ),
			),
			array(
				'id'      => 'brand_type',
				'type'    => 'select',
				'label'   => __( 'Brand type', 'heureka' ),
				'options' => array(
					array(
						'label' => 'Text',
						'value' => 'text',
					),
					array(
						'label' => 'Custom field',
						'value' => 'custom_field',
					),
					array(
						'label' => 'Taxonomy',
						'value' => 'taxonomy',
					),
				),
			),

			array(
				'id'          => 'generate_feeds_title',
				'type'        => 'title',
				'label'       => __( 'Generate feeds', 'heureka' ),
				'description' => __( 'Product feeds (CZ and SK) are generated manually by clicking a button. If you switch "Auto regenerate on product update", they will be generated automatically after product update.<br/>The availability feed is generated automatically every day. If necessary, it can be regenerated manually by clicking the button.',
					'heureka' ),
			),
			array(
				'id'          => 'products_per_run',
				'type'        => 'number',
				'title'       => __( 'Products per run', 'heureka' ),
				'description' => __( 'The number of products processed in one run. Leave empty for default (100).', 'heureka' ),
				'default'     => '100',
			),
			array(
				'id'          => 'auto_regenerate_on_product_update',
				'type'        => 'toggle',
				'title'       => __( 'Auto regenerate on product update', 'heureka' ),
				'description' => __( 'Automatically regenerates feeds after product save/update. Toggle off if you have large number of product updates (ie. when using WP All Import for importing large number of products).', 'heureka' ),
				'default'     => false,
			),
			array(
				'id'          => 'generate_feed_cz',
				'type'        => 'button',
				'label'       => sprintf( __( 'Generate feed CZ', 'heureka' ), 'heureka' ),
				'description' => sprintf( __( 'Once generated, the feed will be available at: <a href="%1$s" target="blank">%1$s</a>', 'heureka' ), $this->feed_product_cz->get_xml_url() ),
				'url'         => add_query_arg(
					array(
						'action'        => 'heureka-generate-feed',
						'feed'          => 'products-cz',
						'heureka_nonce' => wp_create_nonce( 'heureka-generate-feed' ),
					),
					admin_url()
				),
			),
			array(
				'id'          => 'generate_feed_sk',
				'type'        => 'button',
				'label'       => sprintf( __( 'Generate feed SK', 'heureka' ), 'heureka' ),
				'description' => sprintf( __( 'Once generated, the feed will be available at: <a href="%1$s" target="blank">%1$s</a>', 'heureka' ), $this->feed_product_sk->get_xml_url() ),
				'url'         => add_query_arg(
					array(
						'action'        => 'heureka-generate-feed',
						'feed'          => 'products-sk',
						'heureka_nonce' => wp_create_nonce( 'heureka-generate-feed' ),
					),
					admin_url()
				),
			),
			array(
				'id'          => 'generate_feed_availability',
				'type'        => 'button',
				'label'       => sprintf( __( 'Generate availability feed', 'heureka' ), 'heureka' ),
				'description' => sprintf( __( 'Once generated, the feed will be available at: <a href="%1$s" target="blank">%1$s</a>', 'heureka' ), $this->feed_availability->get_xml_url() ),
				'url'         => add_query_arg(
					array(
						'action'        => 'heureka-generate-feed',
						'feed'          => 'availability',
						'heureka_nonce' => wp_create_nonce( 'heureka-generate-feed' ),
					),
					admin_url()
				),
			),
			array(
				'label'   => __( 'Feed URL', 'wpify-woo-feeds' ),
				'id'      => 'feed_url',
				'type'    => 'html',
				'content' => $this->get_feeds_url_content(),
			),
		);

		$this->wcf->create_woocommerce_settings(
			array(
				'tab'        => array(
					'id'    => 'heureka',
					'label' => __( 'Heureka', 'heureka' ),
				),
				'section'    => array(
					'id'    => Settings::SECTION_FEED_PRODUCTS,
					'label' => __( 'Product feed settings', 'heureka' ),
				),
				'page_title' => __( 'Product feed settings', 'heureka' ),
				'items'      => array(
					array(
						'type'  => 'group',
						'id'    => Settings::SECTION_FEED_PRODUCTS,
						'items' => $items,
					),
				),
			)
		);
	}

	private function get_feeds_url_content() {
		ob_start(); ?>
		<p><?php _e( 'To regenerate the feeds automatically, you can setup the cron jobs from the table bellow. The first cron is used to schedule the regeneration, so you can set it up ie. every one hour. The second cron is used to trigger the regeneration of the next batch - depending on the number of products in one batch set it up to ie. every minute.', 'heureka' ); ?></p>
		<p><?php _e( 'You can also use the WP-CLI command to regenerate the feeds. The command regenerates the feed at once, so you can schedule it ie. every one hour.', 'heureka' ); ?></p>
		<br>
		<table style="width: 100%;" class="wp-list-table widefat striped table-view-list">
			<thead>
			<tr>
				<th><?php _e( 'Name', 'wpify-woo-feeds' ) ?></th>
				<th><?php _e( 'Feed URL', 'wpify-woo-feeds' ) ?></th>
				<th><?php _e( 'Cron URLs', 'wpify-woo-feeds' ) ?></th>
				<th><?php _e( 'WP CLI', 'wpify-woo-feeds' ) ?></th>
			</tr>
			</thead>
			<?php

			foreach ( $this->feeds_manager->get_feeds() as $feed_module ) :
				$feed_url = $feed_module->get_xml_url();
				?>
				<tr>
					<td><?php echo $feed_module::FEED_NAME; ?></td>
					<td><a href="<?php echo $feed_url; ?>" target="_blank"><?php echo $feed_url; ?></a></td>
					<td>
						<code><?php echo add_query_arg( [
								'heureka-action' => 'schedule',
								'heureka-feed'   => $feed_module->feed_name(),
							], site_url() ); ?></code><br/><br/>
						<code><?php echo add_query_arg( [
								'heureka-action' => 'generate',
								'heureka-feed'   => $feed_module->feed_name(),
							], site_url() ); ?></code>
					</td>
					<td>
						<code><?php printf( 'wp heureka generate %s', $feed_module->feed_name() ) ?></code>
					</td>
				</tr>
			<?php
			endforeach;
			?>
		</table>

		<?php
		return ob_get_clean();
	}

	public function generate_feed() {
		if (!isset($_REQUEST['heureka_nonce']) || ! wp_verify_nonce( $_GET[ 'heureka_nonce' ], 'heureka-generate-feed' ) ) {
			wp_die( __( 'Request verification failed', 'heureka' ) );
		}

		$feed = $_REQUEST['feed'] ?? '';
		if ( ! $feed ) {
			wp_die( __( 'Missing feed param', 'heureka' ) );
		}
		$object = null;

		if ( $feed === 'products-cz' ) {
			$object = heureka_container()->get( FeedProductCz::class );
		} elseif ( $feed === 'products-sk' ) {
			$object = heureka_container()->get( FeedProductSk::class );
		} elseif ( $feed === 'availability' ) {
			$object = heureka_container()->get( FeedAvailability::class );
		}

		if ( ! $object ) {
			wp_die( __( 'Wrong feed', 'heureka' ) );
		}

		$object->start_feed_generation();
		wp_redirect(
			add_query_arg(
				array(
					'page'    => 'wc-settings',
					'tab'     => 'heureka',
					'section' => 'heureka_feed_products',
				),
				admin_url( 'admin.php' )
			)
		);
		exit();
	}
}
