import React from 'react';

import { Card, CardBody } from '@wordpress/components';

function Instructions() {
	return (
		<Card>
			<CardBody>
				<h2>✅ Verify Token Ownership</h2>
				<p>
					Please follow the steps below to verify the ownership of
					your Token and display results on your website:
				</p>
				<ul>
					<li>
						<strong>Step 1:</strong> Store Your Token Address below
						for verification
					</li>
					<li>
						<strong>Step 2:</strong> Use the{ ' ' }
						<code>[coin-gate-verify-app]</code> shortcode to render
						the verification app.
					</li>
					<li>
						<strong>Step 3:</strong> Use the{ ' ' }
						<code>[coin-gate-output]</code> to render the
						verification results.
					</li>
				</ul>

				<hr />

				<p>
					<strong>Note: </strong>
					Web3 Coin Gate only works on <strong>Ethereum</strong> main
					net. Therefore, Please use an{ ' ' }
					<strong>Ethereum token</strong> when using Web3 Coin Gate.
					Please upgrade to pro if you want multiple networks.
				</p>
			</CardBody>
		</Card>
	);
}

export default Instructions;
