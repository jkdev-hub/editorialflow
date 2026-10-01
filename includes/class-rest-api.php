<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles EditorialFlow REST API functionality.
 */
class EditorialFlow_REST_API {

	private $editorial_status;

	/**
	 * Initialize the REST API class.
	 *
	 * @param EditorialFlow_Editorial_Status $editorial_status Editorial status handler.
	 */
	public function __construct( $editorial_status ) {
		$this->editorial_status = $editorial_status;
	}

	/**
	 * Register REST API hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST API routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			'editorialflow/v1',
			'/status/(?P<id>\d+)',
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'update_status' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);
	}

    /**
     * Check permission to update editorial status.
     *
     * @param WP_REST_Request $request REST request.
     * @return bool
     */
    public function permissions_check( $request ) {
        $post_id = absint( $request['id'] );

        return current_user_can( 'edit_post', $post_id );
    }

    /**
     * Update editorial status.
     *
     * @param WP_REST_Request $request REST request.
     * @return WP_REST_Response|WP_Error
     */
    public function update_status( $request ) {
        $post_id = absint( $request['id'] );
        $status  = sanitize_key( $request->get_param( 'status' ) );

        if ( ! get_post( $post_id ) ) {
            return new WP_Error(
                'editorialflow_invalid_post',
                __( 'Post not found.', 'editorialflow' ),
                array( 'status' => 404 )
            );
        }

        $statuses = $this->editorial_status->get_statuses();

        if ( ! isset( $statuses[ $status ] ) ) {
            return new WP_Error(
                'editorialflow_invalid_status',
                __( 'Invalid editorial status.', 'editorialflow' ),
                array( 'status' => 400 )
            );
        }
        $old_status = $this->editorial_status->get_status( $post_id );
        $history_id = $this->editorial_status->update_status(
            $post_id,
            $status
        );

        if ( false === $history_id ) {
            return new WP_Error(
                'editorialflow_update_failed',
                __( 'Unable to update editorial status.', 'editorialflow' ),
                array( 'status' => 500 )
            );
        }
        $delete_url = '';

        $delete_url = wp_nonce_url(
            admin_url(
                'admin-post.php?action=editorialflow_delete_history&history_id='
                . absint( $history_id )
            ),
            'editorialflow_delete_history_' . absint( $history_id )
        );

        return rest_ensure_response(
            array(
                'success' => true,
                'post_id' => $post_id,
                'old_status' => $old_status,
                'status'  => $status,
                'delete_url' => $delete_url,
            )
        );
    }
}