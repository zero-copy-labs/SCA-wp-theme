<?php
if(!function_exists('kiamo_id')){
  function kiamo_id(){
    if(kiamo_woocommerce_activated() && is_shop()){
      $pid = wc_get_page_id('shop');
    }elseif(is_page() || is_singular()){
      $pid = get_the_ID();
    }else{
      $pid = '';
    }
    return $pid;
  }
}

if(!function_exists('kiamo_get_footer')){
  function kiamo_get_footer(){
    $footers = array('disable-footer' => esc_html__('Disable Footer', 'kiamo') );
    $footers['default-option-theme'] = esc_html__('Default Option Theme', 'kiamo');
    if(class_exists('Gavias_Footer')){
      $footers = $footers + Gavias_Footer::get_footers();
    }  
    return $footers;
  }
}  

if(!function_exists('kiamo_get_headers')){
  function kiamo_get_headers(){
    $path = get_template_directory().'/header-*.php';
    $files = glob( $path  );
    $headers = array('default-option-theme' => esc_html__('Default Option Theme', 'kiamo') );
    $headers['v__'] = esc_html__('Default', 'kiamo');
    if( count($files)>0 ){
      foreach ($files as $key => $file) {
        $name = str_replace( "header-", '', str_replace( '.php', '', basename($file) ) );
        $headers[$name] = esc_html__( 'Header', 'kiamo' ) . ' ' .str_replace( '-',' ', ucfirst( $name ) );
        }
    }
    return $headers;
  }
}

if(!function_exists('kiamo_general_breadcrumbs')){
  function kiamo_general_breadcrumbs() {

    $delimiter = ' / ';
    $home = 'Home';
    $before = '<li class="active">';
    $after = '</li>';
    $breadcrumb = '';
    $page_title = '';
    if (!is_home() && !is_front_page() || is_paged()) {

      $breadcrumb .= '<ol class="breadcrumb">';

      global $post;
      $homeLink = home_url();
      $breadcrumb .= '<li><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . '</li> ';

      if (is_category()) {
        
        global $wp_query;
        $cat_obj = $wp_query->get_queried_object();
        $thisCat = $cat_obj->term_id;
        $thisCat = get_category($thisCat);
        $parentCat = get_category($thisCat->parent);
        if ($thisCat->parent != 0) $breadcrumb .= (get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
        $breadcrumb .= ($before) . single_cat_title('', false) . $after;
        $page_title = single_cat_title('', false );
     
      } elseif (is_day()) {
        
        $breadcrumb .= '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li> ' . $delimiter . ' ';
        $breadcrumb .= '<li><a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a></li> ' . $delimiter . ' ';
        $breadcrumb .= ($before) . get_the_time('d') . $after;
        $page_title = get_the_time('d');
     
      } elseif (is_month()) {
        
        $breadcrumb .= '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li> ' . $delimiter . ' ';
        $breadcrumb .= ($before) . get_the_time('F') . $after;
        $page_title = get_the_time('F');
     
      } elseif (is_year()) {
       
        $breadcrumb .= ($before) . get_the_time('Y') . $after;
        $page_title = get_the_time('Y');
      
      }elseif ( is_search() || get_query_var('s') ) {

        $breadcrumb .= ($before) . 'Search results for "' . get_search_query() . '"' . $after;
        $page_title = get_search_query();

      } elseif (is_single() && !is_attachment()) {
        
        if ( get_post_type() != 'post' ) {
          $breadcrumb .= ($before) . get_the_title() . $after;
          $page_title = get_the_title();
        } else {
          $cat = get_the_category(); $cat = $cat[0];
          $breadcrumb .= get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
          $breadcrumb .= ($before) . get_the_title() . $after;
          $page_title = get_the_title();
        }

      } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
        
        $post_type = get_post_type_object(get_post_type());
        if( $post_type ){
          $breadcrumb .= ($before) . $post_type->labels->singular_name . $after;
          $page_title = $post_type->labels->singular_name;
        }

      } elseif (is_attachment()) {

        $parent = get_post($post->post_parent);
        $cat = get_the_category($parent->ID); 
        if(isset($cat[0]) && $cat[0]){
          $cat = $cat[0];
          $breadcrumb .= get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
        }
        $breadcrumb .= '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a></li> ' . $delimiter . ' ';
        $breadcrumb .= ($before) . get_the_title() . $after;
        $page_title = get_the_title();

      } elseif ( is_page() && !$post->post_parent ) {
        
        $breadcrumb .= ($before) . get_the_title() . $after;
        $page_title = get_the_title();

      } elseif ( is_page() && $post->post_parent ) {

        $parent_id  = $post->post_parent;
        $breadcrumbs = array();
        while ($parent_id) {
          $page = get_page($parent_id);
          $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
          $parent_id  = $page->post_parent;
        }
        $breadcrumbs = array_reverse($breadcrumbs);
        foreach ($breadcrumbs as $crumb) $breadcrumb .= ($crumb) . ' ' . $delimiter . ' ';
        $breadcrumb .= ($before) . get_the_title() . $after;
        $page_title = get_the_title();

      }  elseif ( is_tag() ) {

        $breadcrumb .= ($before) . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;
        $page_title = single_tag_title('', false);

      } elseif ( is_author() ) {

        global $author;
        $userdata = get_userdata($author);
        if($userdata){
          $breadcrumb .= ($before) . 'Articles posted by ' . $userdata->display_name . $after;
          $page_title = $userdata->display_name;
        } 

      } elseif ( is_404() ) {

        $breadcrumb .= ($before) . 'Error 404' . $after;
        $page_title = 'Error 404';

      }

      if ( get_query_var('paged') ) {

        if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $breadcrumb .= ' (';
        $breadcrumb .= ': ' . esc_html__('Page','kiamo') . ' ' . get_query_var('paged');
        if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $breadcrumb .= ')';

      }

      $breadcrumb .= '</ol>';
      echo trim($breadcrumb);
    }
  }
}  

