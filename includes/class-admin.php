<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles EditorialFlow admin functionality.
 */
class EditorialFlow_Admin {

	private $editorial_status;

	/**
	 * Initialize the admin class.
	 *
	 * @param EditorialFlow_Editorial_Status $editorial_status Editorial status handler.
	 */
	public function __construct( $editorial_status ) {
		$this->editorial_status = $editorial_status;
	}

	/**
	 * Register admin hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
		add_filter( 'manage_posts_columns', array( $this, 'add_status_column' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'render_status_column' ), 10, 2 );
	}
	/**
	 * Add the editorial status column.
	 *
	 * @param array $columns Post columns.
	 * @return array
	 */
	public function add_status_column( $columns ) {
		$columns['editorialflow_status'] = __( 'Editorial Status', 'editorialflow' );
	
		return $columns;
	}

	/**
	 * Display the editorial status column.
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function render_status_column( $column, $post_id ) {
		if ( 'editorialflow_status' !== $column ) {
			return;
		}

		$status   = $this->editorial_status->get_status( $post_id );
		$statuses = $this->editorial_status->get_statuses();

		if ( isset( $statuses[ $status ] ) ) {
			echo esc_html( $statuses[ $status ] );
		}
	}
	/**
	 * Register the editorial status meta box.
	 *
	 * @return void
	 */
	public function add_meta_box() {
		add_meta_box(
			'editorialflow_status',
			__( 'Editorial Status', 'editorialflow' ),
			array( $this, 'render_meta_box' ),
			'post',
			'side'
		);
	}

	/**
	 * Render the editorial status meta box.
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public function render_meta_box( $post ) {
		$current_status = $this->editorial_status->get_status( $post->ID );
		$statuses       = $this->editorial_status->get_statuses();
		$reviewer_name  = $this->editorial_status->get_reviewer_name( $post->ID );
		$editorial_note = $this->editorial_status->get_editorial_note( $post->ID );
		wp_nonce_field(
			'editorialflow_save_status',
			'editorialflow_status_nonce'
		); ?>
		<p>
			<label for="editorialflow_status">
				<?php esc_html_e( 'Editorial Status', 'editorialflow' ); ?>
			</label>
		</p>
		<p>
			<select name="editorialflow_status" id="editorialflow_status" class="widefat">
				<?php foreach ( $statuses as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"
						<?php selected( $current_status, $value ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="editorialflow_reviewer_name">
				<?php esc_html_e( 'Reviewer Name', 'editorialflow' ); ?>
			</label>
		</p>
		<p>
			<input type="text" id="editorialflow_reviewer_name" name="editorialflow_reviewer_name"
				value="<?php echo esc_attr( $reviewer_name ); ?>" class="widefat" />
		</p>
		<p>
			<label for="editorialflow_note"><?php esc_html_e( 'Editorial Note', 'editorialflow' ); ?></label>
		</p>
		<p>
			<textarea id="editorialflow_note" name="editorialflow_note" class="widefat"rows="4"><?php echo esc_textarea( $editorial_note ); ?></textarea>
		</p>
		<?php
	}

	/**
	 * Save editorial status and reviewer data.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_meta_box( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if (
			! isset( $_POST['editorialflow_status_nonce'] ) ||
			! wp_verify_nonce(
				sanitize_text_field(
					wp_unslash( $_POST['editorialflow_status_nonce'] )
				),
				'editorialflow_save_status'
			)
		) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		/*
		* Save the editorial status, reviewer name, and editorial note.
		*/

		if ( isset( $_POST['editorialflow_status'] ) ) {
			$status = sanitize_key(
				wp_unslash( $_POST['editorialflow_status'] )
			);
			$this->editorial_status->update_status(
				$post_id,
				$status
			);
		}

		if ( isset( $_POST['editorialflow_reviewer_name'] ) ) {
			$reviewer_name = sanitize_text_field(
				wp_unslash( $_POST['editorialflow_reviewer_name'] )
			);
			$this->editorial_status->update_reviewer_name(
				$post_id,
				$reviewer_name
			);
		}

		if ( isset( $_POST['editorialflow_note'] ) ) {
			$editorial_note = sanitize_textarea_field(
				wp_unslash( $_POST['editorialflow_note'] )
			);

			$this->editorial_status->update_editorial_note(
				$post_id,
				$editorial_note
			);
		}
	}
}