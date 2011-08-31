=== Inject Admin JS ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: admin, javascript, js, script, admin theme, customization, coffee2code
Requires at least: 2.8
Tested up to: 3.0.1
Stable tag: 1.0
Version: 1.0

Easily define additional JavaScript (inline and/or by URL) to be added to all administration pages.


== Description ==

Easily define additional JavaScript (inline and/or by URL) to be added to all administration pages.

Ever want to introduce custom dynamic functionality to your WordPress admin pages and otherwise harness the power of JavaScript?  Any modification you may want to do with JS can be facilitated via this plugin.

Using this plugin you'll easily be able to define additional JS (inline and/or by URL) to be added to all administration pages.  You can define JS to appear inline in the admin head, admin footer (recommended), or in the admin footer within a jQuery `jQuery(document).ready(function($)) {}` section, or reference JS files to be linked in the page header.  The referenced JS files will appear in the admin head first, listed in the order defined in the plugin's settings.  Then any inline admin head JS is added to the admin head.  All values can be filtered for advanced customization (see Filters section).


== Installation ==

1. Download the file inject-admin-js.zip and unzip it into your /wp-content/plugins/ directory (or install via the built-in WordPress plugin installer).
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to "Appearance" -> "Admin JS" and add some JS to be injected into all admin pages.


== Frequently Asked Questions ==

= Can I add JS I defined via a file, or one that is hosted elsewhere? =

Yes, via the "Admin JS Files" input field on the plugin's settings page.

= Can I limit what admin pages the JS applies to? =

No, not presently.  The JS is injected for every admin page on the site.

= Can I limit what users the JS applies to? =

No, not presently.  The JS is injected for any user that can enter the admin section of the site.


== Screenshots ==

1. A screenshot of the plugin's admin settings page.


== Filters ==

The plugin exposes four filters for hooking.

= c2c_inject_admin_js_files (filter) =

The 'c2c_inject_admin_js_files' hook allows you to programmatically add to or
customize any referenced JS files defined in the "Admin JS Files" field.

Arguments:

* $files (array): Array of JS files

Example:

`add_filter( 'c2c_inject_admin_js_files', 'my_admin_js_files' );
function my_admin_js_files( $files ) {
	$files[] = 'http://ajax.googleapis.com/ajax/libs/yui/2.8.1/build/yuiloader/yuiloader-min.js';
	return $files;
}`

= c2c_inject_admin_js_head (filter) =

The 'c2c_inject_admin_js_head' hook allows you to programmatically add to or
customize any JS to be put into the page head as defined in the
"Admin JS (in head)" field.

Arguments:

* $js (string): JavaScript

Example:

`add_filter( 'c2c_inject_admin_js_head', 'my_add_head_js' );
function my_add_head_js( $js ) {
	$js .= "alert('Hello');";
	return $js;
}`

= c2c_inject_admin_js_footer (filter) =

The 'c2c_inject_admin_js_footer' hook allows you to programmatically add to or
customize any JS to be put into the page head as defined in the
"Admin JS (in footer)" field.

Arguments:

* $js (string): JavaScript

Example:

`add_filter( 'c2c_inject_admin_js_footer', 'my_add_footer_js' );
function my_add_footer_js( $js ) {
	$js .= "alert('Hello');";
	return $js;
}`

= c2c_inject_admin_js_jq (filter) =

The 'c2c_inject_admin_js_jq' hook allows you to filter the jQuery JavaScript to be output in the footer of admin pages.
The 'c2c_inject_admin_js_jq' hook allows you to programmatically add to or
customize any jQuery JS to be put into the page footer as defined in the
"Admin jQuery JS" field.

Arguments:

* $js_jq (string): String of jQuery JS

Example:

`add_filter( 'c2c_inject_admin_js_jq', 'my_add_jq' );
function my_add_jq( $js_jq ) {
	$js_jq .= "$('.hide_me').hide();";
	return $js_jq;
}`


== Changelog ==

= 1.0 =
* Initial release


== Upgrade Notice ==

= 1.0 =
Initial public release!