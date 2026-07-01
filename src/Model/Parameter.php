<?php
/**
 * Event rule parameter value object.
 *
 * @package EventLayer
 */

namespace EventLayer\Model;

/**
 * A single dataLayer parameter attached to an event rule.
 *
 * @since 1.0.0
 */
final class Parameter {

	/**
	 * Constructor.
	 *
	 * @param string              $name            Parameter name (never empty).
	 * @param string              $default_value   Fallback value.
	 * @param ParameterTargetType $target_type     How the value is resolved.
	 * @param string              $target_selector CSS selector, attribute, or query-arg name.
	 */
	public function __construct(
		public readonly string $name,
		public readonly string $default_value,
		public readonly ParameterTargetType $target_type,
		public readonly string $target_selector,
	) {
	}

	/**
	 * Build from a stored meta row. Returns null when the name is empty.
	 *
	 * @param mixed $data Raw parameter row from post meta.
	 * @return self|null
	 */
	public static function from_meta( mixed $data ): ?self {
		if ( ! is_array( $data ) || empty( $data['name'] ) ) {
			return null;
		}

		return new self(
			sanitize_text_field( $data['name'] ),
			sanitize_text_field( $data['default_value'] ?? '' ),
			ParameterTargetType::from_meta( sanitize_text_field( $data['target_type'] ?? 'static' ) ),
			sanitize_text_field( $data['target_selector'] ?? '' ),
		);
	}

	/**
	 * Convert to the legacy snake_case array shape stored in post meta.
	 *
	 * @return array
	 */
	public function to_meta(): array {
		return array(
			'name'            => $this->name,
			'default_value'   => $this->default_value,
			'target_type'     => $this->target_type->value,
			'target_selector' => $this->target_selector,
		);
	}
}
