const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const IgnoreEmitPlugin = require( 'ignore-emit-webpack-plugin' );
const dotenv = require( 'dotenv' ).config( { path: __dirname + '/.env' } );
const webpack = require( 'webpack' );

const path = require( 'path' );
const CssoWebpackPlugin = require( 'csso-webpack-plugin' ).default;
const NodePolyfillPlugin = require( 'node-polyfill-webpack-plugin' );

const camelCaseDash = ( string ) =>
	string.replace( /-([a-z])/g, ( _match, letter ) => letter.toUpperCase() );

const externals = [
	'api-fetch',
	'block-editor',
	'blocks',
	'components',
	'compose',
	'data',
	'date',
	'htmlEntities',
	'hooks',
	'edit-post',
	'element',
	'editor',
	'i18n',
	'plugins',
	'viewport',
	'ajax',
	'codeEditor',
	'rich-text',
	'primitives',
];

const globals = externals.reduce(
	( external, name ) => ( {
		...external,
		[ `@wordpress/${ name }` ]: `wp.${ camelCaseDash( name ) }`,
	} ),
	{}
);

const config = {
	...defaultConfig,
	entry: {
		admin: './packages/admin/src/index.js',
		'admin-style': './packages/admin/src/style.scss',
		'frontend-free': './packages/frontend/src/free.js',
		'frontend-premium': './packages/frontend/src/premium.js',
		'frontend-style': './packages/frontend/src/style.scss',
		extensions: './packages/extensions/src/index.js',
		'extensions-style': './packages/extensions/src/style.scss',
		gutenberg: './packages/gutenberg/src/index.js',
		'gutenberg-style': './packages/gutenberg/src/style.scss',
	},
	output: {
		clean: false,
		path: path.join( __dirname, './dist' ),
	},
	plugins: [
		...defaultConfig.plugins,

		new CssoWebpackPlugin( {
			forceMediaMerge: true,
		} ),

		new MiniCssExtractPlugin( {
			filename: ( pathData ) => {
				const filename = pathData.chunk.name.replace(
					/(style-|-style)/g,
					''
				);

				return `${ filename }.css`;
			},
		} ),

		// TODO: can we implement a better logic, other than surpressing the build chunks?
		new IgnoreEmitPlugin( /(-style)/i ),

		new webpack.DefinePlugin( {
			'process.env': JSON.stringify( dotenv.parsed ),
		} ),

		new NodePolyfillPlugin(),
	],
	externals: {
		wp: 'wp',
		lodash: 'lodash',
		react: 'React',
		'react-dom': 'ReactDOM',
		...globals,
	},
};

module.exports = config;
