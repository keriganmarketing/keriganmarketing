<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

delete_option('yg_filter_any_options');
delete_option('yg_filter_any_tax_options');
delete_option('yg_filter_cache');