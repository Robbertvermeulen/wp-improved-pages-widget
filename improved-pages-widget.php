<?php
/**
 * Plugin Name: Improved pages widget
 * Description: Adds a pages pages widget with dropdown for including pages instead of excluding.
 * Version: 1.0.0
 * Author: Robbert Vermeulen
 * Author URI: https://dev.robbertvermeulen.com
 * Text Domain: improved-pages-widget-plugin
 * License: GPL2
 */

/**
 * Registers the improved pages widget
 */
function add_improved_pages_widget() {
   register_widget( 'Improved_Pages_Widget' );
}
add_action( 'widget_init', 'add_improved_pages_widget' );

/**
 * Widget API: WP_Widget_Pages class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Improved pages widget with a dropdown for including pages
 * insted of excluding.
 *
 * @see WP_Widget
 */
class Improved_Pages_Widget extends WP_Widget {

   /**
	 * Sets up a new Pages widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function __construct() {
   	$widget_ops = array(
   		'description' => __( 'A list of your site&#8217;s Pages.' ),
   	);
   	parent::__construct( 'improved_pages', __( 'Pages' ), $widget_ops );
	}


   /**
    * Outputs the content for the current Pages widget instance.
    *
    * @param array $args     Display arguments including 'before_title', 'after_title',
    *                        'before_widget', and 'after_widget'.
    * @param array $instance Settings for the current Pages widget instance.
    */
   public function widget( $args, $instance ) {

      /**
       * Filters the widget title.
       *
       * @param string $title    The widget title. Default 'Pages'.
       * @param array  $instance An array of the widget's settings.
       * @param mixed  $id_base  The widget ID.
       */
      $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Pages' ) : $instance['title'], $instance, $this->id_base );

      $sortby 	= empty( $instance['sortby'] ) ? 'menu_order' : $instance['sortby'];
      $include = empty( $instance['include'] ) ? '' : $instance['include'];

      if ( $sortby == 'menu_order' )
         $sortby = 'menu_order, post_title';

      /**
       * Filters the arguments for the Pages widget.
       *
       * @since 2.8.0
       *
       * @see wp_list_pages()
       *
       * @param array $args An array of arguments to retrieve the pages list.
       */
      $out = wp_list_pages( apply_filters( 'widget_pages_args', array(
         'title_li'    => '',
         'echo'        => 0,
         'sort_column' => $sortby,
         'include'     => $include // Include instead of exclude
      ) ) );

      if ( ! empty( $out ) ) {
         echo $args['before_widget'];
         if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
         }
         ?>

         <ul>
            <?php echo $out; ?>
         </ul>
         <?php
         echo $args['after_widget'];
      }
   }

   /**
	 * Handles updating settings for the current Pages widget instance.
	 *
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		if ( in_array( $new_instance['sortby'], array( 'post_title', 'menu_order', 'ID' ) ) ) {
			$instance['sortby'] = $new_instance['sortby'];
		} else {
			$instance['sortby'] = 'menu_order';
		}

		$instance['include'] = $new_instance['include'];

		return $instance;
	}

	/**
	 * Outputs the settings form for the Pages widget.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {

		// Defaults
		$instance = wp_parse_args( (array) $instance, array( 'sortby' => 'post_title', 'title' => '', 'include' => '') );

		// Pages
		$pages = get_pages();
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sortby' ) ); ?>"><?php _e( 'Sort by:' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'sortby' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'sortby' ) ); ?>" class="widefat">
				<option value="post_title"<?php selected( $instance['sortby'], 'post_title' ); ?>><?php _e('Page title'); ?></option>
				<option value="menu_order"<?php selected( $instance['sortby'], 'menu_order' ); ?>><?php _e('Page order'); ?></option>
				<option value="ID"<?php selected( $instance['sortby'], 'ID' ); ?>><?php _e( 'Page ID' ); ?></option>
			</select>
		</p>
		<p>
			<label><?php _e( 'Include:' ); ?></label>

			<?php if ( ! empty( $pages ) ) { ?>
				<div class="wp-tab-panel">
					<ul>
						<?php foreach ( $pages as $page ) {
							$checked = ( is_array( $instance['include'] ) && in_array( $page->ID, $instance['include'] ) ) ? 'checked' : '';
							?>
							<li><label><input type="checkbox" name="<?php echo $this->get_field_name( 'include' ) . '[]'; ?>" id="<?php echo $this->get_field_id( 'include' ); ?>" value="<?php echo $page->ID; ?>" <?php echo $checked; ?>> <?php echo $page->post_title; ?></label></li>
						<?php } ?>
					</ul>
				</div>
			<?php } ?>
		</p>
		<?php
	}

}

?>
