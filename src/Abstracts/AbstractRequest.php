<?php

namespace Heureka\Abstracts;

use Heureka\Repositories\SettingsRepository;

abstract class AbstractRequest {

	const METHOD_POST = 'POST';
	const METHOD_POST_MULTIPART = 'POST_MULTIPART';
	const METHOD_GET = 'GET';
	const METHOD_PUT = 'PUT';

	public $settings_repository;

	public function __construct( SettingsRepository $settings_repository ) {
		$this->settings_repository = $settings_repository;
	}

	public function call( string $endpoint, string $method, array $data = array() ) {
		$api_url = $this->settings_repository->get_marketplace_setting( 'api_url' );
		if ( substr( $api_url, - 1 ) !== '/' ) {
			$api_url .= '/';
		}
		$url = $api_url . $endpoint;
		$response = null;
		if ( $method === self::METHOD_GET ) {
			$response = wp_remote_retrieve_body( wp_remote_get( add_query_arg( $data, $url ) ) );
		} elseif ( $method === self::METHOD_POST ) {
			$response = wp_remote_retrieve_body(
				wp_remote_post(
					$url,
					array(
						'body' => $data,
					)
				)
			);
		} elseif ( $method === self::METHOD_PUT ) {
			$response = wp_remote_retrieve_body(
				wp_remote_request(
					$url,
					array(
						'body'   => $data,
						'method' => self::METHOD_POST,
					)
				)
			);
		} elseif ( $method === self::METHOD_POST_MULTIPART ) {
			$curl = curl_init();
			curl_setopt_array( $curl, array(
				CURLOPT_URL            => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING       => '',
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_TIMEOUT        => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST  => 'POST',
				CURLOPT_POSTFIELDS     => array(
					'order_id' => $data['order_id'],
					'invoice'  => new \CURLFILE( $data['invoice'] )
				),
			) );
			$response = curl_exec( $curl );
			curl_close( $curl );
		}

		return json_decode( $response, ARRAY_A );
	}
}
