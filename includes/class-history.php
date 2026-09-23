	<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * Handles editorial status history.
	 */
	class EditorialFlow_History {

		/**
		 * Add a status history record.
		 *
		 * @param int    $post_id    Post ID.
		 * @param string $old_status Previous status.
		 * @param string $new_status New status.
		 * @return bool
		 */
		public function add( $post_id, $old_status, $new_status ) {
			global $wpdb;

			$table_name = $wpdb->prefix . 'editorialflow_history';

			$result = $wpdb->insert(
				$table_name,
				array(
					'post_id'    => $post_id,
					'old_status' => $old_status,
					'new_status' => $new_status,
					'user_id'    => get_current_user_id(),
					'changed_at' => current_time( 'mysql' ),
				),
				array(
					'%d',
					'%s',
					'%s',
					'%d',
					'%s',
				)
			);

			return false !== $result;
		}

		/**
		 * Get status history for a post.
		 *
		 * @param int $post_id Post ID.
		 * @return array
		 */
		public function get_by_post( $post_id ) {
			global $wpdb;

			$table_name = $wpdb->prefix . 'editorialflow_history';

			$query = $wpdb->prepare(
				"SELECT * FROM {$table_name}
				WHERE post_id = %d
				ORDER BY changed_at DESC",
				$post_id
			);

			return $wpdb->get_results( $query, ARRAY_A );
		}
	}