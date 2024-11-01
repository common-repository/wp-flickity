<?php

/**
 * WP FLICKITY MAIN SHORTCODE FUNCTION
 * @param  (array/shortcodeAtts) $wp_fkty_props Properties of flickity
 * @return (html/text) This function Fires the result of the slider configured at backend
 * @since  0.1
 */
function wp_flickity( $wp_fkty_props ) {
	$wp_fkty_props = shortcode_atts( array(
		'id' => 0
	), $wp_fkty_props );
	$flickity_slider_id = $wp_fkty_props['id'];
	$flickity_html_shortcode = "";
	global 	$wpdb,
			$wp_flickity_table_name,
			$post;
	$flickity_slider_metadata = $wpdb->get_var( "SELECT flickity_metadata FROM $wp_flickity_table_name WHERE id='$flickity_slider_id'" );
	$flickity_slider_metadata = unserialize($flickity_slider_metadata);
	$flickity_slider_images = explode(',', $flickity_slider_metadata['images_ids']);
	$flickity_slider_settings = isset($flickity_slider_metadata['settings'])?$flickity_slider_metadata['settings']:array();
	$flickity_post_enabled = (isset($flickity_slider_metadata['post_enabled']) and ((bool)$flickity_slider_metadata['post_enabled'])==true)?true:false;

	//checking if the slider is empty and notify admin user
	if((empty($flickity_slider_images) or $flickity_slider_images[0]=="") and $flickity_post_enabled==false){
		if(is_admin()){
			return 'This Flickity slider is currently empty. Go to <a href="'.wp_flickity_edit_link($flickity_slider_id).'">edit</a>';
		}else{
			return "";
		}		
	}

	$customSliderSettings = array();
	$customSliderClasses = array();
	$sliderImageSize = 'large';

	if(isset($flickity_slider_metadata['settings']['customSliderClass']) and !empty($flickity_slider_metadata['settings']['customSliderClass'])){
		$customSliderClasses[]=$flickity_slider_metadata['settings']['customSliderClass'];
	}
	if(isset($flickity_slider_metadata['settings']['thumbSize']) and !empty($flickity_slider_metadata['settings']['thumbSize'])){
		$sliderImageSize=$flickity_slider_metadata['settings']['thumbSize'];
	}

	if(!empty($flickity_slider_settings)){
		if(isset($flickity_slider_settings['wrapAround'])){
			$customSliderSettings['wrapAround']="wrapAround:true";
		}
		if(isset($flickity_slider_settings['freeScroll'])){
			$customSliderSettings['freeScroll']="freeScroll:true";
		}
		if(isset($flickity_slider_settings['prevNextButtons']) and intval($flickity_slider_settings['prevNextButtons'])==0){
			$customSliderSettings['prevNextButtons']="prevNextButtons:false";
		}
		if(isset($flickity_slider_settings['pageDots']) and intval($flickity_slider_settings['pageDots'])==0){
			$customSliderSettings['pageDots']="pageDots:false";
		}
		if(isset($flickity_slider_settings['autoPlay'])){
			if(intval($flickity_slider_settings['autoPlay'])==0){
				$customSliderSettings['autoPlay']="autoPlay:false";
			}elseif(intval($flickity_slider_settings['autoPlay'])>0){
				$customSliderSettings['autoPlay']="autoPlay:".$flickity_slider_settings['autoPlay'];
			}else{
				$customSliderSettings['autoPlay']="autoPlay:true";	
			}
			
		}
		if(isset($flickity_slider_settings['forceFullWidth'])){
			$customSliderClasses[]='forceFullWidth';
		}
		if(isset($flickity_slider_settings['customClass'])){
			$customSliderClasses[]=trim($flickity_slider_settings['customClass']);
		}
		
	}
	if($flickity_post_enabled) $customSliderClasses[]='wp-flickity-post-enabled';

	$h = '<div id="wpflickity-'.$flickity_slider_id.'" class="gallery '.implode(' ',$customSliderClasses).'" wp-flickity-sliderid="'.$flickity_slider_id.'">';
	
	if($flickity_post_enabled){
		$wpFlickityPosts = get_posts($flickity_slider_metadata['posts']['query']);
		foreach ( $wpFlickityPosts as $post ) : setup_postdata( $post ); 
			$h .= '<div class="gallery-cell post-id-'.get_the_ID().'">
				'.get_the_post_thumbnail(get_the_ID(),$sliderImageSize).'
				<a href="'.get_permalink().'">'.get_the_title().'</a>
			</div>';
		endforeach; 
		wp_reset_postdata();
	}else{
		foreach ( $flickity_slider_images as $image_id) {
			$image = wp_get_attachment_image_src($image_id,$sliderImageSize);
			$h.='<img width="'.$image[1].'" height="'.$image[2].'" 
				class="gallery-cell-image" 
				src="'.$image[0].'" />';
		}	
	}
	
	$h .="</div>";

	$customSliderSettings = implode(',', $customSliderSettings);
	$script = "
	<script>
	jQuery(document).ready(function(){
		jQuery('[wp-flickity-sliderid=\"".$flickity_slider_id."\"]').flickity({
			// options
			cellAlign: 'center',
			contain: true,
			imagesLoaded: true,
			//lazyload:2,
			percentPosition:false,
			".$customSliderSettings."
		});
	});
	</script>";
	$flickity_html_shortcode = $h.$script;
	return $flickity_html_shortcode;
}
add_shortcode( 'wp_flickity','wp_flickity' );
add_shortcode( 'wp-flickity','wp_flickity' );//aliasing
//