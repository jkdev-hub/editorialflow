document.addEventListener( 'DOMContentLoaded', function () {
	const button = document.getElementById( 'editorialflow-update-status' );
	if ( ! button ) {
		return;
	}

	button.addEventListener( 'click', function () {
		const statusSelect = document.getElementById( 'editorialflow_status_select' );
		const postId       = button.dataset.postId;
		const status       = statusSelect ? statusSelect.value : '';

		wp.apiFetch( {
			path: '/editorialflow/v1/status/' + postId,
			method: 'POST',
			data: {
				status: status
			}
		} )
        .then( function ( response ) {
            const historyList = document.getElementById( 'editorialflow-history-list' );
            const message     = document.getElementById( 'editorialflow-status-message' );

            if ( historyList ) {
                const historyItem = document.createElement( 'p' );

                historyItem.textContent = response.old_status + ' → ' + response.status + ' ';

                const deleteLink = document.createElement( 'a' );

                deleteLink.href = response.delete_url.replaceAll( '&amp;', '&' );
                deleteLink.textContent = 'Delete';
                deleteLink.addEventListener( 'click', function ( event ) {
                    if ( ! window.confirm( 'Delete this history record?' ) ) {
                        event.preventDefault();
                    }
                } );

                historyItem.appendChild( deleteLink );
                historyList.prepend( historyItem );
            }

            if ( message ) {
                message.textContent = 'Status updated.';
            }
        } )
        .catch( function ( error ) {
            console.error( error );

            if ( message ) {
                message.textContent = 'Unable to update status.';
            }
        } );
	} );
} );