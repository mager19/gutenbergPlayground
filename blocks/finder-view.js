/**
 * Interactivity store for externado/program-finder.
 *
 * Server state (see inc/finder.php): restUrl, nonce, restNonce and UI strings.
 * Each block keeps its own search in the context: query, one status flag per
 * state (idle, loading, results, empty, error) and the results list.
 */
import { store, getContext, getElement, withSyncEvent } from '@wordpress/interactivity';

const STATUSES = [ 'idle', 'loading', 'results', 'empty', 'error' ];

/**
 * Switch the visible state. Only one flag is true at a time.
 *
 * @param {Object} context Block context.
 * @param {string} status  One of STATUSES.
 */
const setStatus = ( context, status ) => {
	STATUSES.forEach( ( name ) => {
		const key = 'is' + name.charAt( 0 ).toUpperCase() + name.slice( 1 );
		context[ key ] = name === status;
	} );
	context.hasAnswer = status === 'results' || status === 'empty';
};

const errorFor = ( status ) => {
	if ( status === 403 ) {
		return state.errorNonce;
	}
	if ( status === 429 ) {
		return state.errorLimit;
	}
	if ( status === 400 ) {
		return state.errorShort;
	}
	return state.errorDefault;
};

/**
 * Run a search for the context's query. Generator so the runtime keeps the scope across awaits.
 *
 * @param {Object} context Block context.
 */
function* search( context ) {
	const query = ( context.query || '' ).trim();

	if ( context.isLoading ) {
		return;
	}
	if ( query.length < 2 ) {
		context.errorMessage = state.errorShort;
		context.canRetry = false;
		setStatus( context, 'error' );
		return;
	}

	context.canRetry = true;
	context.lastQuery = query;
	setStatus( context, 'loading' );

	const headers = {
		'Content-Type': 'application/json',
		'X-Externado-Finder-Nonce': state.nonce,
	};
	if ( state.restNonce ) {
		headers[ 'X-WP-Nonce' ] = state.restNonce;
	}

	try {
		const response = yield fetch( state.restUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers,
			body: JSON.stringify( { query } ),
		} );

		if ( ! response.ok ) {
			context.errorMessage = errorFor( response.status );
			setStatus( context, 'error' );
			return;
		}

		const data = yield response.json();
		const results = Array.isArray( data.results ) ? data.results : [];

		context.results = results;
		context.isAi = data.mode === 'ai';
		context.modeLabel = context.isAi ? state.labelAi : state.labelWords;
		context.showAiHint = ! context.isAi && ! data.ai_configured;
		context.showAiFailed = ! context.isAi && !! data.ai_failed;
		context.countLabel =
			results.length === 1
				? state.countOne
				: state.countMany.replace( '%d', String( results.length ) );
		setStatus( context, results.length ? 'results' : 'empty' );
	} catch ( error ) {
		context.errorMessage = state.errorDefault;
		setStatus( context, 'error' );
	}
}

const { state } = store( 'externado/finder', {
	actions: {
		setQuery: withSyncEvent( ( event ) => {
			getContext().query = event.target.value;
		} ),

		/** Enter or "Buscar": no page reload. */
		submit: withSyncEvent( function* ( event ) {
			event.preventDefault();
			yield* search( getContext() );
		} ),

		/** Example chip: fill the input and search right away. */
		*useExample() {
			const context = getContext();
			const { ref } = getElement();
			if ( context.isLoading ) {
				return;
			}
			context.query = ref.dataset.query || '';
			yield* search( context );
		},

		*retry() {
			const context = getContext();
			context.query = context.lastQuery || context.query;
			yield* search( context );
		},
	},
} );
