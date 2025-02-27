<?php 
  $images = get_post_meta( get_the_ID(), 'kiamo_gallery_images' , false ); 
?>
<div class="gallery-post text-center"> 
  <div class="images lightGallery">
    <?php if($images && is_array($images) && count($images) > 0){
      $i = 0;
      foreach($images as $image): 
         $i++; $image_full_src = false; $image_thumb_src = false;
         if($image_full_src = wp_get_attachment_image_src($image, 'full')) $image_full_src = $image_full_src['0'];
         if($image_thumb_src = wp_get_attachment_image_src($image, 'thumbnail')) $image_thumb_src = $image_thumb_src['0']; 
         if($i==1){ ?>
            <div class="image-item" id="<?php echo get_the_ID() ?>">
               <a href="<?php echo esc_url($image_full_src) ?>" class="zoomGallery" title="" data-rel="lightGallery">
                  <span class="icon-expand"><i class="fa fa-expand"></i></span>
                  <img src="<?php echo esc_url($image_thumb_src) ?>"  class="hidden" alt="" />
               </a>
               <?php echo wp_get_attachment_image( $image, 'medium' ); ?>
            </div>
         <?php }else{ ?>
            <div class="image-item">
               <a href="<?php echo esc_url($image_full_src) ?>" class="zoomGallery hidden" title="" data-rel="lightGallery">
                  <img src="<?php echo esc_url($image_thumb_src) ?>" alt="" class="hidden" />
               </a>
            </div>
         <?php }
      endforeach;
    }?>
  </div>
</div>