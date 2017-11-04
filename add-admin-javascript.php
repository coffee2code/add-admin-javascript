<?php
/**
 * Plugin Name: Add Admin JavaScript
 * Version:     1.6
 * Plugin URI:  http://coffee2code.com/wp-plugins/add-admin-javascript/
 * Author:      Scott Reilly
 * Author URI:  http://coffee2code.com/
 * Text Domain: add-admin-javascript
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Description: Interface for easily defining additional JavaScript (inline and/or by URL) to be added to all administration pages.
 *
 * Compatible with WordPress 4.6+ through 4.8+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/add-admin-javascript/
 *
 * @package Add_Admin_JavaScript
 * @author  Scott Reilly
 * @version 1.6
*/

/*
 * TODO:
 * - Syntax highlighting. (Maybe try http://codemirror.net/).
 */

/*
	Copyright (c) 2010-2018 by Scott Reilly (aka coffee2code)

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

if ( is_admin() && ! class_exists( 'c2c_AddAdminJavaScript_Plugin_046' ) ) :

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'c2c-plugin.php' );

final class c2c_AddAdminJavaScript extends c2c_AddAdminJavaScript_Plugin_046 {

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
		parent::__construct( '1.6', 'add-admin-javascript', 'c2c', __FILE__, array() );
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
		delete_option( 'c2c_add_admin_javascript' );
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
				'help'             => __( 'List one URL per line.  The reference can be relative to the root of your site, or a full, absolute URL.  These will be listed in the order listed, and appear in the &lt;head&gt; before the JS defined below.', 'add-admin-javascript' ),
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
		add_action( 'admin_enqueue_scripts',      array( $this, 'enqueue_js' ) );
		add_action( 'admin_head',                 array( $this, 'add_js_to_head' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'add_js_to_foot' ) );
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
		if ( $screen_id != $this->options_page ) {
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
	\$files[] = 'http://ajax.googleapis.com/ajax/libs/yui/2.8.1/build/yuiloader/yuiloader-min.js';
	return \$files;
}</code></pre>

HTML;

		$help .= '<p>' . __( 'In addition, the "Admin JavaScript (in head)" and "Admin JavaScript (in footer)" can be filtered via <code>c2c_add_admin_js_head</code> and <code>c2c_add_admin_js_footer</code> respectively.', 'add-admin-javascript' ) . "</p>\n";

		return $help;
	}

	/**
	 * Obtain the jQuery JavaScript, if any.  Needed since it is requested in two
	 * functions so it should be memoizable.
	 */
	public function get_jq() {
		$options = $this->get_options();

		if ( $this->jq === false || empty( $this->jq ) ) {
			$this->jq = trim( apply_filters( 'c2c_add_admin_js_jq', $options['js_jq'] . "\n" ) );
		}

		return $this->jq;
	}

	/**
	 * Enqueues javascript.
	 */
	public function enqueue_js() {
		$options = $this->get_options();

		if ( $this->get_jq() != '' ) {
			wp_enqueue_script( 'jquery' );
		}

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
				wp_enqueue_script( $this->id_base . sanitize_key( $file ), $file, array(), $version, true );
			}
		}
	}

	/**
	 * Outputs JavaScript as header links and/or inline header code.
	 */
	public function add_js_to_head() {
		$options = $this->get_options();

		$js = trim( apply_filters( 'c2c_add_admin_js_head', $options['js_head'] . "\n" ) );
		if ( ! empty( $js ) ) {
			echo "
			<script type='text/javascript'>
			$js
			</script>
			";
		}
	}

	/**
	 * Outputs JavaScript into footer as regular JavaScript and/or within a jQuery ready
	 *
	 * @return void (Text will be echoed.)
	 */
	public function add_js_to_foot() {
		$options = $this->get_options();

		$js = trim( apply_filters( 'c2c_add_admin_js_footer', $options['js_foot'] . "\n" ) );
		if ( ! empty( $js ) ) {
			echo "
			<script type='text/javascript'>
			$js
			</script>
			";
		}

		$js = $this->get_jq();
		if ( ! empty( $js ) ) {
			echo "
			<script type='text/javascript'>
				jQuery(document).ready(function($) {
					$js
				});
			</script>
			";
		}
	}

} // end c2c_AddAdminJavaScript

c2c_AddAdminJavaScript::instance();

endif; // end if !class_exists()
