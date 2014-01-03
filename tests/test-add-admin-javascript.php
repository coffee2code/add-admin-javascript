<?php

class Add_Admin_JavaScript_Test extends WP_UnitTestCase {

	private $option_name = 'c2c_add_admin_javascript';

	function setUp() {
		parent::setUp();
		$this->set_option();
	}

	function tearDown() {
		parent::tearDown();
		remove_filter( 'c2c_add_admin_js_files', array( $this, 'add_js_files' ) );

		unset( $GLOBALS['wp_scripts']);
		$GLOBALS['wp_scripts'] = new WP_Scripts;

		if ( class_exists( 'c2c_AddAdminJavaScript' ) ) {
			c2c_AddAdminJavaScript::instance()->reset();
		}
	}


	/**
	 *
	 * DATA PROVIDERS
	 *
	 */


	public static function get_js_file_links() {
		return array(
			array( 'http://test.example.org/js/sample.js' ),
			array( 'http://example.org/js/site-relative.js' ),
			array( 'http://example.org/root-relative.js' ),
		);
	}

	public static function get_js_file_links2() {
		return array(
			array( 'http://test.example.org/js/sample2.js' ),
			array( 'http://example.org/js/site-relative2.js' ),
			array( 'http://example.org/root-relative2.js' ),
		);
	}


	/**
	 *
	 * HELPER FUNCTIONS
	 *
	 */


	function get_action_output( $action ) {
		ob_start();
		do_action( $action );
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

	function add_js_files( $files ) {
		$files = array();
		$files[] = 'http://test.example.org/js/sample2.js';
		$files[] = '/js/site-relative2.js';
		$files[] = 'root-relative2.js';
		return $files;
	}

	function add_js( $js, $modifier = '', $jq = false ) {
		$more_js = 'this.default.do("' . $modifier . '");';
		return $js . $more_js;
	}

	function add_js_footer( $footer = '' ) {
		return $footer . $this->add_js( '', 'filters' );
	}

	function add_js_jq( $footer = '' ) {
		return $footer . $this->add_js( '', 'filterjq' );
	}

	function set_option() {
		update_option( $this->option_name, array(
			'files' => array(
				'http://test.example.org/js/sample.js',
				'/js/site-relative.js',
				'root-relative.js',
			),
			'js_head' => $this->add_js( 'this.head.test();', 'settinghead' ),
			'js_foot' => $this->add_js( 'this.foot.test();', 'settingfooter' ),
			'js_jq'   => $this->add_js( '$(".jq").test();', 'settingjq' ),
		) );
	}


	/**
	 *
	 * TESTS
	 *
	 */

	function test_js_added_via_filter_not_added_to_wp_head() {
		add_filter( 'c2c_add_admin_js_head', array( $this, 'add_js' ) );
		$head = $this->get_action_output( 'wp_head' );

		$this->assertEmpty( strpos( $head,  $this->add_js( '' ) ) );
	}

	/**
	 * @dataProvider get_js_file_links2
	 */
	function test_js_files_added_via_filter_not_added_to_wp_head( $link ) {
		add_filter( 'c2c_add_admin_js_files', array( $this, 'add_js_files' ) );

		$head = $this->get_action_output( 'wp_head' );

		$this->assertEmpty( intval( strpos( $head, $link ) ) );
	}

	/***
	 * ALL ADMIN AREA RELATED TESTS NEED TO FOLLOW THIS FUNCTION
	 *****/

	function test_turn_on_admin() {
		define( 'WP_ADMIN', true );
		require dirname( __FILE__ ) . '/../add-admin-javascript.php';
		c2c_AddAdminJavaScript::instance()->init();
		c2c_AddAdminJavaScript::instance()->enqueue_js();

		$this->option_name = c2c_AddAdminJavaScript::instance()->admin_options_name;

		$this->assertTrue( is_admin() );
	}


	/**
	 * @dataProvider get_js_file_links
	 */
	function test_js_files_are_added_to_admin_footer( $link ) {
		c2c_AddAdminJavaScript::instance()->enqueue_js();

		$footer = $this->get_action_output( 'admin_print_footer_scripts' );

		$this->assertGreaterThan( 0, intval( strpos( $footer, $link ) ) );
	}

	/**
	 * @dataProvider get_js_file_links2
	 */
	function test_js_files_added_via_filter_are_added_to_admin_footer( $link ) {
		add_filter( 'c2c_add_admin_js_files', array( $this, 'add_js_files' ) );
		c2c_AddAdminJavaScript::instance()->enqueue_js();

		$head = $this->get_action_output( 'admin_print_footer_scripts' );

		$this->assertGreaterThan( 0, intval( strpos( $head, $link ) ) );
	}

	function test_js_head_is_added_to_admin_head() {
		$head = $this->get_action_output( 'admin_head' );

		$this->assertGreaterThan( 0, intval( strpos( $head, $this->add_js( 'this.head.test();', 'settinghead' ) ) ) );
	}

	function test_js_head_added_via_filter_is_added_to_admin_head() {
		add_filter( 'c2c_add_admin_js', array( $this, 'add_js' ) );

		$head = $this->get_action_output( 'admin_head' );

		$this->assertGreaterThan( 0, intval( strpos( $head,  $this->add_js( '' ) ) ) );
	}

	function test_js_footer_is_added_to_admin_footer() {
		$footer = $this->get_action_output( 'admin_print_footer_scripts' );

		$this->assertGreaterThan( 0, intval( strpos( $footer, $this->add_js( 'this.foot.test();', 'settingfooter' ) ) ) );
	}

	function test_js_footer_added_via_filter_is_added_to_admin_footer() {
		add_filter( 'c2c_add_admin_js_footer', array( $this, 'add_js_footer' ) );

		$footer = $this->get_action_output( 'admin_print_footer_scripts' );

		$this->assertGreaterThan( 0, intval( strpos( $footer,  $this->add_js_footer() ) ) );
	}

	function test_js_jq_is_added_to_admin_footer() {
		$footer = $this->get_action_output( 'admin_print_footer_scripts' );

		$this->assertGreaterThan( 0, intval( strpos( $footer, $this->add_js( '$(".jq").test();', 'settingjq' ) ) ) );
	}

	function test_js_jq_added_via_filter_is_added_to_admin_footer() {
		add_filter( 'c2c_add_admin_js_jq', array( $this, 'add_js_jq' ) );

		$footer = $this->get_action_output( 'admin_print_footer_scripts' );

		$this->assertGreaterThan( 0, intval( strpos( $footer,  $this->add_js_jq() ) ) );
	}

}
