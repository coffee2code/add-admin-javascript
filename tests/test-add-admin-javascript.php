<?php

defined( 'ABSPATH' ) or die();

class Add_Admin_JavaScript_Test extends WP_UnitTestCase {

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
			unset( $_GET[ c2c_AddAdminJavaScript::NO_JS_QUERY_PARAM ] );
		}

	}


	//
	//
	// DATA PROVIDERS
	//
	//


	public static function get_settings_and_defaults() {
		return array(
			array( 'files' ),
			array( 'js_head' ),
			array( 'js_foot' ),
			array( 'js_jq' ),
		);
	}

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

	// Use true for $settings for force use of defaults.
	public function set_option( $settings = true ) {
		$obj = c2c_AddAdminJavaScript::instance();

		if ( true === $settings ) {
			$defaults = array(
				'files' => array(
					'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome.min.js?ver=4.4.0',
					'http://test.example.org/js/sample.js',
					'/js/site-relative.js',
					'root-relative.js',
				),
				'js_head' => $this->add_js( 'this.head.test();', 'settinghead' ),
				'js_foot' => $this->add_js( 'this.foot.test();', 'settingfooter' ),
				'js_jq'   => $this->add_js( '$(".jq").test();', 'settingjq' ),
			);
		} else {
			$defaults = $obj->get_options();
		}

		$settings = wp_parse_args( (array) $settings, $defaults );
		$obj->update_option( $settings, true );
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
		$this->assertTrue( class_exists( 'c2c_AddAdminJavaScript_Plugin_049' ) );
	}

	public function test_plugin_framework_version() {
		$this->assertEquals( '049', c2c_AddAdminJavaScript::instance()->c2c_plugin_version() );
	}

	public function test_version() {
		$this->assertEquals( '1.7', c2c_AddAdminJavaScript::instance()->version() );
	}

	/**
	 * @dataProvider get_settings_and_defaults
	 */
	public function test_default_settings( $setting ) {
		$options = c2c_AddAdminJavaScript::instance()->get_options();

		$this->assertEmpty( $options[ $setting ] );
	}

	/**
	 * @dataProvider get_js_file_links
	 */
	public function test_js_files_are_added_to_admin_footer( $link ) {
		$this->set_option();
		$this->test_turn_on_admin();

		$this->assertContains( $link, $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_ver_query_arg_added_for_links() {
		$this->set_option();
		$this->test_turn_on_admin();

		$this->assertContains( 'http://test.example.org/js/sample.js?ver=' . c2c_AddAdminJavaScript::instance()->version(), $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_ver_query_arg_added_for_relative_links() {
		$this->set_option();
		$this->test_turn_on_admin();

		$this->assertContains( '/js/site-relative.js?ver=' . c2c_AddAdminJavaScript::instance()->version(), $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_ver_query_arg_not_added_if_link_already_has_it() {
		$this->set_option();
		$this->test_turn_on_admin();

		$this->assertContains( "'https://maxcdn.example.com/font-awesome/4.4.0/css/font-awesome.min.js?ver=4.4.0'", $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	/**
	 * @dataProvider get_js_file_links2
	 */
	public function test_js_files_added_via_filter_are_added_to_admin_footer( $link ) {
		$this->set_option();
		add_filter( 'c2c_add_admin_js_files', array( $this, 'add_js_files' ) );

		$this->test_turn_on_admin();

		$this->assertContains( $link, $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_js_head_is_added_to_admin_head() {
		$this->set_option();
		$this->test_turn_on_admin();

		$this->assertContains( $this->add_js( 'this.head.test();', 'settinghead' ), $this->get_action_output( 'admin_head' ) );
	}

	public function test_js_head_added_via_filter_is_added_to_admin_head() {
		$this->set_option();
		add_filter( 'c2c_add_admin_js_head', array( $this, 'add_js' ) );

		$this->test_turn_on_admin();

		$this->assertContains( $this->add_js( '' ), $this->get_action_output( 'admin_head' ) );
	}

	public function test_js_footer_is_added_to_admin_footer() {
		$this->set_option();
		$this->test_turn_on_admin();

		$this->assertContains( $this->add_js( 'this.foot.test();', 'settingfooter' ), $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_js_footer_added_via_filter_is_added_to_admin_footer() {
		$this->set_option();
		$this->test_turn_on_admin();

		add_filter( 'c2c_add_admin_js_footer', array( $this, 'add_js_footer' ) );

		$this->assertContains( $this->add_js_footer(), $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_add_js_to_head( $expected = false ) {
		$js = $this->add_js( 'this.head.test();', 'settingfooter' );

		$this->set_option( array( 'js_head' => $js ) );
		$this->test_turn_on_admin();

		ob_start();
		c2c_AddAdminJavaScript::instance()->add_js_to_head( $js );
		$out = ob_get_contents();
		ob_end_clean();

		if ( false === $expected ) {
			$expected = "
			<script>
			{$js}
			</script>
			";
		}

		$this->assertEquals( $expected, $out );

		return $out;
	}

	public function test_add_js_to_foot( $expected = false ) {
		$js = $this->add_js( 'this.foot.test();', 'settingfooter' );

		$this->set_option( array( 'js_foot' => $js, 'js_jq' => '' ) );
		$this->test_turn_on_admin();

		ob_start();
		c2c_AddAdminJavaScript::instance()->add_js_to_foot( $js );
		$out = ob_get_contents();
		ob_end_clean();

		if ( false === $expected ) {
			$expected = "
			<script>
			{$js}
			</script>
			";
		}

		$this->assertEquals( $expected, $out );

		return $out;
	}

	public function test_add_jq_js_to_foot( $expected = false ) {
		$js = "$('.hide_me').hide();";

		$this->set_option( array( 'js_foot' => '', 'js_jq' => $js ) );
		$this->test_turn_on_admin();

		ob_start();
		c2c_AddAdminJavaScript::instance()->add_js_to_foot( $js );
		$out = ob_get_contents();
		ob_end_clean();

		if ( false === $expected ) {
			$expected = "
			<script>
				jQuery(document).ready(function($) {
					{$js}
				});
			</script>
			";
		}

		$this->assertEquals( $expected, $out );

		return $out;
	}

	public function test_js_jq_is_added_to_admin_footer() {
		$this->set_option();
		$this->test_turn_on_admin();

		$this->assertContains( $this->add_js( '$(".jq").test();', 'settingjq' ), $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_js_jq_added_via_filter_is_added_to_admin_footer() {
		$this->set_option();
		add_filter( 'c2c_add_admin_js_jq', array( $this, 'add_js_jq' ) );

		$this->test_turn_on_admin();

		$this->assertContains( $this->add_js_jq(), $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_remove_query_param_from_redirects() {
		$url = 'https://example.com/wp-admin/options-general.php?page=add-admin-javascript%2Fadd-admin.javascript.php';

		$this->assertEquals(
			$url,
			c2c_AddAdminJavaScript::instance()->remove_query_param_from_redirects( $url . '&' . c2c_AddAdminJavaScript::NO_JS_QUERY_PARAM . '=1' )
		);
	}

	public function test_can_show_js() {
		$this->assertTrue( c2c_AddAdminJavaScript::instance()->can_show_js() );
	}

	public function test_can_show_js_with_false_query_param() {
		$_GET[ c2c_AddAdminJavaScript::NO_JS_QUERY_PARAM ] = '1';

		$this->assertFalse( c2c_AddAdminJavaScript::instance()->can_show_js() );
	}

	public function test_recovery_mode_via_query_param_disables_add_js_to_head() {
		$this->test_can_show_js_with_false_query_param();

		$out = $this->test_add_js_to_head( '' );

		$this->assertEmpty( $out );
	}

	public function test_recovery_mode_via_query_param_disables_add_js_to_foot() {
		$this->test_can_show_js_with_false_query_param();

		$out = $this->test_add_js_to_foot( '' );

		$this->assertEmpty( $out );
	}

	public function test_recovery_mode_via_query_param_add_jq_js_to_foot() {
		$this->test_can_show_js_with_false_query_param();

		$out = $this->test_add_jq_js_to_foot( '' );

		$this->assertEmpty( $out );
	}

	/****************************************
	 * NOTE: Anything beyond this point will run with the
	 * C2C_ADD_ADMIN_JAVASCRIPT_DISABLED define and true.
	 ****************************************/

	public function test_can_show_js_with_true_constant() {
		define( 'C2C_ADD_ADMIN_JAVASCRIPT_DISABLED', true );

		$this->assertFalse( c2c_AddAdminJavaScript::instance()->can_show_js() );
	}

	public function test_recovery_mode_via_constant_disables_add_js_to_head() {
		$out = $this->test_add_js_to_head( '' );

		$this->assertEmpty( $out );
	}

	public function test_recovery_mode_via_constant_disables_add_js_to_foot() {
		$out = $this->test_add_js_to_foot( '' );

		$this->assertEmpty( $out );
	}

	public function test_recovery_mode_via_constant_add_jq_js_to_foot() {
		$out = $this->test_add_jq_js_to_foot( '' );

		$this->assertEmpty( $out );
	}

	/*
	 * Setting handling
	 */

	/*
	// This is normally the case, but the unit tests save the setting to db via
	// setUp(), so until the unit tests are restructured somewhat, this test
	// would fail.
	public function test_does_not_immediately_store_default_settings_in_db() {
		$option_name = c2c_AddAdminJavaScript::SETTING_NAME;
		// Get the options just to see if they may get saved.
		$options     = c2c_AddAdminJavaScript::instance()->get_options();

		$this->assertFalse( get_option( $option_name ) );
	}
	*/

	public function test_uninstall_deletes_option() {
		$option_name = c2c_AddAdminJavaScript::SETTING_NAME;
		$options     = c2c_AddAdminJavaScript::instance()->get_options();

		// Explicitly set an option to ensure options get saved to the database.
		$this->set_option( array( 'js_head' => 'alert("Hi");' ) );

		$this->assertNotEmpty( $options );
		$this->assertNotFalse( get_option( $option_name ) );

		c2c_AddAdminJavaScript::uninstall();

		$this->assertFalse( get_option( $option_name ) );
	}

}
