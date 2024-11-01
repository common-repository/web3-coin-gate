import { useState } from '@wordpress/element';
import { TextControl, Button, withNotices } from '@wordpress/components';

function VerificationForm( props ) {
	const [ isSaving, setSaving ] = useState( false );

	const onDataProcessRequest = async () => {
		try {
			setSaving( true );

			const model = new wp.api.models.Settings( {
				'web3-coin-gate-token-address': props.data.nftAddress,
				'web3-coin-gate-success-text': props.data.successOutputMessage,
				'web3-coin-gate-failure-text': props.data.failureOutputMessage,
				'web3-coin-gate-minimum-token-count':
					props.data.minimumTokenCount,
			} );

			await model.save();
		} catch ( error ) {
			props.noticeOperations.createErrorNotice(
				'Something went wrong, Please try again'
			);
		} finally {
			setSaving( false );
		}
	};

	return (
		<div className="wpnftverify-pre-verification-form">
			<TextControl
				label="Token Address"
				value={ props.data.nftAddress }
				placeholder="Enter you address here..."
				onChange={ ( newNFTAddress ) =>
					props.setData( {
						...props.data,
						nftAddress: newNFTAddress,
					} )
				}
			/>

			<br />

			<TextControl
				label="Minimum Token Count"
				value={ props.data.minimumTokenCount }
				min={ 1 }
				type="number"
				placeholder="Enter the minimum token count..."
				help="Number of minimum tokens user should atleast have in their wallet."
				onChange={ ( newMinimumTokenCount ) =>
					props.setData( {
						...props.data,
						minimumTokenCount: Number( newMinimumTokenCount ),
					} )
				}
			/>

			<br />

			<TextControl
				label="Success Message"
				value={ props.data.successOutputMessage }
				placeholder="Enter you message here..."
				help="Enter the message that should be displayed when the user has verified token."
				onChange={ ( newSuccessOutputMessage ) =>
					props.setData( {
						...props.data,
						successOutputMessage: newSuccessOutputMessage,
					} )
				}
			/>

			<TextControl
				label="Failure Message"
				value={ props.data.failureOutputMessage }
				placeholder="Enter you message here..."
				help="Enter the message that should be displayed when the user does not has verified token."
				onChange={ ( newFailureOutputMessage ) =>
					props.setData( {
						...props.data,
						failureOutputMessage: newFailureOutputMessage,
					} )
				}
			/>

			{ props.noticeUI }

			<div className="wpnftverify-pre-verification-form-footer">
				<Button
					variant="primary"
					isBusy={ isSaving }
					onClick={ onDataProcessRequest }
				>
					{ isSaving ? 'Saving...' : 'Save' }
				</Button>
			</div>
		</div>
	);
}

export default withNotices( VerificationForm );
