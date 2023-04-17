<?php
$post_w_id = get_the_ID(); //list post id
$tags_post = wp_get_post_tags($post_w_id); //array list tags
$cats_post = wp_get_post_categories($post_w_id, [ 'fields' => 'all' ]); //array list cats
$cats_post_curr = wp_get_post_categories($current_id_post, [ 'fields' => 'all' ]); //array cats current post 
$tags_post_ids = array(); //tags id list post
$tags_post_name = array(); //tags name list post
$cats_post_ids = array(); //cats id list post
$cats_post_name = array();  //cats name list post
$cats_post_curr_name = array(); //cats name current post

foreach ($tags_post as $individual_tag){
  $tags_post_ids[] = $individual_tag->term_id;
  $tags_post_name[] = $individual_tag->name;
}
foreach ($cats_post as $individual_cat){
  $cats_post_ids[] = $individual_cat->term_id;
  $cats_post_name[] = $individual_cat->name;
}
foreach ($cats_post_curr as $individual_cat){
  // $cats_post_ids[] = $individual_cat->term_id;
  $cats_post_curr_name[] = $individual_cat->name;
}

// print_r(array_intersect($tag_ids, $tags_post_ids));

$size_thumb = 'medium';

$thumb_id = get_post_thumbnail_id($post_w_id);
// print_r(image_downsize($thumb_id, 'widget_post_thumb'));
if( image_downsize($thumb_id, 'widget_post_thumb')[3] ){
  $size_thumb = 'widget_post_thumb';
}

 
// print_r($cats);
?>
<li class="post-box <?= $style ?>-small" name="<?php do_action('title_version_wp_post_id'); ?>">

  <?php if( 'on' == $instance[ 'showThumbnails' ] ) : ?>

  <div class="post-img">
    <a href="<?= esc_url( get_the_permalink() ) ?>">
      <?php the_post_thumbnail( $size_thumb ) ?>
    </a>
  </div>

  <?php endif; ?>

  <div class="post-data">
    <div class="post-data-container">
      <div class="post-title">
        <a href="<?php the_permalink() ?>">
          <?php do_action('title_wp_post_id') ?>
        </a>
      </div>
      <div class="post-info">

        <?php if( 'on' == $instance[ 'showPostDate' ] ) : ?>
        <span class="thetime updated"><i class="fa fa-clock-o"></i> <?= get_the_date( 'F d, Y' ) ?></span>
        <?php endif; ?>

        <?php if( 'on' == $instance[ 'showComments' ] ) : ?>
        <span class="thecomment"><i class="fa fa-comments"></i>
          <?= wp_count_comments(get_the_ID())->approved ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php if( ('on' == $instance[ 'showMetaDebug' ]) && is_user_logged_in() ) : ?>
  <table class="meta_debug">
    <tr>
      <td>Current Post ID:</td>
      <td><?= $current_id_post ?></td>
    </tr>
    <tr>
      <td>Post ID:</td>
      <td><?= $post_w_id ?></td>
    </tr>
    <tr>
      <td>Current post tags</td>
      <td><?= implode(', ', $tag_name) ?></td>
    </tr>
    <tr>
      <td>Post tags:</td>
      <td><?= implode(', ', $tags_post_name) ?></td>
    </tr>
    <tr>
      <td>Number of tag matches</td>
      <td><?= count(array_intersect($tag_ids, $tags_post_ids)) ?></td>
    </tr>
    <tr>
      <td>Current post cats</td>
      <td><?= implode(', ', $cats_post_curr_name) ?></td>
    </tr>

    <tr>
      <td>Post categories:</td>
      <td><?= implode(', ', $cats_post_name) ?></td>
    </tr>
    <tr>
      <td>Number of category matches</td>
      <td><?= count(array_intersect($cats, $cats_post_ids)) ?></td>
    </tr>

    <tr>
      <td>List IDs:</td>
      <td><?= in_array( $post_w_id, $IDs ) ? 'YES' : 'NO' ?></td>
    </tr>
  </table>
  <?php endif; ?>

</li>
<?php array_push($IDs_not, $post_w_id); $i++; 