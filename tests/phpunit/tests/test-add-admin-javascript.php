<?php

defined( 'ABSPATH' ) or die();

class Add_Admin_JavaScript_Test extends WP_UnitTestCase {

	protected $obj;

	public function setUp() {
		parent::setUp();

		$this->obj = c2c_AddAdminJavaScript::instance();
	}

	public function tearDown() {
		parent::tearDown();

		unset( $GLOBALS['wp_scripts'] );
		$GLOBALS['wp_scripts'] = new WP_Scripts;

		if ( class_exists( 'c2c_AddAdminJavaScript' ) ) {
			$this->obj->reset();
			unset( $_GET[ c2c_AddAdminJavaScript::NO_JS_QUERY_PARAM ] );
		}

		wp_dequeue_script( 'jquery' );
		wp_dequeue_script( 'code-editor' );
		foreach ( self::add_js_files( array() ) as $file ) {
			$key = 'add-admin-javascript' . sanitize_key( $file );
			wp_dequeue_script( $key );
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

	public static function get_default_hooks() {
		return array(
			array( 'action', 'admin_enqueue_scripts',      'enqueue_js' ),
			array( 'action', 'admin_enqueue_scripts',      'add_codemirror' ),
			array( 'action', 'admin_head',                 'add_js_to_head' ),
			array( 'action', 'admin_notices',              'recovery_mode_notice' ),
			array( 'action', 'admin_print_footer_scripts', 'add_js_to_foot' ),
			array( 'filter', 'wp_redirect',                'remove_query_param_from_redirects' ),
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
		if ( 'wp_head' === $action ) {
			// This enqueues a script that doesn't exist in the develop.svn repo.
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		}

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
		$obj = $this->obj;

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

	protected function fake_current_screen( $screen_id = 'hacky' ) {
		$this->test_turn_on_admin();
		set_current_screen( $screen_id );
		$this->obj->options_page = $screen_id;
		return $screen_id;
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

	public function test_class_name() {
		$this->assertTrue( class_exists( 'c2c_AddAdminJavaScript' ) );
	}

	public function test_plugin_framework_class_name() {
		$this->assertTrue( class_exists( 'c2c_Plugin_061' ) );
	}

	public function test_plugin_framework_version() {
		$this->assertEquals( '061', $this->obj->c2c_plugin_version() );
	}

	public function test_version() {
		$this->assertEquals( '2.0', $this->obj->version() );
	}

	public function test_hooks_plugins_loaded() {
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( 'c2c_AddAdminJavaScript', 'instance' ) ) );
	}

	public function test_setting_name() {
		$this->assertEquals( 'c2c_add_admin_javascript', $this->obj::SETTING_NAME );
	}

	public function test_query_param_name() {
		$this->assertEquals( 'c2c-no-js', $this->obj::NO_JS_QUERY_PARAM );
	}

	/**
	 * @dataProvider get_settings_and_defaults
	 */
	public function test_default_settings( $setting ) {
		$options = $this->obj->get_options();

		$this->assertEmpty( $options[ $setting ] );
	}

	/***
	 * ALL ADMIN AREA RELATED TESTS NEED TO FOLLOW THIS FUNCTION
	 *****/

	public function test_turn_on_admin() {
		if ( ! defined( 'WP_ADMIN' ) ) {
			define( 'WP_ADMIN', true );
		}

		require ADD_ADMIN_JAVASCRIPT_PLUGIN_FILE;

		$this->obj->init();
		$this->obj->register_filters();
		$this->obj->enqueue_js();

		$this->assertTrue( is_admin() );
	}

	/**
	 * @dataProvider get_default_hooks
	 */
	public function test_default_hooks( $hook_type, $hook, $function, $priority = 10, $class_method = true ) {
		$this->test_turn_on_admin();

		$callback = $class_method ? array( $this->obj, $function ) : $function;

		$prio = $hook_type === 'action' ?
			has_action( $hook, $callback ) :
			has_filter( $hook, $callback );

		$this->assertNotFalse( $prio );
		if ( $priority ) {
			$this->assertEquals( $priority, $prio );
		}
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

		$this->assertContains( 'http://test.example.org/js/sample.js?ver=' . $this->obj->version(), $this->get_action_output( 'admin_print_footer_scripts' ) );
	}

	public function test_ver_query_arg_added_for_relative_links() {
		$this->set_option();
		$this->test_turn_on_admin();

		$this->assertContains( '/js/site-relative.js?ver=' . $this->obj->version(), $this->get_action_output( 'admin_print_footer_scripts' ) );
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
		$this->obj->add_js_to_head();
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
		$this->obj->add_js_to_foot();
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
		$this->obj->add_js_to_foot();
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

	/*
	 * remove_query_param_from_redirects()
	 */

	public function test_remove_query_param_from_redirects() {
		$url = 'https://example.com/wp-admin/options-general.php?page=add-admin-javascript%2Fadd-admin.javascript.php';

		$this->assertEquals(
			$url,
			$this->obj->remove_query_param_from_redirects( $url . '&' . c2c_AddAdminJavaScript::NO_JS_QUERY_PARAM . '=1' )
		);
	}

	/*
	 * can_show_js()
	 */

	public function test_can_show_js() {
		$this->test_turn_on_admin();

		$this->assertTrue( $this->obj->can_show_js() );

		$_GET[ c2c_AddAdminJavaScript::NO_JS_QUERY_PARAM ] = '0';

		$this->assertTrue( $this->obj->can_show_js() );
	}

	public function test_can_show_js_with_true_query_param() {
		$this->test_turn_on_admin();

		$_GET[ c2c_AddAdminJavaScript::NO_JS_QUERY_PARAM ] = '1';

		$this->assertFalse( $this->obj->can_show_js() );
	}

	/*
	 * Recovery mode
	 */

	public function test_recovery_mode_via_query_param_disables_add_js_to_head() {
		$this->test_can_show_js_with_true_query_param();

		$out = $this->test_add_js_to_head( '' );

		$this->assertEmpty( $out );
	}

	public function test_recovery_mode_via_query_param_disables_add_js_to_foot() {
		$this->test_can_show_js_with_true_query_param();

		$out = $this->test_add_js_to_foot( '' );

		$this->assertEmpty( $out );
	}

	public function test_recovery_mode_via_query_param_add_jq_js_to_foot() {
		$this->test_can_show_js_with_true_query_param();

		$out = $this->test_add_jq_js_to_foot( '' );

		$this->assertEmpty( $out );
	}

	public function test_recovery_mode_notice_when_js_not_disabled() {
		$this->fake_current_screen();

		$this->assertEmpty( $this->get_action_output( 'admin_notices' ) );
	}

	public function test_recovery_mode_notice_when_js_disabled_by_query_param() {
		$this->fake_current_screen();

		$this->test_can_show_js_with_true_query_param();

		$expected = "				<div class=\"notice notice-error\">
					<p><strong>RECOVERY MODE ENABLED:</strong> JavaScript output for this plugin is disabled on this page view.</p>
				</div>";

		$this->assertEquals( $expected, $this->get_action_output( 'admin_notices' ) );
	}

	/*
	 * options_page_description()
	 */

	public function test_options_page_description() {
		$expected = '<h1>Add Admin JavaScript Settings</h1>
<p class="see-help">See the "Help" link to the top-right of the page for more help.</p>
<p>Add additional JavaScript to your admin pages.</p><p>See the "Advanced Tips" tab in the "Help" section above for info on how to use the plugin to programmatically customize JavaScript.</p>';

		$this->expectOutputRegex( '~^' . preg_quote( $expected ) . '$~', $this->obj->options_page_description() );
	}

	/*
	 * enqueue_admin_js()
	 */

	public function test_enqueue_admin_js() {
		$key = 'add-admin-javascript-' . sanitize_key( 'root-relative.js' );

		$this->assertFalse( wp_script_is( $key, 'registered' ) );
		$this->assertFalse( wp_script_is( $key, 'enqueued' ) );

		$this->set_option();
		$this->test_turn_on_admin();
		$this->obj->enqueue_js();

		$this->assertTrue( wp_script_is( $key, 'registered' ) );
		$this->assertTrue( wp_script_is( $key, 'enqueued' ) );
	}

	public function test_enqueue_admin_js_with_jquery() {
		$key = 'jquery';

		$this->assertTrue( wp_script_is( $key, 'registered' ) );
		$this->assertFalse( wp_script_is( $key, 'enqueued' ) );

		$this->set_option( array( 'js_foot' => '', 'js_jq' => "$('.hide_me').hide();" ) );
		$this->test_turn_on_admin();
		$this->obj->enqueue_js();

		$this->assertTrue( wp_script_is( $key, 'registered' ) );
		$this->assertTrue( wp_script_is( $key, 'enqueued' ) );
	}

	/*
	 * add_codemirror()
	 */

	public function test_add_codemirror() {
		$key = 'code-editor';

		$this->assertTrue( wp_script_is( $key, 'registered' ) );
		$this->assertFalse( wp_script_is( $key, 'enqueued' ) );

		$this->set_option();
		$this->test_turn_on_admin();
		$this->obj->add_codemirror();

		$this->assertTrue( wp_script_is( $key, 'registered' ) );
		$this->assertTrue( wp_script_is( $key, 'enqueued' ) );
	}

	public function test_add_codemirror_adds_scripts() {
		$this->fake_current_screen();
		$this->obj->add_codemirror();

		$expected = 'jQuery\.extend\( wp\.codeEditor\.defaultSettings, {"codemirror":.+wp\.codeEditor\.initialize\( "js_head".+wp\.codeEditor\.initialize\( "js_foot".+wp.codeEditor\.initialize\( "js_jq".+\);';

		$this->expectOutputRegex( '~' . $expected . '~ims', wp_scripts()->print_inline_script( 'code-editor' ) );
	}

	public function test_add_codemirror_does_not_add_scripts_on_other_screen_ids() {
		$this->fake_current_screen();
		set_current_screen( 'another' );
		$this->obj->add_codemirror();

		$inline_script = wp_scripts()->print_inline_script( 'code-editor', 'after', false );

		$this->assertEmpty( $inline_script );
	}

	public function test_add_codemirror_does_not_add_scripts_if_code_editor_not_registered() {
		$this->fake_current_screen();
		wp_deregister_script( 'code-editor' );
		$this->obj->add_codemirror();

		$inline_script = wp_scripts()->print_inline_script( 'code-editor', 'after', false );

		$this->assertEmpty( $inline_script );
	}

	/****************************************
	 * NOTE: Anything beyond this point will run with the
	 * C2C_ADD_ADMIN_JAVASCRIPT_DISABLED define and true.
	 ****************************************/

	public function test_can_show_js_with_true_constant() {
		$this->test_turn_on_admin();

		define( 'C2C_ADD_ADMIN_JAVASCRIPT_DISABLED', true );

		$this->assertFalse( $this->obj->can_show_js() );
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

	public function test_recovery_mode_notice_when_js_disabled_by_constant() {
		$this->fake_current_screen();

		$expected = "				<div class=\"notice notice-error\">
					<p><strong>RECOVERY MODE ENABLED:</strong> JavaScript output for this plugin is currently disabled for the entire admin area via use of the <code>C2C_ADD_ADMIN_JAVASCRIPT_DISABLED</code> constant.</p>
				</div>";

		$this->assertEquals( $expected, $this->get_action_output( 'admin_notices' ) );
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
		$options     = $this->obj->get_options();

		$this->assertFalse( get_option( $option_name ) );
	}
	*/

	public function test_uninstall_deletes_option() {
		$option_name = c2c_AddAdminJavaScript::SETTING_NAME;
		$options     = $this->obj->get_options();

		// Explicitly set an option to ensure options get saved to the database.
		$this->set_option( array( 'js_head' => 'alert("Hi");' ) );

		$this->assertNotEmpty( $options );
		$this->assertNotFalse( get_option( $option_name ) );

		c2c_AddAdminJavaScript::uninstall();

		$this->assertFalse( get_option( $option_name ) );
	}

}