if ( ! function_exists( 'kiamo_comment_nav' ) ) :
/**
 * Display navigation to next/previous comments when applicable.
 *
 */
function kiamo_comment_nav() {
  // Are there comments to navigate through?
  if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
  ?>
  <nav class="navigation comment-navigation" role="navigation">
    <h2 class="screen-reader-text"><?php esc_html__( 'Comment navigation', 'kiamo' ); ?></h2>
    <div class="nav-links">
      <?php
        if ( $prev_link = get_previous_comments_link( esc_html__( 'Older Comments', 'kiamo' ) ) ) :
          printf( '<div class="nav-previous">%s</div>', $prev_link );
        endif;

        if ( $next_link = get_next_comments_link( esc_html__( 'Newer Comments', 'kiamo' ) ) ) :
          printf( '<div class="nav-next">%s</div>', $next_link );
        endif;
      ?>
    </div><!-- .nav-links -->
  </nav><!-- .comment-navigation -->
  <?php
  endif;
}
endif;

function kiamo_limit_words($word_limit, $string, $string_second = ''){
  if(empty($string)){
    $string = strip_tags($string_second);
  }
  $words = explode(' ', $string, ($word_limit + 1));
  if(count($words) > $word_limit)
  array_pop($words);
  return implode(' ', $words);
}

if(!function_exists('kiamo_get_options')){
  function kiamo_get_options(){
    global $kiamo_theme_options;
    return $kiamo_theme_options;
  }
}

if(!function_exists('kiamo_get_option')){
  function kiamo_get_option($key, $default = ''){
    global $kiamo_theme_options;
    if(isset($kiamo_theme_options[$key]) && $kiamo_theme_options[$key]){
      return $kiamo_theme_options[$key];
    }else{
      return $default;
    }
  }
}

if(!function_exists('kiamo_random_id')){
  function kiamo_random_id($length=4){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $string = '';
    for ($i = 0; $i < $length; $i++) {
      $string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $string;
  }
}  

if(!function_exists('kiamo_woocommerce_activated')){
  /* Check if WooCommerce is activated */
  function kiamo_woocommerce_activated() {
    if ( class_exists('WooCommerce') ) { 
      return true; 
    }
    return false;
  }
}

if(!function_exists('kiamo_convert_hextorgb')){
  function kiamo_convert_hextorgb($hex, $alpha = false) {
    $hex = str_replace('#', '', $hex);
    if ( strlen($hex) == 6 ) {
      $rgb['r'] = hexdec(substr($hex, 0, 2));
      $rgb['g'] = hexdec(substr($hex, 2, 2));
      $rgb['b'] = hexdec(substr($hex, 4, 2));
    }else if ( strlen($hex) == 3 ) {
      $rgb['r'] = hexdec(str_repeat(substr($hex, 0, 1), 2));
      $rgb['g'] = hexdec(str_repeat(substr($hex, 1, 1), 2));
      $rgb['b'] = hexdec(str_repeat(substr($hex, 2, 1), 2));
    }else {
      $rgb['r'] = '0';
      $rgb['g'] = '0';
      $rgb['b'] = '0';
     }
     if ( $alpha ) {
      $rgb['a'] = $alpha;
    }
    return $rgb;
  }
}

if(!function_exists('kiamo_set_carousel_attrs')){
  function kiamo_set_carousel_attrs($attrs = array()){
    $ouput = '';
    foreach ($attrs as $key => $value) {
      $ouput .= 'data-' . esc_attr( $key ) . '="' . esc_attr($value) . '" ';
    }
    return $ouput;
  }
}


function kiamo_responsive_settings(){
  return array(
    array(
      'type'           => 'dropdown',
      'heading'        => esc_html__( 'Responsive Setting | Items for large Screen', 'kiamo' ),
      "param_name"     => 'items_lg',
      'value'          => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6),
      'std'            => 4
    ),
    array(
      'type'           => 'dropdown',
      'heading'        => esc_html__( 'Responsive Setting | Items for Medium Screen', 'kiamo' ),
      "param_name"     => 'items_md',
      'value'          => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6),
      'std'            => 3
    ),
    array(
      'type'           => 'dropdown',
      'heading'        => esc_html__( 'Responsive Setting | Items for Small Screen', 'kiamo' ),
      "param_name"     => 'items_sm',
      'value'          => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6),
      'std'            => 2
    ),
    array(
      'type'           => 'dropdown',
      'heading'        => esc_html__( 'Responsive Setting | Items for Extra Small Screen', 'kiamo' ),
      "param_name"     => 'items_xs',
      'value'          => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6),
      'std'            => 2
    )
  );
}

