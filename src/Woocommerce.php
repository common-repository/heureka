<?php

namespace Heureka;

use Exception;
use Heureka\Enums\PaymentStatuses;
use Heureka\HeurekaApi\Out\PostOrderInvoice;
use Heureka\HeurekaApi\Out\PostOrderNote;
use Heureka\HeurekaApi\Out\PutOrderStatus;
use Heureka\HeurekaApi\Out\PutPaymentStatus;
use Heureka\Repositories\OrderRepository;
use Heureka\Repositories\SettingsRepository;
use Heureka\Settings\CustomerVerifiedSettings;
use Heureka\Settings\GeneralSettings;
use HeurekaDeps\Heureka\ShopCertification;
use HeurekaDeps\Wpify\Model\OrderItemLine;

class Woocommerce {

	private PostOrderInvoice $post_order_invoice;
	private PostOrderNote $post_order_note;
	private PutOrderStatus $put_order_status;
	private PutPaymentStatus $put_payment_status;
	private OrderRepository $order_repository;
	private SettingsRepository $settings_repository;

	public function __construct(
			PostOrderInvoice $post_order_invoice,
			PostOrderNote $post_order_note,
			PutOrderStatus $put_order_status,
			PutPaymentStatus $put_payment_status,
			OrderRepository $order_repository,
			SettingsRepository $settings_repository
	) {
		$this->post_order_invoice  = $post_order_invoice;
		$this->post_order_note     = $post_order_note;
		$this->put_order_status    = $put_order_status;
		$this->put_payment_status  = $put_payment_status;
		$this->order_repository    = $order_repository;
		$this->settings_repository = $settings_repository;

		if ( $this->settings_repository->is_feature_enabled( GeneralSettings::FEATURE_MARKETPLACE ) && filter_var( $this->settings_repository->get_marketplace_setting( 'api_url' ), FILTER_VALIDATE_URL ) ) {
			add_action( 'woocommerce_new_customer_note', array( $this, 'post_order_note' ), 10, 1 );
			add_action( 'woocommerce_order_status_changed', array( $this, 'put_order_status' ), 10, 3 );
			add_action( 'woocommerce_order_status_changed', array( $this, 'put_payment_status' ), 10, 3 );
		}

		if ( $this->settings_repository->is_feature_enabled( GeneralSettings::FEATURE_FEEDS ) ) {
			add_action( 'init', array( $this, 'schedule_regenerate_availability_feed' ) );
		} else {
			add_action( 'init', array( $this, 'unschedule_regenerate_availability_feed' ) );
		}


		if ( $this->settings_repository->is_feature_enabled( GeneralSettings::FEATURE_CUSTOMERS_VERIFIED ) ) {
			// Customer verified hooks.
			add_action( 'woocommerce_checkout_order_created', array( $this, 'send_order_to_heureka_now' ) );
			add_action( 'heureka_customer_verified', array( $this, 'send_order_to_heureka' ) );
			add_action( 'woocommerce_checkout_after_terms_and_conditions', array( $this, 'add_optout' ) );
			add_action( 'wp_head', array( $this, 'render_widget' ) );
		}

		if ( $this->settings_repository->is_feature_enabled( GeneralSettings::FEATURE_CONVERSION_TRACKING ) ) {
			add_action( 'woocommerce_thankyou', array( $this, 'render_tracking_code' ) );
		}
	}

	/**
	 * Schedule regenerate Heureka availability feed every day.
	 *
	 * @return void
	 */
	public function schedule_regenerate_availability_feed() {
		if ( false === as_has_scheduled_action( 'heureka_availability_generate_feed' ) ) {
			as_schedule_recurring_action( strtotime( 'tomorrow' ), DAY_IN_SECONDS, 'heureka_availability_generate_feed', array(), 'heureka' );
		}
	}

