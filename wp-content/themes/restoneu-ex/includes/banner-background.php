<?php



/*--- Banner background function for image, color, gradient ---*/

function restoneu_banner_background() {
	
	$title = get_theme_mod('banner_sub_title', __('Premium Quality Restaurant Theme', 'restoneu-ex'));
	$fornt_img = get_theme_mod('banner_side_img','');
	$subtitle = get_theme_mod('banner_main_title', __('Welcome to restoneu', 'restoneu-ex'));
	$subpera = get_theme_mod('banner_para', __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi imperdiet ullamcorper erat', 'restoneu-ex'));
	$button_text1 = get_theme_mod('banner_btn1_txt', __('Ready to start', 'restoneu-ex'));
	$button_text2 = get_theme_mod('banner_btn2_txt', __('Know more', 'restoneu-ex'));
	$button_url1 = get_theme_mod('banner_btn1_url', '#');
	$button_url2 = get_theme_mod('banner_btn2_url', '#');
	$site_header_type = get_theme_mod('site_header_type', 'image');
	
	?>
	
	<div class="header-background background-wave style1">
		<div class="header-content">
			<div class="container">
				<div class="row align-center">
				<div class="<?php if(!empty($fornt_img)){ echo 'col-md-7 col-lg-8' ;} else {echo 'col-md-12 col-lg-12';} ?> ">
						<div class="banner-text-content" >
							<h5 class="bg-maintitle"><?php echo esc_html( $title ); ?></h5>
							<h1 class="bg-subtitle"><?php echo esc_html( $subtitle ); ?></h1>
							<p class="bg-subpera"><?php echo esc_html( $subpera ); ?></p>
							<?php if(!empty($button_text1)){ ?>
							<div class="banner-btn-div">
							    <a href="<?php echo esc_url( $button_url1 ); ?>" class="bg-banner-button banner-button mt-5"><?php echo esc_html( $button_text1 ); ?></a>&nbsp;&nbsp;
							<?php }?>
							<?php if(!empty($button_text2)){ ?>
							<a href="<?php echo esc_url( $button_url2 ); ?>" class="bg-banner-button2 banner-button mt-5"><?php echo esc_html( $button_text2 ); ?></a>
							</div>
							<?php }?>
						</div>
					</div>
					<?php if(!empty($fornt_img)){ ?>
					<div class="col-md-5 col-lg-3 ">
						<div class="background_image_right">
							<img class="front_img" src="<?php  echo esc_url($fornt_img); ?>" alt="Banner Image">
						</div>
					</div>
					<?php }?>
				</div>	
			</div>	
		</div>
	</div> 
	
	<?php
	
}


/*--- Banner background function for video ---*/

function restoneu_banner_video() {

	if ( !function_exists('the_custom_header_markup') ) {
		return;
	}

	$banner_type 	= get_theme_mod( 'banner_type' );

	if ( get_theme_mod('banner_type') == 'video' && is_front_page() ) {
		the_custom_header_markup();
	}
}