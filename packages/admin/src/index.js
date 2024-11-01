import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

import App from './app';

domReady( () => {
	let root = document.querySelector( '#wp-verify-nft-root' );
	render( <App />, root );
} );
