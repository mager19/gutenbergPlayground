/**
 * Shared Interactivity store for externado/feature-vote and externado/vote-summary.
 *
 * Server state (see inc/votes.php): counts, mine, features, isLoggedIn,
 * restUrl, countsUrl, pollInterval, isLive, nonce, errorMessage. Derived
 * values are getters here and closures on the server, so both render the
 * same output.
 */
import { store, getContext, getElement } from '@wordpress/interactivity';

const effortOrder = { A: 0, B: 1, C: 2 };

const countsFor = ( id ) => state.counts[ id ] || { yes: 0, no: 0 };

/*
 * Demo mode: visitors without a session vote with an anonymous token that
 * lives in this browser, so each browser (or private window) is one voter.
 * crypto.randomUUID() needs a secure context and the local site is plain
 * http, so the v4 UUID is built from getRandomValues().
 */
const VOTER_KEY = 'externado-voter';

const newUuid = () => {
	const bytes = crypto.getRandomValues( new Uint8Array( 16 ) );
	bytes[ 6 ] = ( bytes[ 6 ] & 0x0f ) | 0x40;
	bytes[ 8 ] = ( bytes[ 8 ] & 0x3f ) | 0x80;
	const hex = [ ...bytes ].map( ( b ) => b.toString( 16 ).padStart( 2, '0' ) );
	return `${ hex.slice( 0, 4 ).join( '' ) }-${ hex.slice( 4, 6 ).join( '' ) }-${ hex
		.slice( 6, 8 )
		.join( '' ) }-${ hex.slice( 8, 10 ).join( '' ) }-${ hex.slice( 10 ).join( '' ) }`;
};

let voterToken = '';
const getVoterToken = () => {
	if ( voterToken ) {
		return voterToken;
	}
	try {
		voterToken = window.localStorage.getItem( VOTER_KEY ) || '';
		if ( ! voterToken ) {
			voterToken = newUuid();
			window.localStorage.setItem( VOTER_KEY, voterToken );
		}
	} catch ( error ) {
		// Storage blocked: keep a per-page token so voting still works.
		voterToken = voterToken || newUuid();
	}
	return voterToken;
};

const requestHeaders = () =>
	state.isLoggedIn
		? { 'X-WP-Nonce': state.nonce }
		: { 'X-Externado-Voter': getVoterToken() };

// Live totals: one poller per page, shared by every vote and summary block.
let liveStarted = false;
let pendingVotes = 0;

const applyServerData = ( data ) => {
	Object.keys( data.counts ).forEach( ( id ) => {
		state.counts[ id ] = data.counts[ id ];
	} );
	Object.keys( state.features ).forEach( ( id ) => {
		state.mine[ id ] = ( data.mine && data.mine[ id ] ) || '';
	} );
};

const refreshTotals = async () => {
	// Skip while the tab is hidden or a vote is in flight, so the optimistic update is not overwritten.
	if ( document.hidden || pendingVotes > 0 ) {
		return;
	}
	try {
		const response = await fetch( state.countsUrl, {
			credentials: 'same-origin',
			cache: 'no-store',
			headers: requestHeaders(),
		} );
		if ( ! response.ok ) {
			throw new Error( `HTTP ${ response.status }` );
		}
		const data = await response.json();
		if ( pendingVotes === 0 ) {
			applyServerData( data );
		}
		state.isLive = true;
	} catch ( error ) {
		state.isLive = false;
	}
};

