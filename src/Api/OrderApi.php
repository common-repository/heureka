<?php

namespace Heureka\Api;

use Heureka\Helpers\IpInRange;
use Heureka\Managers\ApiManager;
use HeurekaDeps\DI\DependencyException;
use HeurekaDeps\DI\NotFoundException;
use HeurekaDeps\Hcapi\Services\OrderCancel;
use HeurekaDeps\Hcapi\Services\OrderSend;
use HeurekaDeps\Hcapi\Services\OrderStatus;
use HeurekaDeps\Hcapi\Services\ServiceException;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class OrderApi extends WP_REST_Controller {

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
			'order/status',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'order_status' ),
					'permission_callback' => function () {
						return $this->ip_in_range->check();
					},
					'args'                => array(
						'order_id' => array(
							'required' => true,
						),
					),
				),
			)
		);

		register_rest_route(
			ApiManager::PATH,
			'order/send',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'order_send' ),
					'permission_callback' => function () {
						return $this->ip_in_range->check();
					},
					'args'                => array(
						'products'        => array(
							'required' => true,
						),
						'customer'        => array(
							'required' => true,
						),
						'deliveryAddress' => array(
							'required' => true,
						),
					),
				),
			)
		);
		register_rest_route(
			ApiManager::PATH,
			'order/cancel',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'order_cancel' ),
					'permission_callback' => function () {
						return $this->ip_in_range->check();
					},
					'args'                => array(
						'order_id' => array(
							'required' => true,
						),
						'reason'   => array(
							'required' => true,
						),
					),
				),
			)
		);
	}

	/**
	 * get order status
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @throws DependencyException
	 * @throws NotFoundException
	 * @throws ServiceException
	 */
	public function order_status( WP_REST_Request $request ) {
		$service = new OrderStatus();
		echo $service->processData(
			array(
				heureka_container()->get( \Heureka\HeurekaApi\In\OrderStatus::class ),
				'handle_request',
			),
			$request->get_params()
		);
		exit();
	}

	/**
	 * Send Order
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @throws DependencyException
	 * @throws NotFoundException
	 * @throws ServiceException
	 */
	public function order_send( WP_REST_Request $request ) {
		$service = new OrderSend();
		echo $service->processData(
			array(
				heureka_container()->get( \Heureka\HeurekaApi\In\OrderSend::class ),
				'handle_request',
			),
			$request->get_params()
		);

		exit();
	}

	/**
	 * Cancel order
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @throws DependencyException
	 * @throws NotFoundException
	 * @throws ServiceException
	 */
	public function order_cancel( WP_REST_Request $request ) {
		$service = new OrderCancel();
		echo $service->processData(
			array(
				heureka_container()->get( \Heureka\HeurekaApi\In\OrderCancel::class ),
				'handle_request',
			),
			$request->get_params()
		);
		exit();
	}
}
