=== Plugin Name ===
Contributors: yairgelb
Donate link: http://www.ysgdesign.com/donate.php
Tags: post list page, custom column, sort, meta data, taxonomy, admin, multi-select
Requires at least: 3.1.0
Tested up to: 4.4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Ever wish you could easily add a column to your custom post admin page with taxonomy or some cool custom meta data you created? Well now you can.

== Description ==

Take full control of all your post list pages. Set columns, make them sortable and add a filter dropdown. You can even add a thumbnail column. 
ColumnPro does all that with an elegant and easy to use interface. Make your admin panel an obedient little puppy.

= Basic Features: =

*	Set up columns for any post type
*	Set meta data or taxonomy based columns
*	Remove unwanted columns
*	Add filters, select or multi-select, to easily see your desired posts
*	Set columns to be sortable alphabetically or numerically 
*	You can drag & drop columns to order them
*	Collapse and expand column sets for accessibility
*	Add a thumbnail column if post type supports it
*	Add an ID column
*	Cache queries for large number of posts


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->ColumnPro settings screen to add columns


== Frequently Asked Questions ==

= Should I use cache for my widget? =

ColumnPro works only on the backend and will not affect you site performance. However if you have a large number of posts and post types, or a meta data or taxonomy with a lot of values or terms, the cache will improve you admin performance and bring back the list pages faster. Note only terms and meta data are cached not the actual posts retrived, WordPress core does that.

= I can define taxonomy column when I register my taxonomy =

True. You can set 'show_admin_column' to true in register_taxonomy. But if you want to use a taxonomy of a plugin or you really want to sort or filter on the taxonomy or you just aren’t a developer set a taxonomy column via the plugin.


== Screenshots ==

1. Admin setting page.
2. Set columns for any post type.
3. Columns on post list page.


== Changelog ==

= 1.1.0 =
* Fixed meta data dropdown duplication bug
* Limited query hooks only to admin loops
* Query hooks run only if custom columns are set

= 2.0.0 =
* Fixed meta data as array
* Taxonomy based on ID for better accuracy
* Added multi select
* Added remove column
* Added ID column