<?php
/**
 * Votes storage, aggregation, REST API and shared Interactivity state.
 *
 * Votes are stored per user in the `externado_votes` user meta as an
 * array of feature_id => 'yes'|'no'.
 *
 * Demo mode: visitors without a session can vote too. Each browser sends an
 * anonymous voter token (a UUID kept in localStorage) and its votes live in
 * the `externado_anon_votes` option as token => array of feature_id => vote.
 * This lets the presenter open several browsers and watch the totals grow.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const EXTERNADO_VOTES_META      = 'externado_votes';
const EXTERNADO_ANON_VOTES      = 'externado_anon_votes';
const EXTERNADO_ANON_VOTERS_MAX = 500;

/**
 * Clean a votes array against the catalog.
 *
 * @param mixed $votes Raw votes.
 * @return array<string, string>
 */
function externado_clean_votes( $votes ) {
	if ( ! is_array( $votes ) ) {
		return array();
	}

	$clean = array();
	foreach ( $votes as $feature => $vote ) {
		if ( externado_is_valid_feature( $feature ) && in_array( $vote, array( 'yes', 'no' ), true ) ) {
			$clean[ $feature ] = $vote;
		}
	}

	return $clean;
}

/**
 * Valid anonymous voter token from the current request, or empty string.
 *
 * @param WP_REST_Request|null $request Request.
 * @return string
 */
function externado_anon_token( $request = null ) {
	$token = $request ? (string) $request->get_header( 'X-Externado-Voter' ) : '';

	return 1 === preg_match( '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $token ) ? $token : '';
}

/**
 * All anonymous votes.
 *
 * @return array<string, array<string, string>>
 */
function externado_get_anon_votes() {
	$all = get_option( EXTERNADO_ANON_VOTES, array() );

	return is_array( $all ) ? $all : array();
}

/**
 * Save (or clear) the vote of an anonymous voter.
 *
 * @param string $token   Voter token (already validated).
 * @param string $feature Feature ID (already validated).
 * @param string $vote    'yes', 'no' or 'clear'.
 * @return array<string, string>|WP_Error The voter's votes after the change.
 */
function externado_set_anon_vote( $token, $feature, $vote ) {
	$all   = externado_get_anon_votes();
	$votes = externado_clean_votes( isset( $all[ $token ] ) ? $all[ $token ] : array() );

	if ( ! isset( $all[ $token ] ) && count( $all ) >= EXTERNADO_ANON_VOTERS_MAX ) {
		return new WP_Error( 'externado_too_many_voters', __( 'Se alcanzó el máximo de votantes de la demostración.', 'externado' ), array( 'status' => 429 ) );
	}

	if ( 'clear' === $vote ) {
		unset( $votes[ $feature ] );
	} else {
		$votes[ $feature ] = $vote;
	}

	if ( $votes ) {
		$all[ $token ] = $votes;
	} else {
		unset( $all[ $token ] );
	}
	update_option( EXTERNADO_ANON_VOTES, $all, false );

	return $votes;
}

/**
 * Get the votes of one user, filtered against the catalog.
 *
 * @param int $user_id User ID.
 * @return array<string, string>
 */
function externado_get_user_votes( $user_id ) {
	return externado_clean_votes( get_user_meta( $user_id, EXTERNADO_VOTES_META, true ) );
}

/**
 * Save (or clear) the vote of a user for one feature.
 *
 * @param int    $user_id User ID.
 * @param string $feature Feature ID (already validated).
 * @param string $vote    'yes', 'no' or 'clear'.
 * @return array<string, string> The user's votes after the change.
 */
function externado_set_user_vote( $user_id, $feature, $vote ) {
	$votes = externado_get_user_votes( $user_id );

	if ( 'clear' === $vote ) {
		unset( $votes[ $feature ] );
	} else {
		$votes[ $feature ] = $vote;
	}

	if ( $votes ) {
		update_user_meta( $user_id, EXTERNADO_VOTES_META, $votes );
	} else {
		delete_user_meta( $user_id, EXTERNADO_VOTES_META );
	}

	return $votes;
}

/**
 * Aggregate votes of all users.
 *
 * @return array<string, array{yes: int, no: int}> Counts for every catalog feature.
 */
function externado_get_vote_counts() {
	$counts = array();
	foreach ( array_keys( externado_get_features() ) as $feature ) {
		$counts[ $feature ] = array(
			'yes' => 0,
			'no'  => 0,
		);
	}

	$user_ids = get_users(
		array(
			'meta_key' => EXTERNADO_VOTES_META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'fields'   => 'ID',
			'number'   => -1,
		)
	);

	foreach ( $user_ids as $user_id ) {
		foreach ( externado_get_user_votes( (int) $user_id ) as $feature => $vote ) {
			++$counts[ $feature ][ $vote ];
		}
	}

	foreach ( externado_get_anon_votes() as $votes ) {
		foreach ( externado_clean_votes( $votes ) as $feature => $vote ) {
			++$counts[ $feature ][ $vote ];
		}
	}

	return $counts;
}

/**
 * Votes of whoever makes the request: the logged-in user, or the anonymous token.
 *
 * @param WP_REST_Request|null $request Request.
 * @return array<string, string>
 */
function externado_get_requester_votes( $request = null ) {
	if ( is_user_logged_in() ) {
		return externado_get_user_votes( get_current_user_id() );
	}

	$token = externado_anon_token( $request );
	if ( '' === $token ) {
		return array();
	}

	$all = externado_get_anon_votes();

	return externado_clean_votes( isset( $all[ $token ] ) ? $all[ $token ] : array() );
}

/**
 * Register REST routes.
 */
function externado_register_vote_routes() {
	register_rest_route(
		'externado/v1',
		'/vote',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'externado_rest_vote',
			'permission_callback' => 'externado_rest_votes_permission',
			'args'                => array(
				'feature' => array(
					'type'              => 'string',
					'required'          => true,
					'validate_callback' => static function ( $value ) {
						if ( ! externado_is_valid_feature( $value ) ) {
							return new WP_Error( 'externado_invalid_feature', __( 'La funcionalidad no existe en el catálogo.', 'externado' ), array( 'status' => 400 ) );
						}
						return true;
					},
				),
				'vote'    => array(
					'type'     => 'string',
					'required' => true,
					'enum'     => array( 'yes', 'no', 'clear' ),
				),
			),
		)
	);

	register_rest_route(
		'externado/v1',
		'/votes',
		array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'externado_rest_votes',
				'permission_callback' => 'externado_rest_votes_permission',
			),
			// Reset every vote (signed-in and anonymous). Administrators only; cookie auth requires the REST nonce.
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => 'externado_rest_reset_votes',
				'permission_callback' => static function () {
					return current_user_can( 'manage_options' );
				},
			),
		)
	);
}

