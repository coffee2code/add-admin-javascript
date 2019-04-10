=== Add Admin JavaScript ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: admin, javascript, js, script, admin theme, customization, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.7
Tested up to: 5.1
Stable tag: 1.7

Interface for easily defining additional JavaScript (inline and/or by URL) to be added to all administration pages.


== Description ==

Ever want to introduce custom dynamic functionality to your WordPress admin pages and otherwise harness the power of JavaScript?  Any modification you may want to do with JavaScript can be facilitated via this plugin.

Using this plugin you'll easily be able to define additional JavaScript (inline and/or by URL) to be added to all administration pages. You can define JavaScript to appear inline in the admin head, admin footer (recommended), or in the admin footer within a jQuery `jQuery(document).ready(function($)) {}` section, or reference JavaScript files to be linked in the page header. The referenced JavaScript files will appear in the admin head first, listed in the order defined in the plugin's settings. Then any inline admin head JavaScript is added to the admin head. All values can be filtered for advanced customization (see Filters section).

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/add-admin-javascript/) | [Plugin Directory Page](https://wordpress.org/plugins/add-admin-javascript/) | [GitHub](https://github.com/coffee2code/add-admin-javascript/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Install via the built-in WordPress plugin installer. Or download and unzip `add-admin-javascript.zip` inside the plugins directory for your site (typically `wp-content/plugins/`)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to "Appearance" -> "Admin JavaScript" and add some JavaScript to be added into all admin pages.


== Frequently Asked Questions ==

= How can I edit the plugin's settings in the event I supplied JavaScript that prevents the admin pages from properly loading or being seen? =

It is certainly possible that you can put yourself in an unfortunate position by supplying JavaScript that could render the admin (in whole or in part) inoperable or hidden, making it seeminly impossible to fix or revert your changes. Fortunately, there are a number of approaches you can take to correct the problem.

The recommended approach is to visit the URL for the plugin's settings page, but appended with a special query parameter to disable the output of its JavaScript. The plugin's settings page would typically be at a URL like `https://example.com/wp-admin/options-general.php?page=add-admin-javascript%2Fadd-admin-javascript.php`. Append `&c2c-no-js=1` to that, so that the URL is `https://example.com/wp-admin/options-general.php?page=add-admin-javascript%2Fadd-admin-javascript.php&c2c-no-js=1` (obviously change example.com with the domain name for your site).

There are other approaches you can use, though they require direct database or server filesystem access:

* Disable JavaScript in your browser and revist the page. With JavaScript disabled, any JavaScript defined by the plugin would have no effect for you. Fix the JavaScript you defined and then re-enabled JavaScript for your browser.
* In the site's `wp-config.php` file, define a constant to disable output of the plugin-defined JavaScript: `define( 'C2C_ADD_ADMIN_JAVASCRIPT_DISABLED', true );`. You can then visit the site's admin. Just remember to remove that line after you've fixed the JavaScript (or at least change "true" to "false"). This is an alternative to the query parameter approach described above, though it persists while the constant remains defined. There will be an admin notice on the plugin's setting page to alert you to the fact that the constant is defined and effectively disabling the plugin from adding any JavaScript.
* Presuming you know how to directly access the database: within the site's database, find the row with the option_name field value of `c2c_add_admin_javascript` and delete that row. The settings you saved for the plugin will be deleted and it will be like you've installed the plugin for the first time.
* If your server has WP-CLI installed, you can delete the plugin's setting from the commandline: `wp option delete c2c_add_admin_javascript`

The initial reaction by some might be to remove the plugin from the server's filesystem. This will certainly disable the plugin and prevent the JavaScript you configured through it from taking effect, restoring the access and functionality to the backend. However, reinstalling the plugin will put you back into the original predicament because the plugin will use the previously-configured settings, which wouldn't have changed.

= Can I add JavaScript I defined via a file, or one that is hosted elsewhere? =

Yes, via the "Admin JavaScript Files" input field on the plugin's settings page.

= Can I limit what admin pages the JavaScript gets output on? =

No, not presently. At least not directly. By default, the JavaScript is added to every admin page on the site.

However, you can preface your selectors with admin page specific class(es) on 'body' tag to ensure CSS only applies on certain admin pages. (e.g. `jQuery('body.index-php h2').hide();`).

Or, you can hook all the plugin's filters and determine the current admin page content to decide whether the respective hook argument should be returned (and thus output) or not.

= Can I limit what users the JavaScript applies to? =

No, not presently. At least not directly. By default, the JavaScript is added for any user that can enter the admin section of the site.

You can hook all the plugin's filters and determine the current user to decide whether the respective hook argument should be returned (and thus output) for the user or not.

= How do I disable syntax highlighting? =

The plugin's syntax highlighting of JavaScript (available on WP 4.9+) honors the built-in setting for whether syntax highlighting should be enabled or not.

To disable syntax highlighting, go to your profile page. Next to "Syntax Highlighting", click the checkbox labeled "Disable syntax highlighting when editing code". Note that this checkbox disables syntax highlighting throughout the admin interface and not just specifically for the plugin's settings page.

= Does this plugin include unit tests? =

Yes.


== Screenshots ==

1. A screenshot of the plugin's admin settings page.


== Hooks ==

The plugin exposes four filters for hooking. Typically, these customizations would be put into your active theme's functions.php file, or used by another plugin.

**c2c_add_admin_js_files (filter)**

The 'c2c_add_admin_js_files' filter allows programmatic modification of the list of JavaScript files to enqueue in the admin.

Arguments:

* $files (array): Array of JavaScript files.

Example:

`
/**
 * Adds a JavaScript file to be enqueued in the WP admin.
 *
 * @param array $files Array of files.
 * @return array
 */
function my_admin_js_files( $files ) {
	$files[] = 'http://ajax.googleapis.com/ajax/libs/yui/2.8.1/build/yuiloader/yuiloader-min.js';
	return $files;
}
add_filter( 'c2c_add_admin_js_files', 'my_admin_js_files' );

`

**c2c_add_admin_js_head (filter)**

The 'c2c_add_admin_js_head' filter allows customization of the JavaScript that should be added directly to the admin page head.

Arguments:

* $js (string): JavaScript code (without `<script>` tags).

Example:

`
/**
 * Adds JavaScript code to be added to the admin page head.
 *
 * @param string $js JavaScript code.
 * @return string
 */
function my_add_head_js( $js ) {
	$js .= "alert('Hello');";
	return $js;
}
add_filter( 'c2c_add_admin_js_head', 'my_add_head_js' );
`

**c2c_add_admin_js_footer (filter)**

The 'c2c_add_admin_js_footer' filter allows customization of the JavaScript that should be added directly to the admin footer.

Arguments:

* $js (string): JavaScript code (without `<script>` tags).

Example:

`
/**
 * Adds JavaScript code to be added to the admin footer.
 *
 * @param string $js JavaScript code.
 * @return string
 */
function my_add_footer_js( $js ) {
	$js .= "alert('Hello');";
	return $js;
}
add_filter( 'c2c_add_admin_js_footer', 'my_add_footer_js' );
`

**c2c_add_admin_js_jq (filter)**

The 'c2c_add_admin_js_jq' filter allows customization of the JavaScript that should be added directly to the admin footer within a jQuery document ready function.

Arguments:

* $jq_js (string): JavaScript code (without `<script>` tags or jQuery document ready function).

Example:

`
/**
 * Adds jQuery code to be added to the admin footer.
 *
 * @param string $jq_js jQuery code.
 * @return string
 */
function my_add_jq( $js_jq ) {
	$js_jq .= "$('.hide_me').hide();";
	return $js_jq;
}
add_filter( 'c2c_add_admin_js_jq', 'my_add_jq' );
`


== Changelog ==

= 1.7 (2019-04-09) =

Highlights:

* This release adds a recovery mode to disable output of JavaScript via the plugin (and an admin notice when it is active), replace code input fields with code editor (with syntax highlight, syntax checking, code completion, and more), improves documentation, updates the plugin framework, notes compatibility through WP 5.1+, drops compatibility with versions of WP older than 4.7, and more documentation and code improvements.

Details:

* New: Add syntax highlighting to JavaScript input fields
    * Adds code highlighting, syntax checking, and other features
* New: Add recovery mode to be able to disable output of JavaScript via the plugin
    * Add support for `c2c-no-js` query parameter for enabling recovery mode
    * Add support for `C2C_ADD_ADMIN_JAVASCRIPT_DISABLED` constant for enabling recovery mode
    * Display admin notice when recovery mode is active
    * Add `can_show_js()`, `remove_query_param_from_redirects()`, `recovery_mode_notice()`
* Change: Initialize plugin on `plugins_loaded` action instead of on load
* Change: Update plugin framework to 049
    * 049:
    * Correct last arg in call to `add_settings_field()` to be an array
    * Wrap help text for settings in `label` instead of `p`
    * Only use `label` for help text for checkboxes, otherwise use `p`
    * Ensure a `textarea` displays as a block to prevent orphaning of subsequent help text
    * Note compatibility through WP 5.1+
    * Update copyright date (2019)
    * 048:
    * When resetting options, delete the option rather than setting it with default values
    * Prevent double "Settings reset" admin notice upon settings reset
    * 047:
    * Don't save default setting values to database on install
    * Change "Cheatin', huh?" error messages to "Something went wrong.", consistent with WP core
    * Note compatibility through WP 4.9+
    * Drop compatibility with version of WP older than 4.7
* Change: Remove unnecessary `type='text/javascript'` attribute from `<script>` tags
* New: Add README.md file
* New: Add CHANGELOG.md file and move all but most recent changelog entries into it
* New: Add FAQ entry describing ways to fix having potentially crippled the admin
* New: Add inline documentation for hooks
* New: Add GitHub link to readme
* Unit tests:
    * Change: Improve tests for settings handling
    * Change: Update `set_option()` to accept an array of setting values to use
    * New: Add unit tests for `add_js_to_head()`, `add_js_to_foot()`
    * New: Add unit test for defaults for settings
    * Remove: Delete `setUp()` and invoke `setup_options()` within each test as needed
    * Remove: Delete private object variable for storing setting name
* Change: Store setting name in constant
* Change: Improve documentation for hooks within readme.txt
* Change: Use alternative example remote JS library to the defunct Yahoo UI library
* Change: Note compatibility through WP 5.1+
* Change: Drop compatibility with version of WP older than 4.7
* Change: Rename readme.txt section from 'Filters' to 'Hooks'
* Change: Modify formatting of hook name in readme to prevent being uppercased when shown in the Plugin Directory
* Change: Update installation instruction to prefer built-in installer over .zip file
* Change: Update copyright date (2019)
* Change: Update License URI to be HTTPS

= 1.6 (2017-11-03) =
* Change: Update plugin framework to 046
    * 046:
    * Fix `reset_options()` to reference instance variable `$options`.
	* Note compatibility through WP 4.7+.
	* Update copyright date (2017)
    * 045:
    * Ensure `reset_options()` resets values saved in the database.
    * 044:
    * Add `reset_caches()` to clear caches and memoized data. Use it in `reset_options()` and `verify_config()`.
    * Add `verify_options()` with logic extracted from `verify_config()` for initializing default option attributes.
    * Add `add_option()` to add a new option to the plugin's configuration.
    * Add filter 'sanitized_option_names' to allow modifying the list of whitelisted option names.
    * Change: Refactor `get_option_names()`.
    * 043:
    * Disregard invalid lines supplied as part of hash option value.
    * 042:
    * Update `disable_update_check()` to check for HTTP and HTTPS for plugin update check API URL.
    * Translate "Donate" in footer message.
* Change: Update unit test bootstrap
    * Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable
    * Enable more error output for unit tests
* Change: Align config array elements
* Change: Note compatibility through WP 4.9+
* Change: Remove support for WordPress older than 4.6
* Change: Update copyright date (2018)

= 1.5 (2016-04-22) =
* Change: Declare class as final.
* Change: Update plugin framework to 041:
    * For a setting that is of datatype array, ensure its default value is an array.
    * Make `verify_config()` public.
    * Use `<p class="description">` for input field help text instead of custom styled span.
    * Remove output of markup for adding icon to setting page header.
    * Remove styling for .c2c-input-help.
    * Add braces around the few remaining single line conditionals.
* Change: Note compatibility through WP 4.5+.
* Change: Remove 'Domain Path' from plugin header.
* New: Add LICENSE file.

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/add-admin-javascript/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 1.7 =
Recommended update: added recovery mode, added code editor inputs, tweaked plugin initialization process, updated plugin framework, compatibility is now WP 4.7 through WP 5.1+, updated copyright date (2019), and more documentation and code improvements.

= 1.6 =
Minor update: update plugin framework to version 046; verified compatibility through WP 4.9; dropped compatibility with versions of WordPress older than 4.6; updated copyright date (2018).

= 1.5 =
Minor update: update plugin framework to version 041; verified compatibility through WP 4.5.

= 1.4 =
Recommended update: bugfixes for CSS file links containing query arguments; improved support for localization; verified compatibility through WP 4.4; removed compatibility with WP earlier than 4.1; updated copyright date (2016)

= 1.3.4 =
Bugfix release: fixed line-wrapping display for Firefox and Safari; noted compatibility through WP 4.2+.

= 1.3.3 =
Bugfix release: reverted use of __DIR__ constant since it isn't supported on older installations (PHP 5.2)

= 1.3.2 =
Trivial update: improvements to unit tests; updated plugin framework to version 039; noted compatibility through WP 4.1+; updated copyright date (2015).

= 1.3.1 =
Trivial update: update plugin framework to version 038; noted compatibility through WP 4.0+; added plugin icon.

= 1.3 =
Recommended update: fixed multiple bugs related to enqueuing files; added unit tests; minor improvements; noted compatibility through WP 3.8+;

= 1.2 =
Recommended update. Highlights: stopped wrapping long input field text; updated plugin framework; updated WP compatibility as 3.1 - 3.5+; explicitly stated license; and more.

= 1.1.1 =
Trivial update: fixed typo in code example; updated screenshot

= 1.1 =
Recommended update: renamed plugin (breaking backwards compatibility); noted compatibility through WP 3.3; dropped support for versions of WP older than 3.0; updated plugin framework; and more.

= 1.0 =
Initial public release!
