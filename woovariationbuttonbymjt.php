<?php
/*
Plugin Name: Woocommerce Variation with Images
Plugin URI: https://github.com/madhavijha01/
Description: Replace Woocommerce Variation Dropdown with Images like as button 
Version: 1.0.0
Author: Madhavi Jha
Author URI: https://www.upwork.com/freelancers/madhavijha3
License: GNU Public License v1
*/

define('MJTPLUGINURL', plugin_dir_url( __FILE__ )); 

add_shortcode( 'mjt_woo_varitation_image_button', 'mjt_woo_varitation_image_button_script' );
function mjt_woo_varitation_image_button_script( $atts ) {
    ob_start();
	global $post;
	 $product_id = $post->ID;
	$product = wc_get_product($product_id);
	$current_products = $product->get_children();
	/*  echo '<pre>'; print_r($current_products) ; echo '</pre>'; */
	if( count( $current_products ) !== 0 ) {
		$args = array(
			'post_type'     => 'product_variation',
			'post_status'   => array( 'private', 'publish' ),
			'numberposts'   => -1,
			'orderby'       => 'menu_order',
			'order'         => 'asc',
			'post_parent'   => $product_id // get parent post-ID
		);
		$variations = get_posts( $args );
		echo '<div class="mjt_price_v woocommerce-Price-amount amount"></div>';
		echo '<ul class="variable-items-wrapper">';
		foreach ( $variations as $variation ) {
			$image_url = '';
			// get variation ID
			$variation_ID = $variation->ID;

			// get variations meta
			$product_variation = new WC_Product_Variation( $variation_ID );
		/*  echo '<pre>'; print_r($product_variation) ; echo '</pre>';  */

			// get variation featured image
			$variation_image = $product_variation->get_image();

			// get variation price
			$variation_price = $product_variation->get_price_html();
			
			// get variation sku
			$variation_sku = $product_variation->get_sku();
			 $variation_imgid =  $product_variation->get_image_id() ;
			 $image_url = wp_get_attachment_image_url($variation_imgid, 'full');
			 $attributes = $product_variation->get_attributes();  $attributename = array();
			 foreach ( $attributes as $attribute_name => $options ) : 
					$attributeterm[] = $attribute_name ;
					$attributename[] = $options ;
			endforeach;
			/* get_post_meta( $variation_ID , '_text_field_date_expire', true );  */
			echo '<li><a href="javascript:void(0);" data-sku="'.$variation_sku.'"  data-vid="'.$variation_ID.'" data-img="'.$image_url.'" data-attribute="'.$attributename[0].'" data-attributeterm="'.$attributeterm[0].'" class="mjt_variable_item"> '.$variation_image .' </a>'.$variation_price.'</li>';
			 
		}
		echo '</ul>';
	}
	$myvariable = ob_get_clean();
    return $myvariable;
}


add_action( 'wp_footer', 'mjt_include_custom_js_script' );

function mjt_include_custom_js_script(){ 
	 if(is_singular( 'product' ) ){
		global $post;
		 $product_id = $post->ID;
		$product = wc_get_product($product_id);
		$current_products = $product->get_children();
		if( count( $current_products ) !== 0 ) {  ?>
			<script type="text/javascript" id="mjt_variable_button">           
				jQuery(document).ready(function($){              
					$('ul.variable-items-wrapper').on( 'click', 'a.mjt_variable_item', function() {
						var psku = $( this ).attr('data-sku');
						 if(!psku){}else{
							$('.sku_wrapper span.sku').text('').text(psku);
						} 
						var pskuarr = psku.split(/\s*\-\s*/g) ;
						var pricehtml = $(this).next('.amount').html();
						var pvid = $( this ).attr('data-vid');							
						$('.variation_id').val('').val(pvid);	
						
						$( 'ul.variable-items-wrapper li' ).css('border','1px solid #e2e2e2');						
						$( this ).parent().css('border','2px solid #111');
						
						var pvterm = $( this ).attr('data-attributeterm');
						var pvval = $( this ).attr('data-attribute');
						if(!pvval){}else{
							$('.mjt_price_v').html('').html('<b>Selected :'+pvval+' : </b>'+pricehtml);	
						} 
						$( "#"+pvterm ).val( pvval ).trigger( 'change' );  
						/* $("#"+pvterm+" > [value='" + pvval + "']").attr("selected", "true"); 
						 $('.woocommerce-variation-add-to-cart').removeClass('woocommerce-variation-add-to-cart-disabled').addClass('woocommerce-variation-add-to-cart-enabled');
						 $('.single_add_to_cart_button').removeClass('disabled'); 
						  $('.single_add_to_cart_button').removeClass('wc-variation-selection-needed'); */
						/* disabled wc-variation-selection-needed
						woocommerce-variation-add-to-cart-disabled	
	woocommerce-variation-add-to-cart variations_button woocommerce-variation-add-to-cart-enabled
	single_add_to_cart_button button alt disabled
						var imgurl = $( this ).attr('data-img');
						var imghtml = '<div class="variation_img woocommerce-product-gallery__image"><img src="'+imgurl+'"></div>'; */
					});
			   
				 });					   
			</script>
		<?php }
	}
}
add_action( 'wp_head', 'mjt_include_custom_css_script' );

function mjt_include_custom_css_script(){ 
	if(is_singular( 'product' ) ){
		global $post;
		 $product_id = $post->ID;
		$product = wc_get_product($product_id);
		$current_products = $product->get_children();
		if( count( $current_products ) !== 0 ) {  ?>
			<style>
			ul.variable-items-wrapper {
				-webkit-box-pack: start;
				-ms-flex-pack: start;
				display: -webkit-box;
				display: -ms-flexbox;
				display: flex;
				-ms-flex-wrap: wrap;
				flex-wrap: wrap;
				justify-content: flex-start;
				list-style: none;
				margin: 0;
				padding: 0;
			}
			ul.variable-items-wrapper li {
				margin: 10px 4px;
				padding: 4px;
				border: 1px solid #e2e2e2;
				border-radius: 50%;
				width: 80px;
				height: 80px;
				vertical-align: middle;
				line-height: 60px;
				text-align: center;
				list-style:none; 
			}
			ul.variable-items-wrapper li a {
				display: block;
			}
			ul.variable-items-wrapper li a img {
				width: 80px;
				height: 70px;
				/* border: 1px solid #e2e2e2; */
				border-radius: 50%;
				margin: auto;
				vertical-align: middle;
			}
			ul.variable-items-wrapper li .woocommerce-Price-amount {
				visibility: hidden;
				height: 0px;
				padding: 0;
				margin: 0;
			}
			.single_variation_wrap .single_variation {
				display: none !important;
			}
			.woocommerce div.product form.cart table.variations {
				visibility: hidden;
				height: 0px;
				margin: 0;
				padding: 0;
				line-height: 0px;
			}
			.woocommerce div.product form.cart table.variations tr th, 
			.woocommerce div.product form.cart table.variations tr td, 
			.woocommerce div.product form.cart table.variations tr td label {
				padding: 0;
				line-height: 0px;
				height: 0px;
			}
			.woocommerce div.product form.cart .variations select {
				padding: 0;
				line-height: 0px;
				height: 0px;
			}
			.woocommerce div.product form.cart .reset_variations {
				visibility: hidden !important;
			}
			</style>
	<?php }
	}
}