/**
 * DELETE /externado/v1/votes: clear all votes so the session starts at zero.
 *
 * @return WP_REST_Response
 */
function externado_rest_reset_votes() {
	delete_metadata( 'user', 0, EXTERNADO_VOTES_META, '', true );
	delete_option( EXTERNADO_ANON_VOTES );

	return rest_ensure_response(
		array(
			'counts' => externado_get_vote_counts(),
			'mine'   => (object) array(),
		)
	);
}
add_action( 'rest_api_init', 'externado_register_vote_routes' );

/**
 * Demo mode: anyone can read results; voting needs a session or an anonymous token.
 *
 * Cookie-authenticated requests are only logged in when they carry a valid
 * `wp_rest` nonce; without it they count as anonymous and need the token.
 *
 * @param WP_REST_Request $request Request.
 * @return true|WP_Error
 */
function externado_rest_votes_permission( WP_REST_Request $request ) {
	if ( 'GET' === $request->get_method() || is_user_logged_in() || '' !== externado_anon_token( $request ) ) {
		return true;
	}

	return new WP_Error(
		'externado_voter_required',
		__( 'Falta el identificador del votante.', 'externado' ),
		array( 'status' => 400 )
	);
}

/**
 * POST /externado/v1/vote.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function externado_rest_vote( WP_REST_Request $request ) {
	$feature = $request['feature'];
	$votes   = is_user_logged_in()
		? externado_set_user_vote( get_current_user_id(), $feature, $request['vote'] )
		: externado_set_anon_vote( externado_anon_token( $request ), $feature, $request['vote'] );

	if ( is_wp_error( $votes ) ) {
		return $votes;
	}

	$counts = externado_get_vote_counts();

	return rest_ensure_response(
		array(
			'feature' => $feature,
			'vote'    => isset( $votes[ $feature ] ) ? $votes[ $feature ] : '',
			'counts'  => $counts,
			'mine'    => (object) $votes,
		)
	);
}

/**
 * GET /externado/v1/votes.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function externado_rest_votes( WP_REST_Request $request ) {
	$response = rest_ensure_response(
		array(
			'counts' => externado_get_vote_counts(),
			'mine'   => (object) externado_get_requester_votes( $request ),
		)
	);
	$response->header( 'Cache-Control', 'no-store' );

	return $response;
}

/**
 * Request-level cache of the data shared by the vote and summary blocks.
 *
 * @return array{counts: array, mine: array}
 */
function externado_votes_data() {
	static $data = null;

	if ( null === $data ) {
		$data = array(
			'counts' => externado_get_vote_counts(),
			'mine'   => is_user_logged_in() ? externado_get_user_votes( get_current_user_id() ) : array(),
		);
	}

	return $data;
}

