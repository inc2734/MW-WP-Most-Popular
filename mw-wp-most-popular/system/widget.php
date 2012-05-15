<?php
class MW_WMP_Widget extends WP_Widget {

	public $defaults = array(
		'title' => 'Popular posts',
		'number' => 5,
		'timeline' => 'all',
		'post_types' => array( 'post' ),
		'show_thumbnail' => '',
		'show_fb_like' => ''
	);

	/**
	 * __construct
	 */
	public function __construct() {
		parent::WP_Widget( 'MW_WMP_widget', 'MW WP Most Popular', array(
			'classname' => 'mw_wp_most_popular',
			'description' => 'Display your most popular blog posts on your sidebar'
		) );
	}

	/**
	 * form
	 */
	public function form( $instance ) {
		$instance = $this->_default_options( $instance );
		?>
		<p>
			<?php
			$title_field_id = $this->get_field_id( 'title' );
			$title_field_name = $this->get_field_name( 'title' );
			?>
			<label for="<?php echo $title_field_id; ?>">Title:</label>
			<input class="widefat" id="<?php echo $title_field_id; ?>" name="<?php echo $title_field_name; ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<?php
			$number_field_id = $this->get_field_id( 'number' );
			$number_field_name = $this->get_field_name( 'number' );
			?>
			<label for="<?php echo $number_field_id; ?>">Number of posts to show:</label>
			<input id="<?php echo $number_field_id; ?>" name="<?php echo $number_field_name; ?>" type="text" value="<?php echo $instance['number']; ?>" size="3" />
		</p>
		<p>
			<?php
			$timeline_field_id = $this->get_field_id( 'timeline' );
			$timeline_field_name = $this->get_field_name( 'timeline' );
			?>
			<label for="<?php echo $timeline_field_id; ?>">Timeline:</label>
			<select id="<?php echo $timeline_field_id; ?>" name="<?php echo $timeline_field_name; ?>">
				<option value="all_time"<?php selected( $instance['timeline'], 'all_time' ); ?>>All time</option>
				<option value="monthly"<?php selected( $instance['timeline'], 'monthly' ); ?>>Past month</option>
				<option value="weekly"<?php selected( $instance['timeline'], 'weekly' ); ?>>Past week</option>
				<option value="daily"<?php selected( $instance['timeline'], 'daily' ); ?>>Today</option>
			</select>
		</p>
		<?php
		$post_types = get_post_types( array(
			'public'   => true,
			'_builtin' => false
		), 'objects', 'and' );
		array_unshift( $post_types, get_post_type_object( 'post' ), get_post_type_object( 'page' ) );
		$post_types_field_id = $this->get_field_id( 'post_types' );
		$post_types_field_name = $this->get_field_name( 'post_types' ).'[]';
		?>
		<p>
			Post Types:
			<?php foreach ( $post_types as $post_type ) : ?>
			<?php $checked = ( in_array( $post_type->name, $instance['post_types'] ) ) ? ' checked="checked"' : ''; ?>
			<label style="display:block"><input type="checkbox" value="<?php echo $post_type->name; ?>"<?php echo $checked; ?> id="<?php echo $post_types_field_id; ?>" name="<?php echo $post_types_field_name; ?>" /> <?php echo $post_type->label; ?></label>
			<?php endforeach; ?>
		</p>
		<p>
			<?php
			$show_thumbnail_field_id = $this->get_field_id( 'show_thumbnail' );
			$show_thumbnail_field_name = $this->get_field_name( 'show_thumbnail' );
			?>
			<input type="checkbox" value="true"<?php checked( $instance['show_thumbnail'], 'true' ); ?> id="<?php echo $show_thumbnail_field_id; ?>" name="<?php echo $show_thumbnail_field_name; ?>" />
			<label for="<?php echo $show_thumbnail_field_id; ?>">Show thumbnail</label>
		</p>
		<p>
			<?php
			$show_fb_like_field_id = $this->get_field_id( 'show_fb_like' );
			$show_fb_like_field_name = $this->get_field_name( 'show_fb_like' );
			?>
			<input type="checkbox" value="true"<?php checked( $instance['show_fb_like'], 'true' ); ?> id="<?php echo $show_fb_like_field_id; ?>" name="<?php echo $show_fb_like_field_name; ?>" />
			<label for="<?php echo $show_fb_like_field_id; ?>">Show Facebook Like Button</label>
		</p>
		<?php
	}

	/**
	 * update
	 */
	public function update( $new, $old ) {
		if ( !isset( $new['post_types'] ) ) {
			$new['post_types'] = $this->defaults['post_types'];
		}
		if ( !isset( $new['show_thumbnail'] ) ) {
			$new['show_thumbnail'] = $this->defaults['show_thumbnail'];
		}
		if ( !isset( $new['show_fb_like'] ) ) {
			$new['show_fb_like'] = $this->defaults['show_fb_like'];
		}
		$instance = wp_parse_args( $new, $old );
		return $instance;
	}

	/**
	 * widget
	 */
	public function widget( $args, $instance ) {
		// Find default args
		extract( $args );
		
		// Get our posts
		$defaults	= $this->_default_options( $instance );
		$posts		= MW_WMP_get_popular( array(
			'limit' => (int) $defaults[ 'number' ],
			'range' => $defaults['timeline'],
			'post_type' => $defaults['post_types']
		) );
		
		// Display the widget
		echo $before_widget;
		if ( $defaults['title'] ) echo $before_title . $defaults['title'] . $after_title;
		echo '<ul>';
		global $post;
		foreach ( $posts as $post ):
			setup_postdata( $post );
			?>
			<li>
				<?php if ( $defaults['show_thumbnail'] && has_post_thumbnail() ) : ?>
				<span class="thumbnail">
					<a href="<?php the_permalink() ?>"><?php the_post_thumbnail(); ?></a>
				</span>
				<?php endif; ?>
				<span class="text">
					<a href="<?php the_permalink() ?>"><?php the_title(); ?></a>
				</span>
				<?php if ( $defaults['show_fb_like'] ) : ?>
				<span class="likeButton">
					<div class="fb-like" data-href="<?php the_permalink(); ?>" data-send="false" data-show-faces="false" data-layout="button_count"></div>
				</span>
				<?php endif; ?>
			</li>
			<?php
		endforeach;
		echo '</ul>';
		echo $after_widget;
		
		// Reset post data
		wp_reset_postdata();
	}
	
	/**
	 * default_options
	 */
	private function _default_options( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		return $instance;
	}
}