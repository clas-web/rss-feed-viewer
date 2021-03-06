<?php
/*
Plugin Name: RSS Feed Viewer
Plugin URI: https://github.com/clas-web/rss-feed-viewer
Description: A simple RSS feed viewer that does strip out any tags. Derived from the official WP RSS widget.
Version: 1.2.1
Author: Crystal Barton
Author URI: https://www.linkedin.com/in/crytalbarton
GitHub Plugin URI: https://github.com/clas-web/rss-feed-viewer
*/


require_once( __DIR__.'/control.php' );
RssFeedView_WidgetShortcodeControl::register_widget();
RssFeedView_WidgetShortcodeControl::register_shortcode();

