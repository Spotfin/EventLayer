<?php
/**
 * Gating provider accessor.
 *
 * @package EventLayer
 */

namespace EventLayer\Gating;

/**
 * Resolves the active gating provider (default: FreeGatingProvider).
 *
 * Add-ons replace the provider via the `eventlayer_gating_provider` filter.
 *
 * @since 1.0.0
 */
final class Gating {

	/**
	 * Resolved provider.
	 *
	 * @var GatingProvider|null
	 */
	private static ?GatingProvider $provider = null;

	/**
	 * Get the active gating provider.
	 *
	 * @return GatingProvider
	 */
	public static function provider(): GatingProvider {
		if ( null === self::$provider ) {
			$default = new FreeGatingProvider();

			/**
			 * Replace the gating provider.
			 *
			 * @since 1.0.0
			 *
			 * @param GatingProvider $default Default free-tier provider.
			 */
			$provider = apply_filters( 'eventlayer_gating_provider', $default );

			self::$provider = $provider instanceof GatingProvider ? $provider : $default;
		}

		return self::$provider;
	}

	/**
	 * Forget the resolved provider (used in tests).
	 *
	 * @return void
	 */
	public static function reset(): void {
		self::$provider = null;
	}
}
