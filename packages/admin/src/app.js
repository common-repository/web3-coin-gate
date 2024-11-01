import VerificationForm from './components/verification-form';

import { isEmpty, has, set } from 'lodash';
import { useState, useLayoutEffect } from '@wordpress/element';
import Loading from './components/loading';
import Instructions from './components/instructions';

function App() {
	const [ data, setData ] = useState( {
		nftAddress: '',
		successOutputMessage: '',
		failureOutputMessage: '',
	} );

	const [ loading, setLoading ] = useState( false );

	const loadInitialData = async () => {
		try {
			setLoading( true );

			const api = new wp.api.models.Settings();

			let options = await api.fetch();
			let initialData = {};

			if ( has( options, 'web3-coin-gate-token-address' ) ) {
				set(
					initialData,
					'nftAddress',
					options[ 'web3-coin-gate-token-address' ]
				);
			}

			if ( has( options, 'web3-coin-gate-success-text' ) ) {
				set(
					initialData,
					'successOutputMessage',
					options[ 'web3-coin-gate-success-text' ]
				);
			}

			if ( has( options, 'web3-coin-gate-failure-text' ) ) {
				set(
					initialData,
					'failureOutputMessage',
					options[ 'web3-coin-gate-failure-text' ]
				);
			}

			if ( has( options, 'web3-coin-gate-minimum-token-count' ) ) {
				set(
					initialData,
					'minimumTokenCount',
					options[ 'web3-coin-gate-minimum-token-count' ]
				);
			}

			// Updating initial data.
			setData( initialData );
		} catch ( err ) {
			console.log( err );
		} finally {
			setLoading( false );
		}
	};

	useLayoutEffect( () => loadInitialData(), [] );

	if ( loading ) {
		return <Loading />;
	}

	return (
		<>
			<div className="nft-verify-app-mainframe">
				<Instructions />
				<br />
				<hr />
				<br />

				<VerificationForm data={ data } setData={ setData } />
			</div>
		</>
	);
}

export default App;
