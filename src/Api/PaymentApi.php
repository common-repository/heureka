<?php

namespace Heureka\Api;

use Heureka\Helpers\IpInRange;
use Heureka\Managers\ApiManager;
use HeurekaDeps\DI\DependencyException;
use HeurekaDeps\DI\NotFoundException;
use HeurekaDeps\Hcapi\Services\PaymentDelivery;
use HeurekaDeps\Hcapi\Services\PaymentStatus;
use HeurekaDeps\Hcapi\Services\ServiceException;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class PaymentApi extends WP_REST_Controller {

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
			'payment/delivery',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'payment_delivery' ),
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
		register_rest_route(
			ApiManager::PATH,
			'payment/status',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'payment_status' ),
					'permission_callback' => function () {
						return $this->ip_in_range->check();
					},
					'args'                => array(
						'order_id' => array(
							'required' => true,
						),
						'status'   => array(
							'required' => true,
						),
						'date'     => array(
							'required' => true,
						),
					),
				),
			)
		);
	}

	/**
	 * Get payment / delivery
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @throws DependencyException
	 * @throws NotFoundException
	 * @throws ServiceException
	 */
	public function payment_delivery( WP_REST_Request $request ) {
		$service = new PaymentDelivery();
		echo $service->processData(
			array(
				heureka_container()->get( \Heureka\HeurekaApi\In\PaymentDelivery::class ),
				'handle_request',
			),
			$request->get_params()
		);
		exit();
	}

	/**
	 * Get payment status
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @throws DependencyException
	 * @throws NotFoundException
	 * @throws ServiceException
	 */
	public function payment_status( WP_REST_Request $request ) {
		$service = new PaymentStatus();
		echo $service->processData(
			array(
				heureka_container()->get( \Heureka\HeurekaApi\In\PaymentStatus::class ),
				'handle_request',
			),
			$request->get_params()
		);
		exit();
	}
}
