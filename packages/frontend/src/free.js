import config from '../../../config.json';
import * as ethers from 'ethers';

function setVerificationLoading( status ) {
	const verificationButtons = document.querySelectorAll(
		'.wpcoingate-verification-connect'
	);

	verificationButtons.forEach( ( verificationButton ) => {
		if ( status ) {
			verificationButton.innerHTML = 'Verifying Assets...';
		} else {
			verificationButton.innerHTML = 'Verify Assets';
		}
	} );
}

function setVerifiedStatus( message ) {
	const verificationPlaceholders = document.querySelectorAll(
		'.wpcoingate-verification-placeholder'
	);

	verificationPlaceholders.forEach( ( placeholder ) => {
		placeholder.classList.remove( 'unloaded' );
		placeholder.innerHTML = message;
	} );
}

async function outputVerificationResults( signature, onSuccess ) {
	setVerificationLoading( true );

	try {
		let proxy = web3CoinGateRestUrl;

		// vanilla fetch request with body.
		const response = await fetch( proxy, {
			method: 'POST',
			body: JSON.stringify( { signature } ),
			headers: {
				'Content-Type': 'application/json',
			},
		} );

		const data = await response.json();

		setVerifiedStatus( data?.message );

		if ( data?.success ) {
			onSuccess();
		}
	} catch ( err ) {
		console.error( err );
	} finally {
		setVerificationLoading( false );
	}
}

let hasWallet = false;

window.addEventListener( 'load', () => {
	const verificationButtons = document.querySelectorAll(
		'.wpcoingate-verification-connect'
	);

	const walletConnectedPlaceholders = document.querySelectorAll(
		'.wpcoingate-wallet-connected-placeholder'
	);

	verificationButtons.forEach( ( verificationButton ) => {
		verificationButton.addEventListener( 'click', async () => {
			if ( ! hasWallet ) {
				const accounts = await window.ethereum.request( {
					method: 'eth_requestAccounts',
				} );

				const requiredAccount = accounts[ 0 ];

				walletConnectedPlaceholders.forEach(
					( connectedPlaceholder ) => {
						connectedPlaceholder.innerHTML =
							'Wallet Connected: ' + requiredAccount;
					}
				);

				verificationButtons.forEach( ( verificationButton ) => {
					verificationButton.innerHTML = 'Verify Assets';
					hasWallet = true;
				} );
			} else {
				const web3 = new ethers.providers.Web3Provider(
					window.ethereum
				);
				const signer = web3.getSigner();
				const signature = await signer.signMessage( config.message );

				outputVerificationResults( signature, () => {
					verificationButton.style.display = 'none';

					walletConnectedPlaceholders.forEach(
						( walletConnectedPlaceholder ) => {
							walletConnectedPlaceholder.style.display = 'none';
						}
					);
				} );
			}
		} );
	} );
} );
