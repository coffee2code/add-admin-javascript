# Changelog

## _(in-progress)_
* Unit tests:
    * New: Add unit tests for recovery mode's admin notice
    * New: Add assertion that recovery mode is not enabled if query param is present but false
    * Fix: Prevent WP from attempting to print the emoji detection script (which isn't built in the develop.svn repo)
    * Change: Update unit test install script and bootstrap to use latest WP unit test repo
    * Change: Ensure admin mode is enabled before running certain tests
    * Change: Rename `test_can_show_js_with_false_query_param()` to `test_can_show_js_with_true_query_param()` to better reflect its intent
* Change: Note compatibility through WP 5.3+
* Change: Tweak installation instruction
* Change: Tweak description of "Hooks" section in readme.txt
* Change: Update copyright date (2020)

## 1.7 _(2019-04-09)_

### Highlights:

* This release adds a recovery mode to disable output of JavaScript via the plugin (and an admin notice when it is active), replaces code input fields with code editor (with syntax highlight, syntax checking, code completion, and more), improves documentation, updates the plugin framework, notes compatibility through WP 5.1+, drops compatibility with versions of WP older than 4.7, and more documentation and code improvements.

### Details:

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

## 1.6 _(2017-11-03)_
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

## 1.5 _(2016-04-22)_
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

## 1.4 _(2016-01-12)_

### Highlights:

* This release fixes a couple of bugs, adds support for language packs, and has many minor behind-the-scenes changes.

### Details:

* Bugfix: Allow CSS links/files with query args to be enqueued.
* Bugfix: If specified as part of the link, 'ver' query arg takes precedence as script version.
* Add: More unit tests.
* Add: Amend a couple of the FAQ answers to indicate things are possible via hooks rather than suggesting they aren't possible.
* Change: Update plugin framework to 040:
    * Change class name to `c2c_AddAdminCSS_Plugin_040` to be plugin-specific.
    * Set textdomain using a string instead of a variable.
    * Don't load textdomain from file.
    * Change admin page header from 'h2' to 'h1' tag.
    * Add `c2c_plugin_version()`.
    * Formatting improvements to inline docs.
* Change: Add support for language packs:
    * Set textdomain using a string instead of a variable.
    * Remove .pot file and /lang subdirectory.
* Change: Declare class as final.
* Change: Explicitly declare methods in unit tests as public or protected.
* Change: Minor tweak to description.
* Change: Minor improvements to inline docs and test docs.
* Add: Create empty index.php to prevent files from being listed if web server has enabled directory listings.
* Change: Note compatibility through WP 4.4+.
* Change: Remove support for versions of WordPress older than 4.1.
* Change: Update copyright date (2016).

## 1.3.4 _(2015-04-30)_
* Bugfix: Fix line-wrapping display for Firefox and Safari
* Enhancement: Add an additional unit test
* Update: Move 'Advanced' section lower in readme; add inline docs to example code
* Update: Note compatibility through WP 4.2+

## 1.3.3 _(2015-02-21)_
* Bugfix: Revert back to using `dirname(__FILE__)`; `__DIR__` is only PHP 5.3+

## 1.3.2 _(2015-02-16)_
* Update plugin framework to 039
* Add to and improve unit tests
* Explicitly declare class method `activation()` and `uninstall()` static
* Use `__DIR__` instead of `dirname(__FILE__)`
* Various inline code documentation improvements (spacing, punctuation)
* Note compatibility through WP 4.1+
* Update copyright date (2015)
* Regenerate .pot

## 1.3.1 _(2014-08-25)_
* Update plugin framework to 038
* Minor plugin header reformatting
* Minor code reformatting (spacing, bracing)
* Change documentation links to wp.org to be https
* Localize an additional string
* Note compatibility through WP 4.0+
* Regenerate .pot
* Add plugin icon

## 1.3 _(2014-01-03)_
* Fix enqueuing multiple JS files by generating unique handle for each
* Fix enqueuing of local files when not prepended with forward slash
* Add unit tests
* Update plugin framework to 036
* Improve URL path construction
* Use explicit path for require_once()
* Add reset() to reset object to its initial state
* Remove `__clone()` and `__wake()` since they are part of framework
* For `options_page_description()`, match method signature of parent class
* Note compatibility through WP 3.8+
* Drop compatibility with versions of WP older than 3.5
* Update copyright date (2014)
* Change donate link
* Minor readme.txt tweaks (mostly spacing)
* Add banner
* Update screenshot

## 1.2
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

## 1.1.1
* Fix typo in code example in Advanced Tips
* Add addition help text for `js_head` to indicate use of `js_foot` is preferred
* Update .pot
* Update screenshot

## 1.1
* Update plugin framework to 029
* Save a static version of itself in class variable $instance
* Discontinue use of global variable `$c2c_add_admin_js` to store instance
* Explicitly declare all functions as public
* Add `__construct()`, `activation()`, and `uninstall()`
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

## 1.0
* Initial release (not publicly released)
