<?php 
$categories = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );
$carousel_attrs = array(
  'items'               => $items_lg,
  'items_lg'            => $items_lg,
  'items_md'            => $items_md,
  'items_sm'            => $items_sm,
  'items_xs'            => $items_xs,
  'loop'                => $ca_loop,
  'speed'               => $ca_speed,
  'auto_play'           => $ca_auto_play,
  'auto_play_speed'     => $ca_auto_play_speed,
  'auto_play_timeout'   => $ca_auto_play_timeout,
  'auto_play_hover'     => $ca_play_hover,
  'navigation'          => $ca_navigation,
  'rewind_nav'          => $ca_rewind_nav,
  'pagination'          => $ca_pagination,
  'mouse_drag'          => $ca_mouse_drag,
  'touch_drag'          => $ca_touch_drag
);
$args = array(
   'post_type'          => 'testimonials',
   'post_status'       => 'publish',
   'ignore_sticky_posts'  => true,
   'posts_per_page'       => $per_page,
   'orderby'           => 'date',
   'order'             => 'desc'
);

if( is_array($categories) && is_numeric($categories) ){
   $args['tax_query'] = array(
      array(
         'taxonomy' => 'testimonial_cat',
         'terms' => $categories,
         'field' => 'term_id',
         'include_children' => false
      )
   );
}

$loop = new WP_Query($args);
?>
<div class="widget vc-widget gva-testimonials avata-<?php echo esc_attr($show_avatar) ?> <?php echo esc_attr($style_display) ?> <?php echo esc_attr($text_color_style) ?> <?php echo esc_attr($el_class) ?>">
   <div class="products carousel-view">
      <div class="init-carousel-owl owl-carousel" <?php echo kiamo_set_carousel_attrs($carousel_attrs) ?>>
        <?php $i=0; while ( $loop->have_posts() ) : $loop->the_post(); $i++; ?>
          <?php 
            $job = get_post_meta(get_the_ID(), 'kiamo_testimonial_job', true );
            $video = get_post_meta(get_the_ID(), 'kiamo_testimonial_video', true );
            $images = rwmb_meta( 'kiamo_testimonial_image', array( 'size'=>'medium', 'limit' => 1 ) );
            $image = reset( $images );
          ?>

          <?php if($style_display == 'default'){ ?>
            <div class="testimonial-item testimonial-v1">
              <div class="testimonial-content">
                <div class="content">
                  <div class="avatar">
                    <?php the_post_thumbnail('full' ); ?>
                  </div>
                  <div class="quote"><?php the_content(); ?></div>  
                  <div class="info">
                    <div class="title"><?php the_title(); ?></div>
                    <div class="job"><?php echo esc_html($job); ?></div>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
         
          <?php if($style_display == 'style-v2'){ ?>
            <div class="item">
               <article class="testimonial-item testimonial-node-v1">      
                  <?php if(isset($image['url']) || $video){ ?>
                    <div class="testimonial-image">
                      <img src="<?php echo esc_url($image['url']); ?>" alt="<?php the_title(); ?>">
                      <?php if($video){ ?>
                        <div class="video-body">
                           <a class="popup-video gsc-video-link" href="<?php echo esc_url($video); ?>">
                              <span class="icon-play"><i class="fa fa-play space-40"></i></span>
                           </a>
                        </div>
                      <?php } ?>
                    </div>
                  <?php } ?>
                  <div class="testimonial-content">
                     <div class="quote"><?php echo get_the_content() ?></div>  
                     <div class="info">
                        <div class="left"><div class="avatar"> <?php the_post_thumbnail('thumbnail' ); ?></div></div>
                        <div class="right">
                           <div class="title"><?php the_title(); ?></div>  
                           <div class="job"><?php echo esc_html($job); ?></div>
                        </div>
                     </div>  
                  </div>

               </article>
            </div>
           <?php } ?>

           <?php if($style_display == 'style-v3'){ ?>
            <div class="item">
               <article class="testimonial-item testimonial-node-v4"> 
                  <div class="testimonial-content">
                    <div class="content">
                      <div class="content-inner">
                        <div class="quote"><?php echo get_the_content() ?></div>  
                        <div class="info clearfix">
                          <div class="avatar"> <?php the_post_thumbnail('thumbnail' ); ?></div>
                          <div class="title"><?php the_title(); ?></div>  
                          <div class="job"><?php echo esc_html($job); ?></div>
                        </div> 
                      </div>
                    </div>     
                  </div>
               </article>
            </div>
           <?php } ?>

         <?php endwhile; 
         wp_reset_postdata();
         ?>
      </div>
   </div>
</div>