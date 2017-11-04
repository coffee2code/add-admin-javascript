<?php

defined( 'ABSPATH' ) or die();

class Add_Admin_JavaScript_Test extends WP_UnitTestCase {

	private $option_name = 'c2c_add_admin_javascript';

	public function setUp() {
		parent::setUp();

		$this->set_option();
	}

	public function tearDown() {
		parent::tearDown();

		remove_filter( 'c2c_add_admin_js',        array( $this, 'add_js' ) );
		remove_filter( 'c2c_add_admin_js_files',  array( $this, 'add_js_files' ) );
		remove_filter( 'c2c_add_admin_js_footer', array( $this, 'add_js_footer' ) );
		remove_filter( 'c2c_add_admin_js_head',   array( $this, 'add_js' ) );
		remove_filter( 'c2c_add_admin_js_jq',     array( $this, 'add_js_jq' ) );

		unset( $GLOBALS['wp_scripts']);
		$GLOBALS['wp_scripts'] = new WP_Scripts;

		if ( class_exists( 'c2c_AddAdminJavaScript' ) ) {
			c2c_AddAdminJavaScript::instance()->reset();
		}
	}


	//
	//
	// DATA PROVIDERS
	//
	//


	public static function get_js_file_links() {
		return array(
			array( 'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome.min.js?ver=4.4.0' ),
			array( 'http://test.example.org/js/sample.js' ),
			array( 'http://example.org/js/site-relative.js' ),
			array( 'http://example.org/root-relative.js' ),
		);
	}

	public static function get_js_file_links2() {
		return array(
			array( 'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome2.min.js?ver=4.4.0' ),
			array( 'http://test.example.org/js/sample2.js' ),
			array( 'http://example.org/js/site-relative2.js' ),
			array( 'http://example.org/root-relative2.js' ),
		);
	}


	//
	//
	// HELPER FUNCTIONS
	//
	//