	/**
	 * Unschedule regenerate Heureka availability feed.
	 *
	 * @return void
	 */
	public function unschedule_regenerate_availability_feed() {
		if ( false !== as_has_scheduled_action( 'heureka_availability_generate_feed' ) ) {
			as_unschedule_all_actions( 'heureka_availability_generate_feed', array(), 'heureka' );
		}
	}

	/**
	 * Post order note
	 *
	 * @param array $data Items - 'order_id', 'customer_note'.
	 *
	 * @return void
	 */
	public function post_order_note( array $data ) {
		$this->post_order_note->do_request( $data['order_id'], $data['customer_note'] );
	}

	/**
	 * Put order status
	 *
	 * @param int    $order_id   Order id.
	 * @param string $status_old Status old.
	 * @param string $status_new Status new.
	 *
	 * @return void
	 */
	public function put_order_status( $order_id, $status_old, $status_new ) {
		$statuses = $this->settings_repository->get_option( Settings::SECTION_MARKETPLACE, 'statuses', array() );
		foreach ( $statuses as $status ) {
			if ( sprintf( 'wc-%s', $status_new ) === $status['status'] ) {
				$this->put_order_status->do_request( $order_id, $status['heureka_status'] );
				break;
			}
		}
	}

	/**
	 * Put payment status
	 *
	 * @param int    $order_id   Order id.
	 * @param string $status_old Status old.
	 * @param string $status_new Status new.
	 *
	 * @return void
	 */
	public function put_payment_status( $order_id, $status_old, $status_new ) {
		$paid_order_statuses = $this->settings_repository->get_marketplace_setting( 'paid_order_statuses' );
		if ( in_array( sprintf( 'wc-%s', $status_new ), $paid_order_statuses ) ) {
			$this->put_payment_status->do_request( $order_id, PaymentStatuses::PAID );

			$this->post_order_invoice( $order_id );
		} else {
			$this->put_payment_status->do_request( $order_id, PaymentStatuses::UNPAID );
		}
	}

	/**
	 * Put order invoice
	 *
	 * @param int $order_id Order id.
	 *
	 * @return void
	 */
	public function post_order_invoice( int $order_id ) {
		$invoice_path_custom_field = $this->settings_repository->get_marketplace_setting( 'invoice_path_custom_field' );
		if ( $invoice_path_custom_field ) {
			$invoice_path = get_post_meta( $order_id, $invoice_path_custom_field, true );
			if ( $invoice_path ) {
				$this->post_order_invoice->do_request( $order_id, $invoice_path );
			}
		}
	}

	/**
	 * Schedule the event
	 *
	 * @param int|string $order_id Order ID.
	 *
	 * @return false|int
	 */
	public function schedule_event( $order_id ) {
		return as_schedule_single_action( time(), 'heureka_customer_verified', array( 'order_id' => $order_id ) );
	}

	/**
	 * Send the order to Heureka on order processed hook
	 *
	 * @param int|string $order_id Order ID.
	 *
	 * @return false
	 * @throws Exception Exception.
	 */
	public function send_order_to_heureka_now( $order_id ) {
		if ( ! $this->settings_repository->get_customer_verified_setting( 'api_key' ) ) {
			return false;
		}

		if ( isset( $_POST[ CustomerVerifiedSettings::INPUT_OPTOUT_NAME ] ) && ! empty( $_POST[ CustomerVerifiedSettings::INPUT_OPTOUT_NAME ] ) ) {
			return false;
		}

		if ( $this->settings_repository->get_customer_verified_setting( 'send_async' ) ) {
			if ( is_a( $order_id, '\WC_Order' ) ) {
				$this->schedule_event( $order_id->get_id() );
			} else {
				$this->schedule_event( $order_id );
			}
		} else {
			$this->send_order_to_heureka( $order_id );
		}
	}