function kiamo_carousel_settings(){
  return array(
    array(
      "type"          => "dropdown",
      "heading"       => esc_html__("Carousel Settings | Loop", 'kiamo'),
      "param_name"    => "ca_loop",
      "value"         => array(
        esc_html__("Enable", 'kiamo' ) => '1',
        esc_html__("Disable", 'kiamo' ) => '0'
      ),
    ),
    array(
      "type"          => "textfield",
      "heading"       => esc_html__("Carousel Settings | Speed", 'kiamo'),
      "param_name"    => "ca_speed",
      "value"         => '1000'
    ),
    array(
      "type"          => "dropdown",
      "heading"       => esc_html__("Carousel Settings | Auto Play", 'kiamo'),
      "param_name"    => "ca_auto_play",
      "value"         => array(
        esc_html__("Enable", 'kiamo' ) => '1',
        esc_html__("Disable", 'kiamo' ) => '0'
      )
    ),
    array(
      "type"        => "textfield",
      "heading"     => esc_html__("Carousel Settings | Auto Play Timeout", 'kiamo'),
      "param_name"  => "ca_auto_play_timeout",
      "value"       => '3000'
    ),
    array(
      "type"        => "textfield",
      "heading"     => esc_html__("Carousel Settings | Auto Play Speed", 'kiamo'),
      "param_name"  => "ca_auto_play_speed",
      "value"       => '1000'
    ),
    array(
      "type"        => "dropdown",
      "heading"     => esc_html__("Carousel Settings | Play Hover", 'kiamo'),
      "param_name"  => "ca_play_hover",
      "value"       => array(
        esc_html__("Enable", 'kiamo' )   => '1',
        esc_html__("Disable", 'kiamo' )  => '0'
      )
    ),
    array(
      "type"        => "dropdown",
      "heading"     => esc_html__("Carousel Settings | Navigation", 'kiamo'),
      "param_name"  => "ca_navigation",
      "value"       => array(
        esc_html__("Enable", 'kiamo' )   => '1',
        esc_html__("Disable", 'kiamo' )  => '0'
      )
    ),
    array(
      "type"        => "dropdown",
      "heading"     => esc_html__("Carousel Settings | Rewind Nav", 'kiamo'),
      "param_name"  => "ca_rewind_nav",
      "value"       => array(
        esc_html__("Enable", 'kiamo' )   => '1',
        esc_html__("Disable", 'kiamo' )  => '0'
      )
    ),
    array(
      "type"        => "dropdown",
      "heading"     => esc_html__("Carousel Settings | Pagination", 'kiamo'),
      "param_name"  => "ca_pagination",
      "value"       => array(
        esc_html__("Enable", 'kiamo' )   => '1',
        esc_html__("Disable", 'kiamo' )  => '0'
      )
    ),
    array(
      "type"        => "dropdown",
      "heading"     => esc_html__("Carousel Settings | Mouse Drag", 'kiamo'),
      "param_name"  => "ca_mouse_drag",
      "value"       => array(
        esc_html__("Enable", 'kiamo' )   => '1',
        esc_html__("Disable", 'kiamo' )  => '0'
      )
    ),
    array(
      "type"        => "dropdown",
      "heading"     => esc_html__("Carousel Settings | Touch Drag", 'kiamo'),
      "param_name"  => "ca_touch_drag",
      "value"       => array(
        esc_html__("Enable", 'kiamo' )   => '1',
        esc_html__("Disable", 'kiamo' )  => '0'
      )
    )
  );
}
