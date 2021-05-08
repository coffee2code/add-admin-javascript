<?php
/**
 * Plugin Name: Add Admin JavaScript
 * Version:     2.0
 * Plugin URI:  https://coffee2code.com/wp-plugins/add-admin-javascript/
 * Author:      Scott Reilly
 * Author URI:  https://coffee2code.com/
 * Text Domain: add-admin-javascript
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Description: Interface for easily defining additional JavaScript (inline and/or by URL) to be added to all administration pages.
 *
 * Compatible with WordPress 4.9+ through 5.7+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/add-admin-javascript/
 *
 * @package Add_Admin_JavaScript
 * @author  Scott Reilly
 * @version 2.0
*/

/*
	Copyright (c) 2010-2021 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_Plugin_061' ) ) :

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'c2c-plugin.php' );

final class c2c_AddAdminJavaScript extends c2c_Plugin_061 {

	/**
	 * Name of plugin's setting.
	 *
	 * @since 1.7
	 * @var string
	 */
	const SETTING_NAME = 'c2c_add_admin_javascript';

	/**
	 * Name of query parameter for disabling JS output.
	 *
	 * @since 1.7
	 * @var string
	 */
	const NO_JS_QUERY_PARAM = 'c2c-no-js';

	/**
	 * The one true instance.
	 *
	 * @var c2c_AddAdminJavaScript
	 */
	private static $instance;

	/**
	 * Holds memoized jQuery code.
	 *
	 * @var string
	 */
	protected $jq = false;

	/**
	 * Get singleton instance.
	 *
	 * @since 1.2
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	protected function __construct() {
		parent::__construct( '2.0', 'add-admin-javascript', 'c2c', __FILE__, array() );
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );

		return self::$instance = $this;
	}

	/**
	 * Resets the object to its initial state.
	 *
	 * @since 1.3
	 */
	public function reset() {
		$this->jq = false;
	}

	/**
	 * Handles activation tasks, such as registering the uninstall hook.
	 *
	 * @since 1.1
	 */
	public static function activation() {
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
	}

	/**
	 * Handles uninstallation tasks, such as deleting plugin options.
	 *
	 * This can be overridden.
	 *
	 * @since 1.1
	 */
	public static function uninstall() {
		delete_option( self::SETTING_NAME );
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 */
	public function load_config() {
		$this->name      = __( 'Add Admin JavaScript', 'add-admin-javascript' );
		$this->menu_name = __( 'Admin JavaScript', 'add-admin-javascript' );

		$this->config = array(
			'files' => array(
				'input'            => 'inline_textarea',
				'default'          => '',
				'datatype'         => 'array',
				'label'            => __( 'Admin JavaScript Files', 'add-admin-javascript' ),
				'help'             => __( 'List one file per line. The reference can be relative to the root of your site or a full, absolute URL. These will be output in the order listed above and appear before the footer JS defined below.', 'add-admin-javascript' ),
				'input_attributes' => 'style="width: 98%; white-space: pre; word-wrap: normal; overflow-x: scroll;" rows="8" cols="40"'
			),
			'js_head' => array(
				'input'            => 'inline_textarea',
				'default'          => '',
				'datatype'         => 'text',
				'label'            => __( 'Admin JavaScript (in head)', 'add-admin-javascript' ),
				'help'             => __( 'Note that the above JavaScript will be added to all admin pages and apply for all admin users. <em>To speed up page load, it is recommended that inline JavaScript be added to the footer instead of the head.</em>', 'add-admin-javascript' ),
				'input_attributes' => 'style="width: 98%; white-space: pre; word-wrap: normal; overflow-x: scroll;" rows="8" cols="40"'
			),
			'js_foot' => array(
				'input'            => 'inline_textarea',
				'default'          => '',
				'datatype'         => 'text',
				'label'            => __( 'Admin JavaScript (in footer)', 'add-admin-javascript' ),
				'help'             => __( 'Note that the above JavaScript will be added to all admin pages and apply for all admin users. <em>To speed up page load, it is recommended that inline JavaScript be added to the footer instead of the head.</em>', 'add-admin-javascript' ),
				'input_attributes' => 'style="width: 98%; white-space: pre; word-wrap: normal; overflow-x: scroll;" rows="8" cols="40"'
			),
			'js_jq' => array(
				'input'            => 'inline_textarea',
				'default'          => '',
				'datatype'         => 'text',
				'label'            => __( 'Admin jQuery JavaScript', 'add-admin-javascript' ),
				'help'             => __( 'This will be put in a <code>jQuery(document).ready(function($)) {}</code> in the footer. Note that the above JavaScript will be added to all admin pages and apply for all admin users.', 'add-admin-javascript' ),
				'input_attributes' => 'style="width: 98%; white-space: pre; word-wrap: normal; overflow-x: scroll;" rows="8" cols="40"'
			)
		);
	}

	/**
	 * Override the plugin framework's register_filters() to register actions and filters.
	 */
	public function register_filters() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_enqueue_scripts',      array( $this, 'enqueue_js' ) );
		add_action( 'admin_enqueue_scripts',      array( $this, 'add_codemirror' ) );
		add_action( 'admin_head',                 array( $this, 'add_js_to_head' ) );
		add_action( 'admin_notices',              array( $this, 'recovery_mode_notice' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'add_js_to_foot' ) );
		add_filter( 'wp_redirect',                array( $this, 'remove_query_param_from_redirects' ) );
	}

	/**
	 * Returns translated strings used by c2c_Plugin parent class.
	 *
	 * @since 2.0
	 *
	 * @param string $string Optional. The string whose translation should be
	 *                       returned, or an empty string to return all strings.
	 *                       Default ''.
	 * @return string|string[] The translated string, or if a string was provided
	 *                         but a translation was not found then the original
	 *                         string, or an array of all strings if $string is ''.
	 */
	public function get_c2c_string( $string = '' ) {
		$strings = array(
			'A value is required for: "%s"'
				/* translators: %s: Label for setting. */
				=> __( 'A value is required for: "%s"', 'add-admin-javascript' ),
			'Click for more help on this plugin'
				=> __( 'Click for more help on this plugin', 'add-admin-javascript' ),
			' (especially check out the "Other Notes" tab, if present)'
				=> __( ' (especially check out the "Other Notes" tab, if present)', 'add-admin-javascript' ),
			'Coffee fuels my coding.'
				=> __( 'Coffee fuels my coding.', 'add-admin-javascript' ),
			'Did you find this plugin useful?'
				=> __( 'Did you find this plugin useful?', 'add-admin-javascript' ),
			'Donate'
				=> __( 'Donate', 'add-admin-javascript' ),
			'Expected integer value for: %s'
				=> __( 'Expected integer value for: %s', 'add-admin-javascript' ),
			'Invalid file specified for C2C_Plugin: %s'
				/* translators: %s: Path to the plugin file. */
				=> __( 'Invalid file specified for C2C_Plugin: %s', 'add-admin-javascript' ),
			'More information about %1$s %2$s'
				/* translators: 1: plugin name 2: plugin version */
				=> __( 'More information about %1$s %2$s', 'add-admin-javascript' ),
			'More Help'
				=> __( 'More Help', 'add-admin-javascript' ),
			'More Plugin Help'
				=> __( 'More Plugin Help', 'add-admin-javascript' ),
			'Please consider a donation'
				=> __( 'Please consider a donation', 'add-admin-javascript' ),
			'Reset Settings'
				=> __( 'Reset Settings', 'add-admin-javascript' ),
			'Save Changes'
				=> __( 'Save Changes', 'add-admin-javascript' ),
			'See the "Help" link to the top-right of the page for more help.'
				=> __( 'See the "Help" link to the top-right of the page for more help.', 'add-admin-javascript' ),
			'Settings'
				=> __( 'Settings', 'add-admin-javascript' ),
			'Settings reset.'
				=> __( 'Settings reset.', 'add-admin-javascript' ),
			'Something went wrong.'
				=> __( 'Something went wrong.', 'add-admin-javascript' ),
			'The plugin author homepage.'
				=> __( 'The plugin author homepage.', 'add-admin-javascript' ),
			"The plugin configuration option '%s' must be supplied."
				/* translators: %s: The setting configuration key name. */
				=>__( "The plugin configuration option '%s' must be supplied.", 'add-admin-javascript' ),
			'This plugin brought to you by %s.'
				/* translators: %s: Link to plugin author's homepage. */
				=> __( 'This plugin brought to you by %s.', 'add-admin-javascript' ),
		);

		if ( ! $string ) {
			return array_values( $strings );
		}

		return ! empty( $strings[ $string ] ) ? $strings[ $string ] : $string;
	}

	/**
	 * Outputs the text above the setting form.
	 *
	 * @param string $localized_heading_text (optional) Localized page heading text.
	 */
	public function options_page_description( $localized_heading_text = '' ) {
		parent::options_page_description( __( 'Add Admin JavaScript Settings', 'add-admin-javascript' ) );
		echo '<p>' . __( 'Add additional JavaScript to your admin pages.', 'add-admin-javascript' ) . '</p>';
		echo '<p>' . __( 'See the "Advanced Tips" tab in the "Help" section above for info on how to use the plugin to programmatically customize JavaScript.', 'add-admin-javascript' ) . '</p>';
	}

	/**
	 * Configures help tabs content.
	 *
	 * @since 1.2
	 */
	public function help_tabs_content( $screen ) {
		$screen->add_help_tab( array(
			'id'      => 'c2c-advanced-tips-' . $this->id_base,
			'title'   => __( 'Advanced Tips', 'add-admin-javascript' ),
			'content' => self::contextual_help( '', $this->options_page )
		) );

		parent::help_tabs_content( $screen );
	}

	/**
	 * Outputs advanced tips text.
	 *
	 * @since 1.2
	 *
	 * @param string $contextual_help The default contextual help
	 * @param int $screen_id The screen ID
	 * @param object $screen The screen object (only supplied in WP 3.0)
	 */
	public function contextual_help( $contextual_help, $screen_id, $screen = null ) {
		if ( $screen_id !== $this->options_page ) {
			return $contextual_help;
		}

		$help = '<h3>' . __( 'Advanced Tips', 'add-admin-javascript' ) . '</h3>';

		$help .= '<p>' . __( 'You can also programmatically add to or customize any JavaScript defined in the "Admin JavaScript" field via the <code>c2c_add_admin_js_jq</code> filter, like so:', 'add-admin-javascript' ) . '</p>';

		$help .= <<<HTML
		<pre><code>add_filter( 'c2c_add_admin_js_jq', 'my_admin_js_jq' );
function my_admin_js_jq( \$js ) {
	\$js .= "\$('.hide_me').hide();";
	return \$js;
}</code></pre>

HTML;

		$help .= '<p>' . __( 'You can also programmatically add to or customize any referenced JavaScript files defined in the "Admin JS Files" field via the <code>c2c_add_admin_js_files</code> filter, like so:', 'add-admin-javascript' ) . '</p>';

		$help .= <<<HTML
		<pre><code>add_filter( 'c2c_add_admin_js_files', 'my_admin_js_files' );
function my_admin_js_files( \$files ) {
	\$files[] = 'https://ajax.googleapis.com/ajax/libs/mootools/1.6.0/mootools.min.js';
	return \$files;
}</code></pre>

HTML;

		$help .= '<p>' . __( 'In addition, the "Admin JavaScript (in head)" and "Admin JavaScript (in footer)" can be filtered via <code>c2c_add_admin_js_head</code> and <code>c2c_add_admin_js_footer</code> respectively.', 'add-admin-javascript' ) . "</p>\n";

		return $help;
	}

	/**
	 * Outputs admin notice on plugin's setting page if recovery mode is active.
	 *
	 * @since 1.7
	 */
	public function recovery_mode_notice() {
		if ( get_current_screen()->id === $this->options_page && ! $this->can_show_js() ) {
			if ( defined( 'C2C_ADD_ADMIN_JAVASCRIPT_DISABLED' ) && C2C_ADD_ADMIN_JAVASCRIPT_DISABLED ) {
				$msg = sprintf(
					__( "<strong>RECOVERY MODE ENABLED:</strong> JavaScript output for this plugin is currently disabled for the entire admin area via use of the <code>%s</code> constant.", 'add-admin-javascript' ),
					'C2C_ADD_ADMIN_JAVASCRIPT_DISABLED'
				);
			} else {
				$msg = __( "<strong>RECOVERY MODE ENABLED:</strong> JavaScript output for this plugin is disabled on this page view.", 'add-admin-javascript' );;
			}

			echo <<<HTML
				<div class="notice notice-error">
					<p>{$msg}</p>
				</div>
HTML;
		}
	}

	/**
	 * Obtain the jQuery JavaScript, if any. Needed since it is requested in two
	 * functions so it should be memoizable.
	 */
	public function get_jq() {
		$options = $this->get_options();

		if ( ! $this->jq ) {
			$js_jq = $options['js_jq'];
			if ( $js_jq ) {
				$js_jq .= "\n";
			}

			/**
			 * Filters the JavaScript that should be added directly to the admin page
			 * footer within a jQuery document ready function.
			 *
			 * @since 1.0
			 *
			 * @param string $files JavaScript code (without `<script>` tags or jQuery
			 *                      document ready function).
			 */
			$this->jq = trim( apply_filters( 'c2c_add_admin_js_jq', $js_jq ) );
		}

		return $this->jq;
	}

	/**
	 * Enqueues javascript.
	 */
	public function enqueue_js() {
		if ( ! $this->can_show_js() ) {
			return;
		}

		$options = $this->get_options();

		if ( $this->get_jq() !== '' ) {
			wp_enqueue_script( 'jquery' );
		}

		/**
		 * Filters the list of JavaScript files to enqueue in the admin.
		 *
		 * @since 1.0
		 *
		 * @param array $files Array of JavaScript files.
		 */
		$files = (array) apply_filters( 'c2c_add_admin_js_files', $options['files'] );

		if ( $files ) {
			foreach ( $files as $file ) {
				// Determine a version for the script (the one specified, else the plugin's version).
				$file_parts = parse_url( $file );
				if ( isset( $file_parts['query'] ) ) {
					parse_str( $file_parts['query'], $file_query );
				}
				$version = ( ! empty( $file_query ) && isset( $file_query['ver'] ) ) ? $file_query['ver'] : $this->version;
				unset( $file_query );

				if ( $file && $file[0] !== '/' && false === strpos( $file, '//' ) ) {
					$file = '/' . $file;
				}

				wp_enqueue_script( $this->id_base . '-' . sanitize_key( $file ), $file, array(), $version, true );
			}
		}
	}

	/**
	 * Removes the query parameter to disable JS output from redirect URLs.
	 *
	 * Needed to prevent the query parameter from propagating from page view
	 * through to form submission.
	 *
	 * @since 1.7
	 *
	 * @param string $url The redirect URL.
	 * @return string
	 */
	public function remove_query_param_from_redirects( $url ) {
		if ( is_admin() ) {
			$url = remove_query_arg( self::NO_JS_QUERY_PARAM, $url );
		}

		return $url;
	}

	/**
	 * Determines if JavaScript can be output under current conditions.
	 *
	 * JS will always be output in the admin unless:
	 * - The C2C_ADD_ADMIN_JAVASCRIPT_DISABLED constant is defined and true.
	 * - The 'c2c-no-js' query parameter is present with a value of '1'.
	 *
	 * @since 1.7
	 *
	 * @return bool True if JS can be shown, otherwise false.
	 */
	public function can_show_js() {
		$can_show = true;

		// Recovery mode enabled via constant.
		if ( $can_show && defined( 'C2C_ADD_ADMIN_JAVASCRIPT_DISABLED' ) && C2C_ADD_ADMIN_JAVASCRIPT_DISABLED ) {
			$can_show = false;
		}

		// Recovery mode enabled via query parameter.
		if ( $can_show && isset( $_GET[ self::NO_JS_QUERY_PARAM ] ) && '1' === $_GET[ self::NO_JS_QUERY_PARAM ] ) {
			$can_show = false;
		}

		return $can_show;
	}

	/**
	 * Outputs JavaScript as header links and/or inline header code.
	 */
	public function add_js_to_head() {
		if ( ! $this->can_show_js() ) {
			return;
		}

		$options = $this->get_options();

		$js_head = $options['js_head'];
		if ( $js_head ) {
			$js_head .= "\n";
		}

		/**
		 * Filters the JavaScript that should be added directly to the admin page head.
		 *
		 * @since 1.0
		 *
		 * @param string $files JavaScript code (without `<script>` tags).
		 */
		$js = trim( apply_filters( 'c2c_add_admin_js_head', $js_head ) );

		if ( $js ) {
			echo "
			<script>
			$js
			</script>
			";
		}
	}

	/**
	 * Outputs JavaScript into footer as regular JavaScript and/or within a jQuery ready.
	 */
	public function add_js_to_foot() {
		if ( ! $this->can_show_js() ) {
			return;
		}

		$options = $this->get_options();

		$js_foot = $options['js_foot'];
		if ( $js_foot ) {
			$js_foot .= "\n";
		}

		/**
		 * Filters the JavaScript that should be added directly to the admin footer.
		 *
		 * @since 1.0
		 *
		 * @param string $files JavaScript code (without `<script>` tags).
		 */
		$js = trim( apply_filters( 'c2c_add_admin_js_footer', $js_foot ) );

		if ( $js ) {
			echo "
			<script>
			$js
			</script>
			";
		}

		$js = $this->get_jq();
		if ( $js ) {
			echo "
			<script>
				jQuery(document).ready(function($) {
					$js
				});
			</script>
			";
		}
	}

	/**
	 * Initializes CodeMirror for the JavaScript textareas.
	 *
	 * @since 1.7
	 */
	public function add_codemirror() {
		// Bail if not on the plugin setting page.
		$current_screen = get_current_screen();
		if ( ! $current_screen || $this->options_page !== $current_screen->id ) {
			return;
		}

		// Bail if the code editor script hasn't been registered.
		if ( ! wp_script_is( 'code-editor', 'registered' ) ) {
			return;
		}

		// Enqueue code editor and settings for manipulating JS.
		$settings = wp_enqueue_code_editor( array( 'type' => 'text/javascript' ) );

		// Bail if user disabled CodeMirror.
		if ( false === $settings ) {
			return;
		}

		// Inline the CodeMirror code.
		$json_settings = wp_json_encode( $settings );
		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery( function() {
					wp.codeEditor.initialize( "js_head", %s );
					wp.codeEditor.initialize( "js_foot", %s );
					wp.codeEditor.initialize( "js_jq", %s );
				} );',
				$json_settings,
				$json_settings,
				$json_settings
			)
		);
	}

} // end c2c_AddAdminJavaScript

add_action( 'plugins_loaded', array( 'c2c_AddAdminJavaScript', 'instance' ) );

endif; // end if !class_exists()
