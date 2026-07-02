<?php
/**
 * Parameter target type enum.
 *
 * @package EventLayer
 */

namespace EventLayer\Model;

/**
 * How an event parameter's value is resolved on the frontend.
 *
 * @since 1.0.0
 */
enum ParameterTargetType: string {

	case StaticValue      = 'static';
	case ElementText      = 'element_text';
	case ElementAttribute = 'element_attribute';
	case UrlParameter     = 'url_parameter';

	/**
	 * Build from a stored meta value, defaulting to StaticValue.
	 *
	 * @param string $value Raw meta value.
	 * @return self
	 */
	public static function from_meta( string $value ): self {
		return self::tryFrom( $value ) ?? self::StaticValue;
	}
}
