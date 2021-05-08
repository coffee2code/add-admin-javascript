=== Add Admin JavaScript ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: admin, javascript, js, script, admin theme, customization, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.9
Tested up to: 5.7
Stable tag: 2.0

Interface for easily defining additional JavaScript (inline and/or by URL) to be added to all administration pages.


== Description ==

Ever want to introduce custom dynamic functionality to your WordPress admin pages and otherwise harness the power of JavaScript?  Any modification you may want to do with JavaScript can be facilitated via this plugin.

Using this plugin you'll easily be able to define additional JavaScript (inline and/or by URL) to be added to all administration pages. You can define JavaScript to appear inline in the admin head, admin footer (recommended), or in the admin footer within a jQuery `jQuery(document).ready(function($)) {}` section, or reference JavaScript files to be linked in the page header. The referenced JavaScript files will appear in the admin head first, listed in the order defined in the plugin's settings. Then any inline admin head JavaScript is added to the admin head. All values can be filtered for advanced customization (see Filters section).

Links: [Plugin Homepage](https://coffee2code.com/wp-plugins/add-admin-javascript/) | [Plugin Directory Page](https://wordpress.org/plugins/add-admin-javascript/) | [GitHub](https://github.com/coffee2code/add-admin-javascript/) | [Author Homepage](https://coffee2code.com)


== Installation ==

1. Install via the built-in WordPress plugin installer. Or download and unzip `add-admin-javascript.zip` inside the plugins directory for your site (typically `wp-content/plugins/`)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to "Settings" -> "Admin JavaScript" and add some JavaScript to be added into all admin pages. (You can also use the "Settings" link in the plugin's entry on the admin "Plugins" page).


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

The plugin's syntax highlighting of JavaScript (available as of WP 4.9) honors the built-in setting for whether syntax highlighting should be enabled or not.

To disable syntax highlighting, go to your profile page. Next to "Syntax Highlighting", click the checkbox labeled "Disable syntax highlighting when editing code". Note that this checkbox disables syntax highlighting throughout the admin interface and not just specifically for the plugin's settings page.

= Does this plugin include unit tests? =

Yes.


== Screenshots ==

1. A screenshot of the plugin's admin settings page.


== Hooks ==

The plugin exposes four filters for hooking. Typically, code making use of filters should ideally be put into a mu-plugin or site-specific plugin (which is beyond the scope of this readme to explain). Bear in mind that most of the features controlled by these filters are configurable via the plugin's settings page. These filters are likely only of interest to advanced users able to code.

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

= 2.0 (2021-05-08) =
Highlights:

This recommended minor release updates its plugin framework, restructures unit test files, notes compatibility through 5.7+, and minor behind-the-scenes tweaks.

Details:

* Change: Outright support HTML5 rather than check for theme support of HTML5, since that isn't relevant to admin
* Change: Update plugin framework to 061
    * 061:
    * Fix bug preventing settings from getting saved
    * 060:
    * Rename class from `c2c_{PluginName}_Plugin_051` to `c2c_Plugin_060`
    * Move string translation handling into inheriting class making the plugin framework code plugin-agnostic
        * Add abstract function `get_c2c_string()` as a getter for translated strings
        * Replace all existing string usage with calls to `get_c2c_string()`
    * Handle WordPress's deprecation of the use of the term "whitelist"
        * Change: Rename `whitelist_options()` to `allowed_options()`
        * Change: Use `add_allowed_options()` instead of deprecated `add_option_whitelist()` for WP 5.5+
        * Change: Hook `allowed_options` filter instead of deprecated `whitelist_options` for WP 5.5+
    * New: Add initial unit tests (currently just covering `is_wp_version_cmp()` and `get_c2c_string()`)
    * Add `is_wp_version_cmp()` as a utility to compare current WP version against a given WP version
    * Refactor `contextual_help()` to be easier to read, and correct function docblocks
    * Don't translate urlencoded donation email body text
    * Add inline comments for translators to clarify purpose of placeholders
    * Change PHP package name (make it singular)
    * Tweak inline function description
    * Note compatibility through WP 5.7+
    * Update copyright date (2021)
    * 051:
    * Allow setting integer input value to include commas
    * Use `number_format_i18n()` to format integer value within input field
    * Update link to coffee2code.com to be HTTPS
    * Update `readme_url()` to refer to plugin's readme.txt on plugins.svn.wordpress.org
    * Remove defunct line of code
* Change: Prevent appending newline to value of setting passed to filter unless an actual value was configured
* Change: Add separator character between merged strings used for the script handle when enqueuing
* Change: Move translation of all parent class strings into main plugin file
* Change: Tweak conditional checks to be more succinct
* Change: Ensure there's a current screen before attempting to get one of its properties
* Change: Note compatibility through WP 5.7+
* Change: Update copyright date (2021)
* Change: Tweak some inline function documentation
* Unit tests:
    * New: Add tests for JS files getting registered and enqueued
    * New: Add tests for `add_codemirror()`
    * Change: Restructure unit test directories and files into `tests/` top-level directory
    * Change: In bootstrap, store path to plugin file constant so its value can be used within that file and in test file

= 1.9.1 (2020-09-26) =
* Change: Update plugin framework to 051
    * Allow setting integer input value to include commas
    * Use `number_format_i18n()` to format integer value within input field
    * Update link to coffee2code.com to be HTTPS
    * Update `readme_url()` to refer to plugin's readme.txt on plugins.svn.wordpress.org
    * Remove defunct line of code
* Change: Note compatibility through WP 5.5+
* Change: Restructure unit test file structure
    * New: Create new subdirectory `phpunit/` to house all files related to unit testing
    * Change: Move `bin/` to `phpunit/bin/`
    * Change: Move `tests/bootstrap.php` to `phpunit/`
    * Change: Move `tests/` to `phpunit/tests/`
    * Change: Rename `phpunit.xml` to `phpunit.xml.dist` per best practices
* Change: Add missing changelog entry for v1.9 release into readme.txt

= 1.9 (2020-06-26) =
Highlights:

This minor release updates its plugin framework, adds a TODO.md file, updates a few URLs to be HTTPS, expands unit testing, updates compatibility to be WP 4.9 through 5.4+, and minor behind-the-scenes tweaks.

Details:

* Change: Change class names used for admin notice to match current WP convention
* Change: Update plugin framework to 050
    * Allow a hash entry to literally have '0' as a value without being entirely omitted when saved
    * Output donation markup using `printf()` rather than using string concatenation
    * Update copyright date (2020)
    * Note compatibility through WP 5.4+
    * Drop compatibility with version of WP older than 4.9
* New: Add TODO.md and move existing TODO list from top of main plugin file into it (and add more items to it)
* Change: Tweak help text for 'files' setting for better phrasing and to remove extra sentence spaces
* Change: Note compatibility through WP 5.4+
* Change: Drop compatibility for version of WP older than 4.9
* Change: Update links to coffee2code.com to be HTTPS
* Unit tests:
    * New: Add tests for `options_page_description()`
    * New: Add test for default hooks
    * New: Add tests for setting and query param names
    * New: Label groupings of tests
    * Change: Remove unnecessary unregistering of hooks in `tearDown()`
    * Change: Move `test_turn_on_admin()` until just before first needed now that other tests can run before it
    * Change: Store plugin instance in class variable to simplify referencing it
    * Change: Use HTTPS for link to WP SVN repository in bin script for configuring unit tests (and delete commented-out code)

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/add-admin-javascript/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 2.0 =
Recommended minor update: updated plugin framework, restructured unit test files, noted compatibility through WP 5.7+, and updated copyright date (2021).

= 1.9.1 =
Trivial update: Updated plugin framework to version 051, restructured unit test file structure, and noted compatibility through WP 5.5+.

= 1.9 =
Minor update: updated plugin framework, added a TODO.md file, updated a few URLs to be HTTPS, expanded unit testing, updated compatibility to be WP 4.9 through 5.4+, and minor behind-the-scenes tweaks.

= 1.8.1 =
Minor bugfix release: Fixes typo causing PHP warning.

= 1.8 =
Minor update: added non-HTML5 support when not supported by the theme, modernized and fixed unit tests, noted compatibility through WP 5.3+, and updated copyright date (2020).

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
