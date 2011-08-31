<?php
/**
 * @package Inject_Admin_JS
 * @author Scott Reilly
 * @version 1.0
 */
/*
Plugin Name: Inject Admin JS
Version: 1.0
Plugin URI: http://coffee2code.com/wp-plugins/inject-admin-js/
Author: Scott Reilly
Author URI: http://coffee2code.com
Text Domain: inject-admin-js
Description: Easily define additional JavaScript (inline and/or by URL) to be added to all administration pages.

Compatible with WordPress 2.8+, 2.9+, 3.0+.

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/inject-admin-js/

*/

/*
Copyright (c) 2010 by Scott Reilly (aka coffee2code)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

if ( is_admin() && !class_exists( 'c2c_InjectAdminJS' ) ) :

require_once( 'c2c-plugin.php' );

class c2c_InjectAdminJS extends C2C_Plugin_018 {

	var $jq = false; // To hold memoized jQuery code

	/**
	 * Handles installation tasks, such as ensuring plugin options are instantiated and saved to options table.
	 *
	 * @return void
	 */
	function c2c_InjectAdminJS() {
		$this->C2C_Plugin_018( '1.0', 'inject-admin-js', 'c2c', __FILE__, array() );
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 *
	 * @return void
	 */
	function load_config() {
		$this->name = __( 'Inject Admin JS', $this->textdomain );
		$this->menu_name = __( 'Admin JS', $this->textdomain );

		$this->config = array(
			'files' => array( 'input' => 'inline_textarea', 'default' => '', 'datatype' => 'array',
					'label' => __( 'Admin JS Files', $this->textdomain ),
					'help' => __( 'List one URL per line.  The reference can be relative to the root of your site, or a full, absolute URL.  These will be listed in the order listed, and appear in the &lt;head&gt; before the JS defined below.', $this->textdomain ),
					'input_attributes' => 'rows="8" cols="40"'
			),
			'js_head' => array( 'input' => 'textarea', 'default' => '', 'datatype' => 'text',
					'label' => __( 'Admin JS (in head)', $this->textdomain ),
					'help' => __( 'Note that the above JS will be added to all admin pages and apply for all admin users.', $this->textdomain ),
					'input_attributes' => 'rows="8" cols="40"'
			),
			'js_foot' => array( 'input' => 'textarea', 'default' => '', 'datatype' => 'text',
					'label' => __( 'Admin JS (in footer)', $this->textdomain ),
					'help' => __( 'Note that the above JS will be added to all admin pages and apply for all admin users. <em>To speed up page load, it is recommended that inline JS be added to the footer instead of the head.</em>', $this->textdomain ),
					'input_attributes' => 'rows="8" cols="40"'
			),
			'js_jq' => array( 'input' => 'textarea', 'default' => '', 'datatype' => 'text',
					'label' => __( 'Admin jQuery JS', $this->textdomain ),
					'help' => __( 'This will be put in a <code>jQuery(document).ready(function($)) {}</code> in the footer. Note that the above JS will be added to all admin pages and apply for all admin users.', $this->textdomain ),
					'input_attributes' => 'rows="8" cols="40"'
			)
		);
	}

	/**
	 * Override the plugin framework's register_filters() to register actions and filters.
	 *
	 * @return void
	 */
	function register_filters() {
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_js' ) );
		add_action( 'admin_head', array( &$this, 'add_js_to_head' ) );
		add_action( 'admin_print_footer_scripts', array( &$this, 'add_js_to_foot' ) );
		add_action( $this->get_hook( 'after_settings_form' ), array( &$this, 'advanced_tips' ) );
	}

	/**
	 * Outputs the text above the setting form
	 *
	 * @return void (Text will be echoed.)
	 */
	function options_page_description() {
		parent::options_page_description( __( 'Inject Admin JS Settings', $this->textdomain ) );
		echo '<p>' . __( 'Add additional JavaScript to your admin pages.', $this->textdomain ) . '</p>';
		echo '<p>' . __( 'See <a href="#advanced-tips">Advanced Tips</a> for info on how to use the plugin to programmatically customize JavaScript.' ) . '</p>';
	}

	/*
	 * Outputs advanced tips text
	 *
	 * @return void (Text will be echoed.)
	 */
	function advanced_tips() {
		echo '<a name="advanced-tips"></a>';
		echo '<h2>Advanced Tips</h2>';
		echo '<p>' . __( 'You can also programmatically add to or customize any JavaScript defined in the "Admin JS" field via the <code>inject_admin_js_jq</code> filter, like so:', $this->textdomain ) . '</p>';
		echo <<<HTML
		<pre><code>add_filter( 'inject_admin_js_jq', 'my_admin_js_jq' );
function my_admin_js_jq( \$js ) {
	\$js .= "
		#site-heading a span { color:blue !important; }
		#favorite-actions { display:none; }
	";
	return \$js;
}</code></pre>

HTML;
		echo '<p>' . __( 'You can also programmatically add to or customize any referenced JS files defined in the "Admin JS Files" field via the <code>inject_admin_js_files</code> filter, like so:', $this->textdomain ) . '</p>';
		echo <<<HTML
		<pre><code>add_filter( 'inject_admin_js_files', 'my_admin_js_files' );
function my_admin_js_files( \$files ) {
	\$files[] = 'http://ajax.googleapis.com/ajax/libs/yui/2.8.1/build/yuiloader/yuiloader-min.js';
	return \$files;
}</code></pre>

HTML;
		echo '<p>' . __( 'In addition, the "Admin JS (in head)" and "Admin JS (in footer)" can be filtered via <code>inject_admin_js_head</code> and <code>inject_admin_js_footer</code> respectively.', $this->textdomain ) . "</p>\n";
	}

	/**
	 * Obtain the jQuery JS, if any.  Needed since it is requested in two
	 * functions so it should be memoizable.
	 */
	function get_jq() {
		$options = $this->get_options();
		if ( $this->jq === false || empty( $this->jq ) )
			$this->jq = trim( apply_filters( 'c2c_inject_admin_js_jq', $options['js_jq'] . "\n" ) );
		return $this->jq;
	}

	/**
	 * Enqueues javascript.
	 *
	 * @return void
	 */
	function enqueue_js() {
		$options = $this->get_options();

		if ( $this->get_jq() != '' )
			wp_enqueue_script( 'jquery' );

		$files = (array) apply_filters( 'c2c_inject_admin_js_files', $options['files'] );
		if ( $files ) {
			foreach ( $files as $file )
				wp_enqueue_script( $this->id_base, $file, array(), $this->version, true );
		}
	}

	/**
	 * Outputs JS as header links and/or inline header code
	 *
	 * @return void (Text will be echoed.)
	 */
	function add_js_to_head() {
		$options = $this->get_options();
		$js = trim( apply_filters( 'c2c_inject_admin_js_head', $options['js_head'] . "\n" ) );
		if ( !empty( $js ) ) {
			echo "
			<script type='text/javascript'>
			$js
			</script>
			";
		}
	}

	/**
	 * Outputs JS into footer as regular JS and/or within a jQuery ready
	 *
	 * @return void (Text will be echoed.)
	 */
	function add_js_to_foot() {
		$options = $this->get_options();
		$js = trim( apply_filters( 'c2c_inject_admin_js_footer', $options['js_foot'] . "\n" ) );
		if ( !empty( $js ) ) {
			echo "
			<script type='text/javascript'>
			$js
			</script>
			";
		}
		$js = $this->get_jq();
		if ( !empty( $js ) ) {
			echo "
			<script type='text/javascript'>
				jQuery(document).ready(function($) {
					$js
				});
			</script>
			";
		}
	}

} // end c2c_InjectAdminJS

$GLOBALS['c2c_inject_admin_js'] = new c2c_InjectAdminJS();

endif; // end if !class_exists()

?>