	public function get_action_output( $action ) {
		ob_start();
		do_action( $action );
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

	public function add_js_files( $files ) {
		$files = array();
		$files[] = 'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome2.min.js?ver=4.4.0' ;
		$files[] = 'http://test.example.org/js/sample2.js';
		$files[] = '/js/site-relative2.js';
		$files[] = 'root-relative2.js';
		return $files;
	}

	public function add_js( $js, $modifier = '', $jq = false ) {
		$more_js = 'this.default.do("' . $modifier . '");';
		return $js . $more_js;
	}

	public function add_js_footer( $footer = '' ) {
		return $footer . $this->add_js( '', 'filters' );
	}

	public function add_js_jq( $footer = '' ) {
		return $footer . $this->add_js( '', 'filterjq' );
	}

	public function set_option() {
		update_option( $this->option_name, array(
			'files' => array(
				'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome.min.js?ver=4.4.0',
				'http://test.example.org/js/sample.js',
				'/js/site-relative.js',
				'root-relative.js',
			),
			'js_head' => $this->add_js( 'this.head.test();', 'settinghead' ),
			'js_foot' => $this->add_js( 'this.foot.test();', 'settingfooter' ),
			'js_jq'   => $this->add_js( '$(".jq").test();', 'settingjq' ),
		) );
	}


	//
	//
	// TESTS
	//
	//

	public function test_js_added_via_filter_not_added_to_wp_head() {
		add_filter( 'c2c_add_admin_js_head', array( $this, 'add_js' ) );

		$this->assertNotContains( $this->add_js( '' ), $this->get_action_output( 'wp_head' ) );
	}

	/**
	 * @dataProvider get_js_file_links2
	 */
	public function test_js_files_added_via_filter_not_added_to_wp_head( $link ) {
		add_filter( 'c2c_add_admin_js_files', array( $this, 'add_js_files' ) );

		$this->assertNotContains( $link, $this->get_action_output( 'wp_head' ) );
	}

	/***
	 * ALL ADMIN AREA RELATED TESTS NEED TO FOLLOW THIS FUNCTION
	 *****/

	public function test_turn_on_admin() {
		if ( ! defined( 'WP_ADMIN' ) ) {
			define( 'WP_ADMIN', true );
		}

		require( dirname( __FILE__ ) . '/../add-admin-javascript.php' );
		c2c_AddAdminJavaScript::instance()->init();
		c2c_AddAdminJavaScript::instance()->register_filters();
		c2c_AddAdminJavaScript::instance()->enqueue_js();

		$this->option_name = c2c_AddAdminJavaScript::instance()->admin_options_name;

		$this->assertTrue( is_admin() );
	}


	public function test_class_name() {
		$this->assertTrue( class_exists( 'c2c_AddAdminJavaScript' ) );
	}

	public function test_plugin_framework_class_name() {
		$this->assertTrue( class_exists( 'c2c_AddAdminJavaScript_Plugin_046' ) );
	}

	public function test_plugin_framework_version() {
		$this->assertEquals( '046', c2c_AddAdminJavaScript::instance()->c2c_plugin_version() );
	}

	public function test_version() {
		$this->assertEquals( '1.6', c2c_AddAdminJavaScript::instance()->version() );
	}

	/**
	 * @dataProvider get_js_file_links
	 */
	public function test_js_files_are_added_to_admin_footer( $link ) {
		$this->test_turn_on_admin();

		$this->assertContains( $link, $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_ver_query_arg_added_for_links() {
		$this->test_turn_on_admin();

		$this->assertContains( 'http://test.example.org/js/sample.js?ver=' . c2c_AddAdminJavaScript::instance()->version(), $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_ver_query_arg_added_for_relative_links() {
		$this->test_turn_on_admin();

		$this->assertContains( '/js/site-relative.js?ver=' . c2c_AddAdminJavaScript::instance()->version(), $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_ver_query_arg_not_added_if_link_already_has_it() {
		$this->test_turn_on_admin();

		$this->assertContains( "'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome.min.js?ver=4.4.0'", $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	/**
	 * @dataProvider get_js_file_links2
	 */
	public function test_js_files_added_via_filter_are_added_to_admin_footer( $link ) {
		add_filter( 'c2c_add_admin_js_files', array( $this, 'add_js_files' ) );

		$this->test_turn_on_admin();

		$this->assertContains( $link, $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_js_head_is_added_to_admin_head() {
		$this->test_turn_on_admin();

		$this->assertContains( $this->add_js( 'this.head.test();', 'settinghead' ), $this->get_action_output( 'admin_head' ) );
	}

	public function test_js_head_added_via_filter_is_added_to_admin_head() {
		add_filter( 'c2c_add_admin_js_head', array( $this, 'add_js' ) );

		$this->test_turn_on_admin();

		$this->assertContains( $this->add_js( '' ), $this->get_action_output( 'admin_head' ) );
	}

	public function test_js_footer_is_added_to_admin_footer() {
		$this->test_turn_on_admin();

		$this->assertContains( $this->add_js( 'this.foot.test();', 'settingfooter' ), $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_js_footer_added_via_filter_is_added_to_admin_footer() {
		$this->test_turn_on_admin();

		add_filter( 'c2c_add_admin_js_footer', array( $this, 'add_js_footer' ) );

		$this->assertContains( $this->add_js_footer(), $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_js_jq_is_added_to_admin_footer() {
		$this->test_turn_on_admin();

		$this->assertContains( $this->add_js( '$(".jq").test();', 'settingjq' ), $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_js_jq_added_via_filter_is_added_to_admin_footer() {
		add_filter( 'c2c_add_admin_js_jq', array( $this, 'add_js_jq' ) );

		$this->test_turn_on_admin();

		$this->assertContains( $this->add_js_jq(), $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_uninstall_deletes_option() {
		$option = 'c2c_add_admin_javascript';
		c2c_AddAdminJavaScript::instance()->get_options();

		$this->assertNotFalse( get_option( $option ) );

		c2c_AddAdminJavaScript::uninstall();

		$this->assertFalse( get_option( $option ) );
	}

}
