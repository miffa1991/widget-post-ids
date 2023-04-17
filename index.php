<?php
/*
Plugin Name: Posts by id
Description: Display posts by id
* Version: 2.2
* Author: Miffka
*/

if ( ! defined( 'ABSPATH' ) ) {
	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

class Miff_IDs_Post extends WP_Widget {

	public function __construct() {
		// actual widget processes
		parent::__construct(
			'miff_ids_post', // Base ID
			'Posts by ID ', // Name
			array( 'description' => __( 'Display posts in sidebar widget.', 'miffka_ids_post' ), 'classname' => 'miffka_ids_post-widget') // Args
		);
	}
  
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$ids = $instance['ids'];
		$style = $instance['styleList'];
    $showThumbnails = $instance[ 'showThumbnails' ] ? 'true' : 'false';
    $showComments = $instance[ 'showComments' ] ? 'true' : 'false';
    $showPostDate = $instance[ 'showPostDate' ] ? 'true' : 'false';
    $showMetaDebug = $instance[ 'showMetaDebug' ] ? 'true' : 'false';
    
    $number_posts_per_page = $instance['number_posts_per_page'] ? $instance['number_posts_per_page'] : 0; 
    $start_date = $instance['start_date'] ? $instance['start_date'] : 2012-01-01; 
		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo '<h3 class="widget-title">' . $title . '</h3>';
		}
    $IDs = null;
    $IDs_not = array();
    $tag_ids = array();
    $tag_name = array();
    


    ?>
<ul class="category-posts">
  <?php
    if($ids){
      $IDs = explode(',', $ids );
    }
    $current_id_post = get_the_ID();
    array_push($IDs_not, $current_id_post);
    foreach($IDs as $key => $item){
        if ( $item == $current_id_post ){
          unset($IDs[$key]);
        }
    }
    
    $tags = wp_get_post_tags($current_id_post);
    $cats = wp_get_post_categories($current_id_post);
    // print_r($cats);
    
    foreach ($tags as $individual_tag){
      $tag_ids[] = $individual_tag->term_id;
      $tag_name[] = $individual_tag->name;
    }


    $posts = array(
      'post_type' => 'post',
      'posts_per_page' => $number_posts_per_page,
      'orderby' => 'rand',
      // 'tax_query' => array(
      //   'relation' => 'AND',
      //   array(
      //       'taxonomy' => 'category',
      //       'field' => 'term_id',
      //       'terms' => $cats,
      //       'include_children' => false 
      //   ),
      //   array(
      //       'taxonomy' => 'post_tag',
      //       'field' => 'term_id',
      //       'terms' => $tag_ids,
      //   )
      //   ),
      'date_query' => [
        'after' => $start_date,
      ],
    );
    
    if($IDs){
      $posts['post__in'] =  $IDs;
    }
    
    if($tag_ids){
      $posts['tag__in'] = $tag_ids; 
    }
    
    // print_r($tag_ids);

    // if($cats){
    //   $posts['category__in'] = $cats; 
    // }

    $i = 1;
    query_posts($posts);
    if ( have_posts() ) :
    ?>

  <?php	while ( have_posts() ) :
				the_post();
        if( in_array( get_the_ID(), $IDs_not ) ){
          continue;
        }
        include( plugin_dir_path( __FILE__ ) . 'widget-item-post.php');
        endwhile; 
endif;
wp_reset_query();

if( $i <= $number_posts_per_page ){

  $cats = wp_get_post_categories($current_id_post);

  $posts = array(
    'post_type' => 'post',
    'posts_per_page' => $number_posts_per_page,
    'orderby' => 'rand',
    // 'tax_query' => array(
    //   'relation' => 'OR',
    //   array(
    //       'taxonomy' => 'category',
    //       'field' => 'term_id',
    //       'terms' => $cats,
    //       'include_children' => false 
    //   )
    //   ),
    'date_query' => [
      'after' => $start_date,
    ],
  );
  
  if($IDs){
    $posts['post__in'] =  $IDs;
  }
  $cat_in_cur = !empty($cats) ? $cats[0] : 0;

  foreach($cats as $cat){
    if( count_cat_post($cat) < count_cat_post($cat_in_cur) ){
      $cat_in_cur = $cat;
    }
  }
  if($cats){
    $posts['category__in'] = $cat_in_cur; 
  }
  

  query_posts($posts);
  if ( have_posts() ) :
	while ( have_posts() ) :
    the_post();
      if( in_array( get_the_ID(), $IDs_not ) ){
        continue;
      }
      include( plugin_dir_path( __FILE__ ) . 'widget-item-post.php');
      endwhile; 
  endif;
  wp_reset_query();
}

if( $i <= $number_posts_per_page ){
  
  $posts = array(
    'post_type' => 'post',
    'posts_per_page' => $number_posts_per_page - $i + 1,
    // 'post__not_in' =>  $IDs_not,
    // 'tag__in' => $tag_ids,
    'date_query' => [
      'after' => $start_date,
    ],
  );
  if($tag_ids){
    $posts['tag__in'] = $tag_ids; 
  }
  query_posts($posts);
  if ( have_posts() ) :
  	while ( have_posts() ) :
      the_post();
      if( in_array( get_the_ID(), $IDs_not ) ){
        continue;
      }
      include( plugin_dir_path( __FILE__ ) . 'widget-item-post.php');
    endwhile; 
  endif;
  wp_reset_query();
  
} ?>

</ul>
<?php

echo $after_widget;
	}

	public function form( $instance ) {
		// outputs the options form in the admin
		if ( isset( $instance ) ) {
			$title = $instance[ 'title' ];
      $ids = $instance[ 'ids' ];
      $number_posts_per_page = $instance[ 'number_posts_per_page' ];
      // $styleList = $instance[ 'styleList' ];
      // $styleList = isset( $instance['styleList'] ) ? $instance['styleList'] : '';
		}
		else {

			$title = __( '', 'miffka_ids_post' );
      $ids = __( '', 'miffka_ids_post' );
      $styleList = __( '', 'miffka_ids_post' );
      $instance[ 'showThumbnails' ] = 'off';
      $instance[ 'showPostDate' ] = 'off';
      $instance[ 'showComments' ] = 'off';
      $instance[ 'showMetaDebug' ] = 'off';
      
      $instance[ 'number_posts_per_page' ] = 5;
      $instance['start_date'] = '2012-01-01';
		}
		?>

<p>
  <label for="<?= $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
  <input class="widefat" id="<?= $this->get_field_id( 'title' ); ?>" name="<?= $this->get_field_name( 'title' ); ?>"
    type="text" value="<?= esc_attr( $title ); ?>" />
</p>
<p>
  <label
    for="<?= $this->get_field_name( 'number_posts_per_page' ); ?>"><?php _e( 'Number of Posts to show:' ); ?></label>
  <input class="number" id="<?= $this->get_field_id( 'number_posts_per_page' ); ?>"
    name="<?= $this->get_field_name( 'number_posts_per_page' ); ?>" type="number" min="0" max="30"
    value="<?= esc_attr( $number_posts_per_page ); ?>" />
</p>
<!-- <br> -->

<p>
  <label for="<?= $this->get_field_name( 'ids' ); ?>"><?php _e( 'IDs' ); ?></label>
  <textarea class="widefat" rows="16" cols="20" id="<?= $this->get_field_id( 'ids' ); ?>"
    name="<?= $this->get_field_name( 'ids' ); ?>"><?= $ids ?></textarea>
</p>

<!-- <br> -->
<p>
  <label for="<?= $this->get_field_id('styleList'); ?>"><?php _e('Style widget:', 'miffka_ids_post'); ?></label>
  <select id="<?= $this->get_field_id('styleList'); ?>" name="<?= $this->get_field_name('styleList'); ?>">
    <option value="<?= 'horizontal' ?>" <?php selected( $instance['styleList'] , 'horizontal') ?>>
      <?= 'Horizontal' ?>
    </option>
    <option value="<?= 'vertical' ?>" <?php selected( $instance[ 'styleList' ], 'vertical') ?>>
      <?= 'Vertical' ?>
    </option>
  </select>
</p>

<!-- <br> -->

<p>
  <input class="checkbox" id="<?= $this->get_field_id( 'showThumbnails' ); ?>" type="checkbox"
    <?php checked( $instance[ 'showThumbnails' ], 'on' ); ?> name="<?= $this->get_field_name( 'showThumbnails' ); ?>" />
  <label for="<?= $this->get_field_id('showThumbnails'); ?>">
    <?php _e('Show Thumbnails', 'miffka_ids_post'); ?>
  </label>
</p>

<!-- <br> -->

<p>
  <input class="checkbox" id="<?= $this->get_field_id( 'showPostDate' ); ?>" type="checkbox"
    <?php checked( $instance[ 'showPostDate' ], 'on' ); ?> name="<?= $this->get_field_name( 'showPostDate' ); ?>" />
  <label for="<?= $this->get_field_id('showPostDate'); ?>">
    <?php _e('Show post date', 'miffka_ids_post'); ?>
  </label>
</p>

<!-- <br> -->

<p>
  <input class="checkbox" id="<?= $this->get_field_id( 'showComments' ); ?>" type="checkbox"
    <?php checked( $instance[ 'showComments' ], 'on' ); ?> name="<?= $this->get_field_name( 'showComments' ); ?>" />
  <label for="<?= $this->get_field_id('showComments'); ?>">
    <?php _e('Show number of comments', 'miffka_ids_post'); ?>
  </label>
</p>

<p>
  <input class="checkbox" id="<?= $this->get_field_id( 'showMetaDebug' ); ?>" type="checkbox"
    <?php checked( $instance[ 'showMetaDebug' ], 'on' ); ?> name="<?= $this->get_field_name( 'showMetaDebug' ); ?>" />
  <label for="<?= $this->get_field_id('showMetaDebug'); ?>">
    <?php _e('Show meta debug', 'miffka_ids_post'); ?>
  </label>
</p>

<!-- <br> -->

<p>
  <label for="<?= $this->get_field_id('start_date'); ?>">
    <?php _e('Date after which to show posts ', 'miffka_ids_post'); ?>
  </label>
  <input type="date" id="<?= $this->get_field_id( 'start_date' ); ?>"
    name="<?= $this->get_field_name( 'start_date' ); ?>" value="<?= $instance['start_date'] ?>" min="2012-01-01"
    max="2050-12-31">

</p>

<?php /*print_r($showThumbnails )*/ ?>

<?php
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = array();
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['number_posts_per_page'] = ( !empty( $new_instance['number_posts_per_page'] ) ) ? strip_tags( $new_instance['number_posts_per_page'] ) : 0;
    $instance['start_date'] = $new_instance['start_date'];
		$instance['ids'] = ( !empty( $new_instance['ids'] ) ) ? strip_tags( $new_instance['ids'] ) : '';
		$instance['styleList'] = $new_instance['styleList'];
		$instance['showThumbnails'] = $new_instance['showThumbnails'];
		$instance['showComments'] = $new_instance['showComments'];
		$instance['showMetaDebug'] = $new_instance['showMetaDebug'];
    
		$instance['showPostDate'] = $new_instance['showPostDate'];

    
		return $instance;
	}
	
}

function count_cat_post($category) {
  if(is_string($category)) {
      $catID = get_cat_ID($category);
  }
  elseif(is_numeric($category)) {
      $catID = $category;
  } else {
      return 0;
  }
  $cat = get_category($catID);
  return $cat->count;
}




// Register Foo_Widget widget
add_action( 'widgets_init', 'register_miffka_id_posts' );

function register_miffka_id_posts() {
	register_widget( 'Miff_IDs_Post' );
}


function miffka_posts_id_style() {
  wp_register_style('miffka_posts_id', plugins_url('style.css',__FILE__ ));
  wp_enqueue_style('miffka_posts_id');
}

add_action( 'admin_init','miffka_posts_id_style');
add_action('init', 'miffka_posts_id_style');

add_action( 'title_wp_post_id', 'title_wp_post_id_widget', 10 );

function title_wp_post_id_widget(){
  echo get_the_title();
}

add_action( 'title_version_wp_post_id', 'title_v_wp_post_id_widget', 10 );

function title_v_wp_post_id_widget(){
  echo 'get_title';
}

add_action( 'after_setup_theme', 'thumb_widget_reg' );
function thumb_widget_reg() {
    add_image_size( 'widget_post_thumb', 344, 215, true ); 
}