const { state } = store( 'externado/votes', {
	state: {
		get isYes() {
			return state.mine[ getContext().feature ] === 'yes';
		},
		get isNo() {
			return state.mine[ getContext().feature ] === 'no';
		},
		get yesCount() {
			return countsFor( getContext().feature ).yes;
		},
		get noCount() {
			return countsFor( getContext().feature ).no;
		},
		get isPriority() {
			const counts = countsFor( getContext().feature );
			return counts.yes > counts.no;
		},
		get isNotPriority() {
			return ! state.isPriority;
		},
		// Reset control: one stage at a time (idle → confirm → working → done).
		get isResetIdle() {
			return state.resetStage === 'idle';
		},
		get isResetConfirm() {
			return state.resetStage === 'confirm';
		},
		get isResetWorking() {
			return state.resetStage === 'working';
		},
		get isResetDone() {
			return state.resetStage === 'done';
		},
		get totalVotes() {
			return Object.values( state.counts ).reduce(
				( sum, counts ) => sum + counts.yes + counts.no,
				0
			);
		},
		get ranking() {
			return Object.keys( state.features )
				.map( ( id ) => {
					const counts = countsFor( id );
					return {
						id,
						title: state.features[ id ].title,
						effort: state.features[ id ].effort,
						// Booleans for data-wp-class (directives cannot compare values).
						isA: state.features[ id ].effort === 'A',
						isB: state.features[ id ].effort === 'B',
						isC: state.features[ id ].effort === 'C',
						yes: counts.yes,
						no: counts.no,
					};
				} )
				.sort(
					( a, b ) =>
						b.yes - a.yes ||
						a.no - b.no ||
						effortOrder[ a.effort ] - effortOrder[ b.effort ]
				);
		},
	},
	actions: {
		/**
		 * Optimistic vote: update local state, persist, and roll back on failure.
		 * Clicking the current choice again clears the vote.
		 */
		*vote() {
			const context = getContext();
			const { ref } = getElement();
			const choice = ref.dataset.vote;
			const feature = context.feature;
			const previous = state.mine[ feature ] || '';
			const next = previous === choice ? '' : choice;

			if ( ! state.counts[ feature ] ) {
				state.counts[ feature ] = { yes: 0, no: 0 };
			}
			const snapshot = { ...state.counts[ feature ] };

			if ( previous ) {
				state.counts[ feature ][ previous ] -= 1;
			}
			if ( next ) {
				state.counts[ feature ][ next ] += 1;
			}
			state.mine[ feature ] = next;
			context.error = false;
			pendingVotes += 1;

			try {
				const response = yield fetch( state.restUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: {
						'Content-Type': 'application/json',
						...requestHeaders(),
					},
					body: JSON.stringify( { feature, vote: next || 'clear' } ),
				} );

				if ( ! response.ok ) {
					throw new Error( `HTTP ${ response.status }` );
				}

				const data = yield response.json();

				// Sync with the server truth (includes other editors' votes).
				Object.keys( data.counts ).forEach( ( id ) => {
					state.counts[ id ] = data.counts[ id ];
				} );
				state.mine[ feature ] = data.vote || '';
			} catch ( error ) {
				state.counts[ feature ] = snapshot;
				state.mine[ feature ] = previous;
				context.error = true;
			} finally {
				pendingVotes -= 1;
			}
		},

		/** Reset control: ask for an inline confirmation instead of a browser dialog. */
		askReset() {
			state.resetError = false;
			state.resetStage = 'confirm';
		},
		cancelReset() {
			state.resetStage = 'idle';
		},

		/**
		 * Clear every vote. The other browsers pick up the zeros on their next poll;
		 * this one applies them right away.
		 */
		*confirmReset() {
			state.resetStage = 'working';
			pendingVotes += 1; // Pause polling so a stale poll cannot overwrite the zeros.
			try {
				const response = yield fetch( state.countsUrl, {
					method: 'DELETE',
					credentials: 'same-origin',
					headers: { 'X-WP-Nonce': state.nonce },
				} );
				if ( ! response.ok ) {
					throw new Error( `HTTP ${ response.status }` );
				}
				applyServerData( yield response.json() );
				state.resetStage = 'done';
			} catch ( error ) {
				state.resetStage = 'idle';
				state.resetError = true;
				return;
			} finally {
				pendingVotes -= 1;
			}
			// Keep the success message visible for a moment, then return to the button.
			yield new Promise( ( resolve ) => setTimeout( resolve, 4000 ) );
			state.resetStage = 'idle';
		},
	},
	callbacks: {
		/**
		 * Start polling the totals once per page (every block calls it on init).
		 * The first refresh also restores this browser's anonymous votes,
		 * which the server cannot know when it renders the page.
		 */
		startLive() {
			if ( liveStarted ) {
				return;
			}
			liveStarted = true;
			refreshTotals();
			window.setInterval( refreshTotals, state.pollInterval || 3000 );
			document.addEventListener( 'visibilitychange', () => {
				if ( ! document.hidden ) {
					refreshTotals();
				}
			} );
		},
	},
} );
