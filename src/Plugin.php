<?php

namespace Heureka;

use Heureka\Managers\ApiManager;
use Heureka\Managers\FeedsManager;
use Heureka\Managers\HelpersManager;
use Heureka\Managers\PostTypesManager;
use Heureka\Repositories\ProductItemIdRepository;

final class Plugin {

	private Woocommerce $woocommerce;

	public function __construct(
		ApiManager $api_manager,
		PostTypesManager $post_types_manager,
		FeedsManager $feeds_manager,
		HelpersManager $helpers_manager,
		Settings $settings,
		Woocommerce $woocommerce
	) {
		$this->woocommerce = $woocommerce;

		add_filter( 'plugin_action_links_heureka/heureka.php', array( $this, 'add_action_links' ) );

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			heureka_container()->get( CLI::class );
		}
	}

	/**
	 * @param bool $network_wide
	 */
	public function activate( bool $network_wide ) {
		$repositories = array(
			heureka_container()->get( ProductItemIdRepository::class ),
		);
		foreach ( $repositories as $repository ) {
			$repository->create_table();
		}
	}

	/**
	 * @param bool $network_wide
	 */
	public function deactivate( bool $network_wide ) {
		$this->woocommerce->unschedule_regenerate_availability_feed();
	}

	/**
	 *
	 */
	public function uninstall() {
		$repositories = array(
			heureka_container()->get( ProductItemIdRepository::class ),
		);
		foreach ( $repositories as $repository ) {
			$repository->drop_table();
		}
	}

	/**
	 * @param $links
	 *
	 * @return array
	 */
	public function add_action_links( $links ): array {
		$before = array(
			'settings' => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=wc-settings&tab=heureka' ), __( 'Settings', 'heureka' ) ),
		);

		return array_merge( $before, $links );
	}
}
