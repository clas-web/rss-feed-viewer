<?php

require_once( dirname(__FILE__).'/widget-shortcode-control.php' );

/**
 * RssFeedView_WidgetShortcodeControl
 * 
 * The RssFeedView_WidgetShortcodeControl class for the "RSS Feed Viewer" plugin.
 * Derived from the official WP RSS widget.
 * 
 * Shortcode Example:
 * [rss_feed_viewer title="My RSS Feed Viewer" url="http://www.example.com/feed" items="5" sort="a-z"]
 * 
 * @package    clas-buttons
 * @author     Crystal Barton <cbarto11@uncc.edu>
 */
if( !class_exists('RssFeedView_WidgetShortcodeControl') ):
class RssFeedView_WidgetShortcodeControl extends WidgetShortcodeControl
{
	
	private static $MIN_ITEMS = 1;
	private static $MAX_ITEMS = 20;
	
	private static $SORT_TYPES = array(
		'in-order'		=> 'Recent First',
		'reverse-order'	=> 'Recent Last',
		'a-z'			=> 'A-Z',
		'z-a'			=> 'Z-A',
	);
	
	
	/**
	 * Constructor.
	 * Setup the properties and actions.
	 */
	public function __construct()
	{
		$widget_ops = array(
			'description'	=> 'Entries from any RSS or Atom feed.',
		);
		
		$control_ops = array(
			'width'			=> 400,
			'height'		=> 200,
		);
		
		parent::__construct( 'rss-feed-viewer', 'RSS Feed Viewer', $widget_ops, $control_ops );
	}
	

	/**
	 * Enqueues the scripts or styles needed for the control in the site frontend.
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_style( 'rss-feed-viewer', plugins_url( '/style.css' , __FILE__ ) );
	}
	
	
	/**
	 * Output the widget form in the admin.
	 * Use this function instead of form.
	 * @param   array   $options  The current settings for the widget.
	 */
	public function print_widget_form( $options )
	{
		$options = $this->merge_options( $options );
		extract( $options );
		
		?>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat">
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e( 'URL:' ); ?></label> 
		<input id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" type="text" value="<?php echo esc_attr( $url ); ?>" class="widefat">
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'items' ); ?>"><?php _e( 'Number of items:' ); ?></label> 
		<select id="<?php echo $this->get_field_id( 'items' ); ?>" name="<?php echo $this->get_field_name( 'items' ); ?>">
			<?php for( $i = self::$MIN_ITEMS; $i < self::$MAX_ITEMS+1; $i++ ): ?>
			
				<option value="<?php echo $i; ?>" <?php selected($i, $items); ?>><?php echo $i; ?></option>
			
