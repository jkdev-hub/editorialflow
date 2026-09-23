<?php
/**
 * Handles editorial status data.
 *
 * @package EditorialFlow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles editorial status data for posts.
 */
class EditorialFlow_Editorial_Status {

	/**
	 * Post meta key.
	 *
	 * @var string
	 */
	private $meta_key = '_editorialflow_status';
    private $reviewer_meta_key = '_editorialflow_reviewer_name';
	private $note_meta_key = '_editorialflow_note';
	private $history;

	public function __construct( $history ) {
		$this->history = $history;
	}

	/**
	 * Get available editorial statuses.
	 *
	 * @return array
	 */
	public function get_statuses() {
		return array(
			'draft'         => __( 'Draft', 'editorialflow' ),
			'in_review'     => __( 'In Review', 'editorialflow' ),
			'approved'      => __( 'Approved', 'editorialflow' ),
			'needs_changes' => __( 'Needs Changes', 'editorialflow' ),
		);
	}

	/**
	 * Get editorial status for a post.
	 *
	 * @param int
	 * @return string
	 */
	public function get_status( $post_id ) {
		$status = get_post_meta( $post_id, $this->meta_key, true );

		if ( empty( $status ) ) {
			return get_option( 'editorialflow_default_status', 'draft' );
		}

		return $status;
	}

	/**
	 * Update editorial status for a post.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $status  Editorial status.
	 * @return bool|int
	 */
	public function update_status( $post_id, $status ) {
		$statuses = $this->get_statuses();

		if ( ! array_key_exists( $status, $statuses ) ) {
			return false;
		}

		$old_status = $this->get_status( $post_id );

		if ( $old_status === $status ) {
			return true;
		}

		$result = update_post_meta(
			$post_id,
			$this->meta_key,
			$status
		);

		if ( false !== $result ) {
			$this->history->add(
				$post_id,
				$old_status,
				$status
			);
		}

		return $result;
	}

    /**
	 * Get the reviewer name for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public function get_reviewer_name( $post_id ) {
		return get_post_meta(
			$post_id,
			$this->reviewer_meta_key,
			true
		);
	}

	/**
	 * Update the reviewer name for a post.
	 *
	 * @param int    $post_id       Post ID.
	 * @param string $reviewer_name Reviewer name.
	 * @return bool|int
	 */
	public function update_reviewer_name( $post_id, $reviewer_name ) {
		return update_post_meta(
			$post_id,
			$this->reviewer_meta_key,
			$reviewer_name
		);
	}

	/**
	 * Get the editorial note for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public function get_editorial_note( $post_id ) {
		return get_post_meta(
			$post_id,
			$this->note_meta_key,
			true
		);
	}

	/**
	 * Update the editorial note for a post.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $note    Editorial note.
	 * @return bool|int
	 */
	public function update_editorial_note( $post_id, $note ) {
		return update_post_meta(
			$post_id,
			$this->note_meta_key,
			$note
		);
	}
}