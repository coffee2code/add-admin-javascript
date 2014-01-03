=== Add Admin JavaScript ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: admin, javascript, js, script, admin theme, customization, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.5
Tested up to: 3.8
Stable tag: 1.3

Interface for easily defining additional JavaScript (inline and/or by URL) to be added to all administration pages.


== Description ==

Ever want to introduce custom dynamic functionality to your WordPress admin pages and otherwise harness the power of JavaScript?  Any modification you may want to do with JavaScript can be facilitated via this plugin.

Using this plugin you'll easily be able to define additional JavaScript (inline and/or by URL) to be added to all administration pages. You can define JavaScript to appear inline in the admin head, admin footer (recommended), or in the admin footer within a jQuery `jQuery(document).ready(function($)) {}` section, or reference JavaScript files to be linked in the page header. The referenced JavaScript files will appear in the admin head first, listed in the order defined in the plugin's settings. Then any inline admin head JavaScript is added to the admin head. All values can be filtered for advanced customization (see Filters section).

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/add-admin-javascript/) | [Plugin Directory Page](http://wordpress.org/plugins/add-admin-javascript/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Unzip `add-admin-javascript.zip` inside the `/wp-content/plugins/` directory for your site (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to "Appearance" -> "Admin JavaScript" and add some JavaScript to be added into all admin pages.


== Frequently Asked Questions ==

= Can I add JavaScript I defined via a file, or one that is hosted elsewhere? =

Yes, via the "Admin JavaScript Files" input field on the plugin's settings page.

= Can I limit what admin pages the JavaScript gets output on? =

No, not presently. The JavaScript is added to every admin page on the site.

However, you can preface your selectors with admin page specific class(es) on 'body' tag to ensure CSS only applies on certain admin pages. (e.g. `jQuery('body.index-php h2').hide();`).

= Can I limit what users the JavaScript applies to? =

No, not presently. The JavaScript is added for any user that can enter the admin section of the site.

= Does this plugin include unit tests? =

Yes.


== Screenshots ==

1. A screenshot of the plugin's admin settings page.


== Filters ==

The plugin exposes four filters for hooking. Typically, these customizations would be put into your active theme's functions.php file, or used by another plugin.

= c2c_add_admin_js_files (filter) =

The 'c2c_add_admin_js_files' hook allows you to programmatically add to or
customize any referenced JavaScript files defined in the "Admin JavaScript Files" field.

Arguments:

* $files (array): Array of JavaScript files

Example:

`
add_filter( 'c2c_add_admin_js_files', 'my_admin_js_files' );
function my_admin_js_files( $files ) {
	$files[] = 'http://ajax.googleapis.com/ajax/libs/yui/2.8.1/build/yuiloader/yuiloader-min.js';
	return $files;
}
`

= c2c_add_admin_js_head (filter) =

The 'c2c_add_admin_js_head' hook allows you to programmatically add to or
customize any JavaScript to be put into the page head as defined in the
"Admin JavaScript (in head)" field.

Arguments:

* $js (string): JavaScript

Example:

`
add_filter( 'c2c_add_admin_js_head', 'my_add_head_js' );
function my_add_head_js( $js ) {
	$js .= "alert('Hello');";
	return $js;
}
`

= c2c_add_admin_js_footer (filter) =

The 'c2c_add_admin_js_footer' hook allows you to programmatically add to or
customize any JavaScript to be put into the page head as defined in the
"Admin JavaScript (in footer)" field.

Arguments:

* $js (string): JavaScript

Example:

`
add_filter( 'c2c_add_admin_js_footer', 'my_add_footer_js' );
function my_add_footer_js( $js ) {
	$js .= "alert('Hello');";
	return $js;
}
`

= c2c_add_admin_js_jq (filter) =

The 'c2c_add_admin_js_jq' hook allows you to filter the jQuery JavaScript to be output in the footer of admin pages.
The 'c2c_add_admin_js_jq' hook allows you to programmatically add to or
customize any jQuery JS to be put into the page footer as defined in the
"Admin jQuery JavaScript" field.

Arguments:

* $js_jq (string): String of jQuery JavaScript

Example:

`
add_filter( 'c2c_add_admin_js_jq', 'my_add_jq' );
function my_add_jq( $js_jq ) {
	$js_jq .= "$('.hide_me').hide();";
	return $js_jq;
}
`


== Changelog ==

= 1.3 (2014-01-03) =
* Fix enqueuing multiple JS files by generating unique handle for each
* Fix enqueuing of local files when not prepended with forward slash
* Add unit tests
* Update plugin framework to 036
* Improve URL path construction
* Use explicit path for require_once()
* Add reset() to reset object to its initial state
* Remove __clone() and __wake() since they are part of framework
* For options_page_description(), match method signature of parent class
* Note compatibility through WP 3.8+
* Drop compatibility with versions of WP older than 3.5
* Update copyright date (2014)
* Change donate link
* Minor readme.txt tweaks (mostly spacing)
* Add banner
* Update screenshot

= 1.2 =
* Move 'Advanced Tips' section from bottom of settings page into contextual help section
* Add `help_tabs_content()` and `contextual_help()`
* Prevent textareas from wrapping lines
* Change input fields to be displayed as inline_textarea instead of textarea
* Add `instance()` static method for returning/creating singleton instance
* Made static variable 'instance' private
* Add dummy `__clone()` and `__wakeup()`
* Remove `c2c_AddAdminJavaScript()`; only PHP5 constructor is supported now
* Update plugin framework to 035
* Discontinue use of explicit pass-by-reference for objects
* Add check to prevent execution of code if file is directly accessed
* Regenerate .pot
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Minor documentation improvements
* Note compatibility through WP 3.5+
* Drop compatibility versions of WP older than 3.1
* Update copyright date (2013)
* Minor code reformatting (spacing)
* Remove ending PHP close tag
* Create repo's WP.org assets directory
* Move screenshot into repo's assets directory

= 1.1.1 =
* Fix typo in code example in Advanced Tips
* Add addition help text for js_head to indicate use of js_foot is preferred
* Update .pot
* Update screenshot

= 1.1 =
* Update plugin framework to 029
* Save a static version of itself in class variable $instance
* Discontinue use of global variable $c2c_add_admin_js to store instance
* Explicitly declare all functions as public
* Add __construct(), activation(), and uninstall()
* Note compatibility through WP 3.3+
* Drop compatibility with versions of WP older than 3.0
* Expand most onscreen and documentation references from "JS" to "JavaScript"
* Add .pot
* Add screenshot
* Add 'Domain Path' plugin header
* Tweak description
* Minor code formatting changes (spacing)
* Update copyright date (2011)
* Add plugin homepage and author links in description in readme.txt

= 1.0 =
* Initial release (not publicly released)


== Upgrade Notice ==

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
