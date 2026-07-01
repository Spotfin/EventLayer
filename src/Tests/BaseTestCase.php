<?php

namespace EventLayer\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Base test case for EventLayer tests.
 *
 * @package EventLayer\Tests
 * @since 1.0.0
 */
abstract class BaseTestCase extends TestCase {

	/**
	 * Set up test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		// Test setup code here.
	}

	/**
	 * Tear down test environment.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		parent::tearDown();
		// Test cleanup code here.
	}
}