	/**
	 * Send order to Heureka
	 *
	 * @param int|string $order_id Order ID.
	 *
	 * @throws Exception Exception.
	 */
	public function send_order_to_heureka( $order_id ) {
		$order = $this->order_repository->get( $order_id );

		try {
			$options = array();
			if ( 'CZ' === $this->settings_repository->get_customer_verified_setting( 'country' ) ) {
				$options['service'] = ShopCertification::HEUREKA_CZ;
			} elseif ( 'SK' === $this->settings_repository->get_customer_verified_setting( 'country' ) ) {
				$options['service'] = ShopCertification::HEUREKA_SK;
			}

			$shop_certification = new ShopCertification( $this->settings_repository->get_customer_verified_setting( 'api_key' ), $options, ( new WpRequester() ) );
			$shop_certification->setEmail( $order->get_wc_order()->get_billing_email() );
			$shop_certification->setOrderId( $order->id );

			foreach ( $order->get_items() as $item ) {
				if ( $item instanceof OrderItemLine ) {
					$shop_certification->addProductItemId( $item->product_id );
				}
			}

			$shop_certification->logOrder();
		} catch ( Exception $e ) {
			return $e;
		}
	}

	/**
	 * Add optout to checkout
	 */
	public function add_optout() {
		if ( ! $this->settings_repository->get_customer_verified_setting( 'enable_optout' ) ) {
			return;
		}
		?>
		<p class="form-row wpify-woo-heureka-optout">
			<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
				<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"
					   name="<?php echo CustomerVerifiedSettings::INPUT_OPTOUT_NAME; ?>" style="width: auto;"
						<?php
						checked( isset( $_POST[ CustomerVerifiedSettings::INPUT_OPTOUT_NAME ] ), true ); // WPCS: input var ok, csrf ok.
						?>
				/>
				<span class="wpify-woo-heureka-optout-checkbox-text"><?php echo sanitize_text_field( $this->settings_repository->get_customer_verified_setting( 'enable_optout_text' ) ); ?></span>&nbsp;
			</label>
		</p>
		<?php
	}

	/**
	 * Render certification widget
	 */
	public function render_widget() {
		if ( empty( $this->settings_repository->get_customer_verified_setting( 'widget_enabled' ) ) || empty( $this->settings_repository->get_customer_verified_setting( 'widget_code' ) ) || apply_filters( 'heureka_render_widget', true ) === false ) {
			return;
		}

		echo $this->settings_repository->get_customer_verified_setting( 'widget_code' );
	}

	/**
	 * Conversion tracking on thankyou page
	 */
	public function render_tracking_code( $order_id ) {
		$api_key = $this->settings_repository->get_conversion_tracking_setting( 'api_key' );
		if ( ! $api_key ) {
			return;
		}

		$order    = $this->order_repository->get( $order_id );
		$products = [];
		foreach ( $order->line_items as $item ) {
			$products[] = [
					'addProduct',
					$item->name,
					(string) $item->unit_price_tax_included,
					(string) $item->quantity,
					(string) $item->id,
			];
		}
		$url = 'https://im9.cz/js/ext/1-roi-async.js';
		if ( 'sk' === $this->settings_repository->get_conversion_tracking_setting( 'country' ) ) {
			$url = 'https://im9.cz/sk/js/ext/2-roi-async.js';
		}
		$url = apply_filters( 'heureka_mereni_konverzi_url', $url );
		?>
		<script type="text/javascript">
			var _hrq = _hrq || [];
			_hrq.push(['setKey', '<?php echo esc_attr( $api_key ); ?>']);
			_hrq.push(['setOrderId', '<?php echo esc_attr( $order->id ); ?>']);

			<?php foreach ( $products as $item ) { ?>
			_hrq.push(<?php echo json_encode( $item );?>);
			<?php }?>
			_hrq.push(['trackOrder']);

			(function () {
				var ho = document.createElement('script');
				ho.type = 'text/javascript';
				ho.async = true;
				ho.src = '<?php echo $url;?>';
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(ho, s);
			})();
		</script>

		<?php
	}
}
