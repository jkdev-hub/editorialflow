<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles EditorialFlow settings.
 */
class EditorialFlow_Settings {

	/**
	 * Register settings hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
     * Add the EditorialFlow admin page.
     *
     * @return void
     */
    public function add_settings_page() {
        add_menu_page(
            __( 'EditorialFlow Settings', 'editorialflow' ),
            __( 'EditorialFlow', 'editorialflow' ),
            'manage_editorialflow', 'editorialflow',
            array( $this, 'render_settings_page' ),
            'dashicons-edit-page', 25
        );
    }

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'EditorialFlow Settings', 'editorialflow' ); ?></h1>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'editorialflow_settings' );
				do_settings_sections( 'editorialflow' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register EditorialFlow settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'editorialflow_settings',
			'editorialflow_default_status',
			array(
				'sanitize_callback' => array( $this, 'sanitize_default_status' ),
				'default'           => 'draft',
			)
		);

		add_settings_section(
			'editorialflow_general',
			__( 'General Settings', 'editorialflow' ),
			'__return_false', 'editorialflow'
		);

		add_settings_field(
			'editorialflow_default_status',
			__( 'Default Editorial Status', 'editorialflow' ),
			array( $this, 'render_default_status_field' ),
			'editorialflow', 'editorialflow_general'
		);
	}

	/**
	 * Render the default status field.
	 *
	 * @return void
	 */
	public function render_default_status_field() {
		$value = get_option( 'editorialflow_default_status', 'draft' ); ?>
		<select name="editorialflow_default_status">
			<option value="draft" <?php selected( $value, 'draft' ); ?>>
				<?php esc_html_e( 'Draft', 'editorialflow' ); ?>
			</option>
			<option value="in_review" <?php selected( $value, 'in_review' ); ?>>
				<?php esc_html_e( 'In Review', 'editorialflow' ); ?>
			</option>
			<option value="approved" <?php selected( $value, 'approved' ); ?>>
				<?php esc_html_e( 'Approved', 'editorialflow' ); ?>
			</option>
			<option value="needs_changes" <?php selected( $value, 'needs_changes' ); ?>>
				<?php esc_html_e( 'Needs Changes', 'editorialflow' ); ?>
			</option>
		</select>
		<?php
	}

	/**
	 * Sanitize the default editorial status.
	 *
	 * @param string $status Status value.
	 * @return string
	 */
	public function sanitize_default_status( $status ) {
		$allowed_statuses = array(
			'draft',
			'in_review',
			'approved',
			'needs_changes',
		);

		$status = sanitize_key( $status );

		if ( ! in_array( $status, $allowed_statuses, true ) ) {
			return 'draft';
		}

		return $status;
	}
	
}