			<?php endfor; ?>
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'sort' ); ?>"><?php _e( 'Sort Order:' ); ?></label> 
		<select id="<?php echo $this->get_field_id( 'sort' ); ?>" name="<?php echo $this->get_field_name( 'sort' ); ?>">
			<?php foreach( self::$SORT_TYPES as $sort_key => $sort_value ): ?>
			
				<option value="<?php echo $sort_key; ?>" <?php selected($sort, $sort_key); ?>><?php echo $sort_value; ?></option>
			
			<?php endforeach; ?>
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'allowed_tags' ); ?>"><?php _e( 'Allowed HTML Tags:' ); ?></label> 
		<input id="<?php echo $this->get_field_id( 'allowed_tags' ); ?>" name="<?php echo $this->get_field_name( 'allowed_tags' ); ?>" type="text" value="<?php echo esc_attr( $allowed_tags ); ?>" class="widefat">
		</p>
		
		
		<?php
	}
	
	
	/**
	 * Get the default settings for the widget or shortcode.
	 * @return  array  The default settings.
	 */
	public function get_default_options()
	{
		return array(
			'title'			=> '',
			'url'			=> '',
			'items'			=> 5,
			'sort'			=> 'in-order',
			'allowed_tags'	=> 'p,div,br,ul,ol,li,span',
		);
	}
	
	
	/**
	 * Process options from the database or shortcode.
	 * Designed to convert options from strings or sanitize output.
	 * @param   array   $options  The current settings for the widget or shortcode.
	 * @return  array   The processed settings.
	 */
	public function process_options( $options )
	{
		// parse allowed tags.
		if( is_string($options['allowed_tags']) )
		{
			$options['allowed_tags'] = explode( ',', $options['allowed_tags'] );
			$options['allowed_tags'] = '<' . implode( '><', $options['allowed_tags'] ) . '>';
		}
		
		return $options;
	}


	/**
	 * Update a particular instance.
	 * Override function from WP_Widget parent class.
	 * @param   array       $new_options  New options set in the widget form by the user.
	 * @param   array       $old_options  Old options from the database.
	 * @return  array|bool  The settings to save, or false to cancel saving.
	 */
	public function update( $new_options, $old_options )
	{
		$testurl = ( isset( $new_options['url'] ) && ( !isset( $old_options['url'] ) || ( $new_options['url'] != $old_options['url'] ) ) );
		$rss_options = wp_widget_rss_process( $new_options, $testurl );
		return array_merge( $new_options, $rss_options );
	}
	
	
	/**
	 * Echo the widget or shortcode contents.
	 * @param   array  $options  The current settings for the control.
	 * @param   array  $args     The display arguments.
	 */
	public function print_control( $options, $args )
	{
		$options = $this->merge_options( $options );
		if( !$args ) $args = $this->get_args();
		
		extract( $options );
		
		$rss = fetch_feed($url);
		
		if( empty($title) )
		{
			if( !is_wp_error($rss) )
				$title = esc_html( strip_tags($rss->get_title()) );
			if( empty($title) )
				$title = 'Unknown Feed';
		}
		
		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', $title, $options, $this->id_base );
		
		$url = esc_url( strip_tags($url) );

		echo $args['before_widget'];
		echo '<div id="rss-feed-viewer-control-'.self::$index.'" class="wscontrol rss-feed-viewer-control">';
		
		echo '<div class="rss-list">';

		if( !empty($title) )
		{
			echo $args['before_title'] . $title . $args['after_title'];
		}
		
		if( is_wp_error($rss) )
		{
			echo '<div class="error">'.$rss->get_error_message().'</div>';
		}
		else
		{
			$this->wp_widget_rss_output( $rss, $options );
		}
		
		echo '</div>';
		
		echo '</div>';
		echo $args['after_widget'];
	}
	
	
	/**
	 * Echo the RSS items in the order specified by the user.
	 * Modified version of "function wp_widget_rss_output" in WP core.
	 * @param   SimplePie  $rss      The SimplePie RSS object.
	 * @param   array      $options  The current settings of the control.
	 */
	private function wp_widget_rss_output( $rss, $options )
	{
		$items = intval( $options['items'] );
		if( $items < self::$MIN_ITEMS ) $items = self::$MIN_ITEMS;
		if( $items > self::$MAX_ITEMS ) $items = self::$MAX_ITEMS;

		if( !$rss->get_item_quantity() )
		{
			echo '<div class="error">';
			echo __( 'An error has occurred, which probably means the feed is down. Try again later.' );
			echo '</div>';
		}
		else
		{
			// sort items
			$rss_items = $rss->get_items( 0, $items );
			switch( $options['sort'] )
			{
				case 'in-order':
				case 'reverse-order':
				case 'a-z':
				case 'z-a':
					usort( $rss_items, array($this, 'sort_rss_items_'.str_replace('-','_',$options['sort'])) );
					break;
			}
			
			// print items
			foreach( $rss_items as $item )
			{
				$link = $item->get_link();
				while( stristr( $link, 'http' ) != $link )
					$link = substr( $link, 1 );
				$link = esc_url( strip_tags( $link ) );

				$title = esc_html( trim( strip_tags( $item->get_title() ) ) );
				if ( empty( $title ) ) 
					$title = __( 'Untitled' );

				$summary = @html_entity_decode( $item->get_description(), ENT_QUOTES, get_option('blog_charset') );
				$summary = strip_tags( $summary, $options['allowed_tags'] );
				$summary = '<div class="summary">'.$summary.'</div>';

				if ( $link == '' )
				{
					echo '<div class="item">'.$title.$summary.'</li>';
				}
				else
				{
					echo '<div class="item"><a class="title" href="'.$link.'">'.$title.'</a>'.$summary.'</div>';
				}
			}
		}
		
		$rss->__destruct();
		unset($rss);
	}
	
	
	/**
	 * Sort in chronological order.
	 * @param   SimplePie_Item  $a  The first item to compare.
	 * @param   SimplePie_Item  $b  The second item to compare.
	 * @return  int             1 if b is greater, -1 if a is greater, 0 if matched.
	 */
	private function sort_rss_items_in_order( $a, $b )
	{
		$a_date = $a->get_date('U');
		$b_date = $b->get_date('U');
		
		if( $a_date == $b_date ) return 0;
		
		if( $a_date > $b_date ) return 1;
		
		return -1;
	}


	/**
	 * Sort in reverse chronological order.
	 * @param   SimplePie_Item  $a  The first item to compare.
	 * @param   SimplePie_Item  $b  The second item to compare.
	 * @return  int             1 if b is greater, -1 if a is greater, 0 if matched.
	 */
	private function sort_rss_items_reverse_order( $a, $b )
	{
		$a_date = $a->get_date('U');
		$b_date = $b->get_date('U');
		
		if( $a_date == $b_date ) return 0;
		
		if( $a_date > $b_date ) return -1;
		
		return 1;
	}


	/**
	 * Sort in alphabetical order.
	 * @param   SimplePie_Item  $a  The first item to compare.
	 * @param   SimplePie_Item  $b  The second item to compare.
	 * @return  int             1 if b is greater, -1 if a is greater, 0 if matched.
	 */
	private function sort_rss_items_a_z( $a, $b )
	{
		$a_title = $a->get_title();
		$b_title = $b->get_title();
		
		if( $a_title == $b_title )
		{
			$a_date = $a->get_date('U');
			$b_date = $b->get_date('U');

			if( $a_date == $b_date ) return 0;
		
			if( $a_date < $b_date ) return -1;
		
			return 1;
		}
		
		if( $a_title > $b_title ) return 1;
		
		return -1;
	}
	
	
	/**
	 * Sort in reverse alphabetical order.
	 * @param   SimplePie_Item  $a  The first item to compare.
	 * @param   SimplePie_Item  $b  The second item to compare.
	 * @return  int             1 if b is greater, -1 if a is greater, 0 if matched.
	 */
	private function sort_rss_items_z_a( $a, $b )
	{
		$a_title = $a->get_title();
		$b_title = $b->get_title();
		
		if( $a_title == $b_title )
		{
			$a_date = $a->get_date('U');
			$b_date = $b->get_date('U');

			if( $a_date == $b_date ) return 0;
		
			if( $a_date < $b_date ) return 1;
		
			return -1;
		}
		
		if( $a_title > $b_title ) return -1;
		
		return 1;
	}
	
}
endif;

