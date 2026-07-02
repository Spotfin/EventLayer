<?php
/**
 * License provider contract.
 *
 * @package EventLayer
 */

namespace EventLayer\Gating;

/**
 * Contract for license validation.
 *
 * The free core ships no implementation; EventLayer Pro provides one and
 * uses it to back its GatingProvider.
 *
 * @since 1.0.0
 */
interface LicenseProvider {

	/**
	 * Whether a valid license is active for this site.
	 *
	 * @return bool
	 */
	public function is_active(): bool;

	/**
	 * The stored license key ('' when none).
	 *
	 * @return string
	 */
	public function get_license_key(): string;
}
