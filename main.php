<?php
/*
Plugin Name: RSS Feed Viewer
Plugin URI: 
Description: A simple RSS feed viewer that does strip out any tags. Derived from the official WP RSS widget.
Version: 1.0.0
Author: Crystal Barton
Author URI: 
*/


require_once( dirname(__FILE__).'/control.php' );
RssFeedView_WidgetShortcodeControl::register_widget();
RssFeedView_WidgetShortcodeControl::register_shortcode();

