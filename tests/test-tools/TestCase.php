<?php
/**
 * Base test case for Simple Local Avatars.
 *
 * @package Simple_Local_Avatars
 * @author  10up
 */

namespace Tenup\SimpleLocalAvatars;

class TestCase extends \WP_Mock\Tools\TestCase {

	protected $testFiles = array();

	public function run( \PHPUnit_Framework_TestResult $result = null ) {
		$this->setPreserveGlobalState( false );
		return parent::run( $result );
	}

	public function setUp() {
		if ( ! empty( $this->testFiles ) ) {
			foreach ( $this->testFiles as $file ) {
				if ( file_exists( PROJECT . $file ) ) {
					require_once( PROJECT . $file );
				}
			}
		}
		parent::setUp();
	}

	public function assertActionsCalled() {
		$actions_not_added = $expected_actions = 0;
		try {
			WP_Mock::assertActionsCalled();
		} catch ( \Exception $e ) {
			$actions_not_added = 1;
			$expected_actions  = $e->getMessage();
		}
		$this->assertEmpty( $actions_not_added, $expected_actions );
	}

	public function ns( $function ) {
		if ( ! is_string( $function ) || false !== strpos( $function, '\\' ) ) {
			return $function;
		}
		$thisClassName = trim( get_class( $this ), '\\' );
		if ( ! strpos( $thisClassName, '\\' ) ) {
			return $function;
		}
		// $thisNamespace is constructed by exploding the current class name on
		// namespace separators, running array_slice on that array starting at 0
		// and ending one element from the end (chops the class name off) and
		// imploding that using namespace separators as the glue.
		$thisNamespace = implode( '\\', array_slice( explode( '\\', $thisClassName ), 0, - 1 ) );
		return "$thisNamespace\\$function";
	}

	/**
	 * Define constants after requires/includes
	 *
	 * See http://kpayne.me/2012/07/02/phpunit-process-isolation-and-constant-already-defined/
	 * for more details
	 *
	 * @param \Text_Template $template
	 */
	public function prepareTemplate( \Text_Template $template ) {
		$template->setVar( [
			'globals' => '$GLOBALS[\'__PHPUNIT_BOOTSTRAP\'] = \'' . $GLOBALS['__PHPUNIT_BOOTSTRAP'] . '\';',
		] );
		parent::prepareTemplate( $template );
	}

	/**
	 * Clean up the global query object if necessary.
	 */
	protected function cleanGlobals() {
		if ( class_exists( 'WP_Query' ) && property_exists( 'WP_Query', '__posts' ) ) {
			\WP_Query::$__posts    = \WP_Query::$__data = array();
			\WP_Query::$__instance = null;
		}
		parent::cleanGlobals();
	}
}