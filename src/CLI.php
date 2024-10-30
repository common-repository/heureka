<?php // phpcs:ignore

namespace Heureka;


use WP_CLI;
use WP_CLI_Command;

use Heureka\Managers\FeedsManager;

use function WP_CLI\Utils\make_progress_bar;

/**
 * Class CLI
 *
 * @property Plugin $plugin
 */
class CLI extends WP_CLI_Command {

	private $feed_manager;

	/**
	 * Construct.
	 */
	public function __construct() {
		parent::__construct();

		WP_CLI::add_command( 'heureka', self::class );
		$this->feed_manager = heureka_container()->get( FeedsManager::class );
	}

	public function generate( $args ) {
		$feed = $this->feed_manager->get_feed_by_id( $args[0] );
		if ( $feed ) {
			$feed->start_feed_generation();
			if ( $feed->get_product_ids_to_generate() ) {
				$this->generate_feed( $feed, count( $feed->get_product_ids_to_generate() ) );
			}
		}
	}

	private function generate_feed( $feed, $total_count, $processed = 0 ) {
		$progress  = make_progress_bar( sprintf( 'Generating feed %s', $feed->feed_name() ), $total_count );
		$processed += $feed->products_per_run();
		$feed->generate_feed();
		$progress->tick( $processed );

		if ( $feed->get_product_ids_to_generate() ) {
			$this->generate_feed( $feed, $total_count, $processed );
		}
	}
}
