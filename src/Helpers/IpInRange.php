<?php
/*
 * ip_in_range.php - Function to determine if an IP is located in a
 *                   specific range as specified via several alternative
 *                   formats.
 *
 * Network ranges can be specified as:
 * 1. Wildcard format:     1.2.3.*
 * 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
 * 3. Start-End IP format: 1.2.3.0-1.2.3.255
 *
 * Return value BOOLEAN : ip_in_range($ip, $range);
 *
 * Copyright 2008: Paul Gregg <pgregg@pgregg.com>
 * 10 January 2008
 * Version: 1.2
 *
 * Source website: http://www.pgregg.com/projects/php/ip_in_range/
 * Version 1.2
 *
 * This software is Donationware - if you feel you have benefited from
 * the use of this tool then please consider a donation. The value of
 * which is entirely left up to your discretion.
 * http://www.pgregg.com/donate/
 *
 * Please do not remove this header, or source attibution from this file.
 */

namespace Heureka\Helpers;

class IpInRange {
	/**
	 * Compare IP address with whitelist.
	 *
	 * @param string $ip IP address.
	 *
	 * @return bool
	 */
	public function check(): bool {
		foreach ( $this->get_heureka_whitelist() as $range ) {
			$request_ip = $this->get_request_ip();
			if ( $request_ip == $range || $this->is_in_range( $request_ip, $range ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * This function takes 2 arguments, an IP address and a "range" in several different formats.
	 * Network ranges can be specified as:
	 * 1. Wildcard format:     1.2.3.*
	 * 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
	 * 3. Start-End IP format: 1.2.3.0-1.2.3.255
	 * The function will return true if the supplied IP is within the range. Note little validation is done on the range inputs - it expects you to use one of the above 3 formats.
	 *
	 * @param string $ip    IP address.
	 * @param string $range IP range.
	 *
	 * @return bool
	 */
	public function is_in_range( string $ip, string $range ): bool {
		if ( strpos( $range, '/' ) !== false ) {
			// $range is in IP/NETMASK format
			list( $range, $netmask ) = explode( '/', $range, 2 );
			if ( strpos( $netmask, '.' ) !== false ) {
				// $netmask is a 255.255.0.0 format
				$netmask     = str_replace( '*', '0', $netmask );
				$netmask_dec = ip2long( $netmask );

				return ( ( ip2long( $ip ) & $netmask_dec ) == ( ip2long( $range ) & $netmask_dec ) );
			} else {
				// $netmask is a CIDR size block
				// fix the range argument
				$x = explode( '.', $range );
				while ( count( $x ) < 4 ) {
					$x[] = '0';
				}
				list( $a, $b, $c, $d ) = $x;
				$range     = sprintf( "%u.%u.%u.%u", empty( $a ) ? '0' : $a, empty( $b ) ? '0' : $b, empty( $c ) ? '0' : $c, empty( $d ) ? '0' : $d );
				$range_dec = ip2long( $range );
				$ip_dec    = ip2long( $ip );

				# Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
				#$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

				# Strategy 2 - Use math to create it
				$wildcard_dec = pow( 2, ( 32 - $netmask ) ) - 1;
				$netmask_dec  = ~$wildcard_dec;

				return ( ( $ip_dec & $netmask_dec ) == ( $range_dec & $netmask_dec ) );
			}
		} else {
			// range might be 255.255.*.* or 1.2.3.0-1.2.3.255
			if ( strpos( $range, '*' ) !== false ) { // a.b.*.* format
				// Just convert to A-B format by setting * to 0 for A and 255 for B
				$lower = str_replace( '*', '0', $range );
				$upper = str_replace( '*', '255', $range );
				$range = "$lower-$upper";
			}

			if ( strpos( $range, '-' ) !== false ) { // A-B format
				list( $lower, $upper ) = explode( '-', $range, 2 );
				$lower_dec = (float) sprintf( "%u", ip2long( $lower ) );
				$upper_dec = (float) sprintf( "%u", ip2long( $upper ) );
				$ip_dec    = (float) sprintf( "%u", ip2long( $ip ) );

				return ( ( $ip_dec >= $lower_dec ) && ( $ip_dec <= $upper_dec ) );
			}

			return false;
		}

	}

	/**
	 * Returns IP address
	 *
	 * @return string
	 */
	public function get_request_ip(): string {
		// Get real IP behind cloud network
		if ( isset( $_SERVER["HTTP_CF_CONNECTING_IP"] ) ) {
			$_SERVER['REMOTE_ADDR']    = $_SERVER["HTTP_CF_CONNECTING_IP"];
			$_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		}

		$client  = $_SERVER['HTTP_CLIENT_IP'] ?? null;
		$forward = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;
		$remote  = $_SERVER['REMOTE_ADDR'];

		if ( filter_var( $client, FILTER_VALIDATE_IP ) ) {
			$ip = $client;
		} elseif ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
			$ip = $forward;
		} else {
			$ip = $remote;
		}

		return strval( $ip );
	}

	/**
	 * Returns Heureka IP whitelist.
	 *
	 * @return string[]
	 */
	public function get_heureka_whitelist(): array {
		return array(
			'95.173.213.160/27',
			'95.168.214.64/27',
			'193.85.239.160/28',
			'34.159.216.2',
			'35.198.149.227',
			'34.141.54.245',
			'34.141.29.13',
			'34.141.90.155',
			'34.107.29.43',
			'35.198.190.79',
			'35.234.95.26',
			'34.159.144.22',
			'35.246.150.212',
			'35.246.225.6',
			'35.198.133.17',
			'34.159.98.24',
			'34.107.108.31',
			'34.159.145.91',
			'34.159.117.56',
			'34.159.206.71',
			'35.198.117.118',
			'35.198.103.117',
			'34.159.52.120',
			'35.242.198.197',
			'34.159.92.129',
			'34.141.73.157',
			'34.159.74.66',
			'35.234.120.244',
			'34.141.107.2',
			'35.198.132.196',
			'35.198.101.107',
			'35.198.167.208',
			'34.107.127.117',
			'35.198.107.156',
			'185.68.68.0/22',
			'80.249.162.128/26',
			'80.249.166.0/26',
			'91.185.203.0/26',
			'34.159.226.30',
			'34.159.129.118',
			'34.159.95.245',
			'34.159.217.71',
			'34.107.122.183',
			'34.159.99.82',
			'34.159.61.126',
			'35.246.173.129',
			'34.159.169.221',
			'34.159.245.111',
			'35.246.243.62',
			'34.107.90.29',
			'34.159.44.194',
			'34.107.111.231',
			'35.246.244.208',
			'34.159.92.93',
			'34.159.18.255',
			'34.159.14.149',
			'34.159.95.198',
			'35.246.176.190',
			'34.159.124.37',
			'34.159.234.127',
			'34.159.14.122',
			'34.159.13.81',
			'34.159.149.73',
			'34.89.215.38',
			'35.198.77.157',
			'35.198.118.237',
			'35.242.253.7',
			'34.159.138.229',
			'34.141.91.103',
			'34.89.161.165',
			'178.77.214.9',
			'178.77.214.11',
			'178.77.214.30',
			'178.77.214.34',
			'178.77.214.58',
			'178.77.214.85',
			'178.77.214.88',
			'178.77.214.139',
			'141.170.145.80',
			'141.170.145.81/28'
		);
	}

}