/**
 * Feature ID from the current Interactivity context.
 *
 * @return string
 */
function externado_votes_context_feature() {
	$context = wp_interactivity_get_context();

	return isset( $context['feature'] ) ? (string) $context['feature'] : '';
}

/**
 * Whether a feature has more "yes" than "no" votes.
 *
 * @param string $feature Feature ID.
 * @return bool
 */
function externado_feature_is_priority( $feature ) {
	$data = externado_votes_data();

	if ( ! isset( $data['counts'][ $feature ] ) ) {
		return false;
	}

	return $data['counts'][ $feature ]['yes'] > $data['counts'][ $feature ]['no'];
}

/**
 * Ranked list of features: most "yes" first, then fewer "no", then lower effort.
 *
 * @return array<int, array<string, mixed>>
 */
function externado_get_feature_ranking() {
	$data    = externado_votes_data();
	$ranking = array();

	foreach ( externado_get_features() as $id => $feature ) {
		$ranking[] = array(
			'id'     => $id,
			'title'  => $feature['title'],
			'effort' => $feature['effort'],
			// Booleans for data-wp-class (directives cannot compare values).
			'isA'    => 'A' === $feature['effort'],
			'isB'    => 'B' === $feature['effort'],
			'isC'    => 'C' === $feature['effort'],
			'yes'    => $data['counts'][ $id ]['yes'],
			'no'     => $data['counts'][ $id ]['no'],
		);
	}

	usort(
		$ranking,
		static function ( $a, $b ) {
			if ( $a['yes'] !== $b['yes'] ) {
				return $b['yes'] - $a['yes'];
			}
			if ( $a['no'] !== $b['no'] ) {
				return $a['no'] - $b['no'];
			}
			return strcmp( $a['effort'], $b['effort'] );
		}
	);

	return $ranking;
}

/**
 * Initialize the shared `externado/votes` Interactivity store once per request.
 *
 * Derived values are closures so the server renders the same output the
 * client store computes with its getters.
 */
function externado_votes_init_state() {
	static $done = false;

	if ( $done ) {
		return;
	}
	$done = true;

	$data     = externado_votes_data();
	$features = array();
	foreach ( externado_get_features() as $id => $feature ) {
		$features[ $id ] = array(
			'title'  => $feature['title'],
			'effort' => $feature['effort'],
		);
	}

	wp_interactivity_state(
		'externado/votes',
		array(
			'counts'        => $data['counts'],
			'mine'          => (object) $data['mine'],
			'features'      => $features,
			'isLoggedIn'    => is_user_logged_in(),
			'restUrl'       => esc_url_raw( rest_url( 'externado/v1/vote' ) ),
			'countsUrl'     => esc_url_raw( rest_url( 'externado/v1/votes' ) ),
			'pollInterval'  => 3000,
			'isLive'        => false,
			// Reset control (administrators): idle → confirm → working → done.
			'resetStage'    => 'idle',
			'resetError'    => false,
			'isResetIdle'   => true,
			'isResetConfirm' => false,
			'isResetWorking' => false,
			'isResetDone'   => false,
			'nonce'         => is_user_logged_in() ? wp_create_nonce( 'wp_rest' ) : '',
			'errorMessage'  => __( 'No se pudo guardar su voto. Intente de nuevo.', 'externado' ),
			'isYes'         => static function () {
				$data    = externado_votes_data();
				$feature = externado_votes_context_feature();
				return isset( $data['mine'][ $feature ] ) && 'yes' === $data['mine'][ $feature ];
			},
			'isNo'          => static function () {
				$data    = externado_votes_data();
				$feature = externado_votes_context_feature();
				return isset( $data['mine'][ $feature ] ) && 'no' === $data['mine'][ $feature ];
			},
			'yesCount'      => static function () {
				$data    = externado_votes_data();
				$feature = externado_votes_context_feature();
				return isset( $data['counts'][ $feature ] ) ? $data['counts'][ $feature ]['yes'] : 0;
			},
			'noCount'       => static function () {
				$data    = externado_votes_data();
				$feature = externado_votes_context_feature();
				return isset( $data['counts'][ $feature ] ) ? $data['counts'][ $feature ]['no'] : 0;
			},
			'isPriority'    => static function () {
				return externado_feature_is_priority( externado_votes_context_feature() );
			},
			'isNotPriority' => static function () {
				return ! externado_feature_is_priority( externado_votes_context_feature() );
			},
			'totalVotes'    => static function () {
				$total = 0;
				foreach ( externado_votes_data()['counts'] as $counts ) {
					$total += $counts['yes'] + $counts['no'];
				}
				return $total;
			},
			'ranking'       => static function () {
				return externado_get_feature_ranking();
			},
		)
	);
}
