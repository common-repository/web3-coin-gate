import React from 'react';
import { Spinner } from '@wordpress/components';

function Loading() {
	return (
		<div className="wp-verify-nft-loader">
			<Spinner />
		</div>
	);
}

export default Loading;
