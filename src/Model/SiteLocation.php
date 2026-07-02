<?php
/**
 * Site location enum for event rules.
 *
 * @package EventLayer
 */

namespace EventLayer\Model;

/**
 * Where an event rule is active on the site.
 *
 * @since 1.0.0
 */
enum SiteLocation: string {

	case AllPages      = 'all_pages';
	case Homepage      = 'homepage';
	case SpecificPages = 'specific_pages';

	/**
	 * Build from a stored meta value, defaulting to AllPages.
	 *
	 * @param string $value Raw meta value.
	 * @return self
	 */
	public static function from_meta( string $value ): self {
		return self::tryFrom( $value ) ?? self::AllPages;
	}
}
