<?php

namespace Heureka\Api;

use Heureka\Helpers\IpInRange;
use Heureka\Managers\ApiManager;
use HeurekaDeps\DI\DependencyException;
use HeurekaDeps\DI\NotFoundException;
use HeurekaDeps\Hcapi\Services\ProductsAvailability;
use HeurekaDeps\Hcapi\Services\ServiceException;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class ProductsApi extends WP_REST_Controller {

	private IpInRange $ip_in_range;

	public function __construct(
		IpInRange $ip_in_range
	) {
		$this->ip_in_range = $ip_in_range;
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route(
			ApiManager::PATH,
			'products/availability',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'product_availability' ),
					'permission_callback' => function () {
						return $this->ip_in_range->check();
					},
					'args'                => array(
						'products' => array(
							'required' => true,
						),
					),
				),
			)
		);
	}

	/**
	 * Get product availability
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @throws DependencyException
	 * @throws NotFoundException
	 * @throws ServiceException
	 */
	public function product_availability( WP_REST_Request $request ) {
		$service = new ProductsAvailability();
		echo $service->processData(
			array(
				heureka_container()->get( \Heureka\HeurekaApi\In\ProductsAvailability::class ),
				'handle_request',
			),
			$request->get_params()
		);
		exit();
	}
}
