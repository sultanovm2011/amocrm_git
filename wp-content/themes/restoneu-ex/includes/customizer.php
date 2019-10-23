<?php

add_action( 'customize_register', 'restoneu_customize_register' );
function restoneu_customize_register( $wp_customize ) {
	
	//$wp_customize->get_section( 'header_image' )->panel = 'restoneu_header_panel';
    $wp_customize->get_section( 'header_image' )->priority = '13';
	$wp_customize->remove_control( 'display_header_text' );
    //$wp_customize->get_section( 'colors' )->priority = '10';
	
	/*--- Header Top Panel ---*/
	$wp_customize->add_panel( 'header_top_panel', array(
		'title' => __( 'Header Top Section', 'restoneu-ex' ),
		'description' => __( 'Change the font size and color for Site Title, Site Tagline and Menu', 'restoneu-ex' ), // Include html tags such as <p>.
		'priority' => 1, // Mixed with top-level-section hierarchy.
		) );
		
		//Site Title section
		$wp_customize->add_section( 'site_title_section', array(
			'title' => __( 'Site Title' , 'restoneu-ex' ),
			'panel' => 'header_top_panel',
			'priority' => 1,
			'capability' => 'edit_theme_options',
			) );
			
			//Site title color
			$wp_customize->add_setting( 'site_title_color', array(
				'capability' => 'edit_theme_options',
				'default' => '#ffffff',
				'transport' => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				) );
			
			$wp_customize->add_control( new WP_Customize_Color_Control(
				$wp_customize, 'site_title_color', array(
					'priority' => 2,
					'section' => 'site_title_section',
					'settings' => 'site_title_color',
					'label' => __( 'Color', 'restoneu-ex' ),
					'description' => __( 'Change color of site title', 'restoneu-ex' ),
					) )
				);
		
		//Site Tagline section
		$wp_customize->add_section( 'site_tagline_section', array(
			'title' => __( 'Site Tagline' , 'restoneu-ex' ),
			'panel' => 'header_top_panel',
			'priority' => 2,
			'capability' => 'edit_theme_options',
			) );
			
			//Tagline color
			$wp_customize->add_setting( 'tagline_color', array(
				'capability' => 'edit_theme_options',
				'default' => '#ffffff',
				'transport' => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				) );
			
			$wp_customize->add_control( new WP_Customize_Color_Control(
				$wp_customize, 'tagline_color', array(
					'priority' => 2,
					'section' => 'site_tagline_section',
					'settings' => 'tagline_color',
					'label' => __( 'Color', 'restoneu-ex' ),
					'description' => __( 'Change color of site tagline', 'restoneu-ex' ),
					) ) );
			
	/*--- Header Banner ---*/
	$wp_customize->add_panel( 'header_banner', array(
		'title' => __( 'Header Banner', 'restoneu-ex' ),
		'description' => __( 'Change the font size and color for Site Title, Site Tagline and Menu', 'restoneu-ex' ),
		'priority' => 2,
		) );
	
		//Banner type section
		$wp_customize->add_section( 'banner_type_section', array(
			'title' => __( 'Banner Type' , 'restoneu-ex' ),
			'panel' => 'header_banner',
			'priority' => 1,
			'capability' => 'edit_theme_options',
			) );
		
			//Banner type
			$wp_customize->add_setting( 'banner_type', array(
				'capability' => 'edit_theme_options',
				'default' => 'image',
				'transport' => 'refresh',
				'sanitize_callback' => 'twx_sntz_banner_type',
				) );
			
			$wp_customize->add_control( 'banner_type', array(
				'priority' => 1,
				'type' => 'radio',
				'section' => 'banner_type_section',
				'settings' => 'banner_type',
				'label' => __( 'Fron page banner type', 'restoneu-ex' ),
				'description' => __( 'Select banner type for front page', 'restoneu-ex' ),
				'choices' => array(
					'bg-color' => __( 'Background Color', 'restoneu-ex' ),
					'bg-gradient' => __( 'Background Gradient', 'restoneu-ex' ),
					'image' => __( 'Background Image', 'restoneu-ex' ),
					'video' => __( 'Background Video', 'restoneu-ex' ),
				),
				) );
				
			//Banner height
			$wp_customize->add_setting( 'banner_height', array(
				'capability' => 'edit_theme_options',
				'default' => '700',
				'transport' => 'refresh',
				'sanitize_callback' => 'absint',
				) );
			
			$wp_customize->add_control( 'banner_height', array(
				'priority' => 2,
				'type' => 'number',
				'section' => 'banner_type_section',
				'settings' => 'banner_height',
				'label' => __( 'Height of banner (Default: 700px)', 'restoneu-ex' ),
				'input_attrs' => array(
					'min' => 0,
					'max' => 2000,
					'step' => 1,
				),
				) );
			
		//Banner background color section
		$wp_customize->add_section( 'banner_bg_color_section', array(
			'title' => __( 'Banner Background: Color' , 'restoneu-ex' ),
			'panel' => 'header_banner',
			'priority' => 2,
			'capability' => 'edit_theme_options',
			) );
		
			//Banner background color
			$wp_customize->add_setting( 'banner_bg_color', array(
				'capability' => 'edit_theme_options',
				'default' => '#1C76A8',
				'transport' => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				) );
			
			$wp_customize->add_control( new WP_Customize_Color_Control(
				$wp_customize, 'banner_bg_color', array(
					'priority' => 1,
					'section' => 'banner_bg_color_section',
					'settings' => 'banner_bg_color',
					'label' => __( 'Background Color', 'restoneu-ex' ),
					'description' => __( 'Change background color of banner', 'restoneu-ex' ),
				) ) );
			
		//Banner background Gradient section
		$wp_customize->add_section( 'banner_bg_gradient_section', array(
			'title' => __( 'Banner Background: Gradient' , 'restoneu-ex' ),
			'panel' => 'header_banner',
			'priority' => 3,
			'capability' => 'edit_theme_options',
			'description' => __( 'Choose the colors below to give gradient background to your banner', 'restoneu-ex' ),
			) );
		
			//Banner background Gradient 1
			$wp_customize->add_setting( 'banner_bg_gradient_1', array(
				'capability' => 'edit_theme_options',
				'default' => '#1C76A8',
				'transport' => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				) );
			
			$wp_customize->add_control( new WP_Customize_Color_Control(
				$wp_customize, 'banner_bg_gradient_1', array(
					'priority' => 1,
					'section' => 'banner_bg_gradient_section',
					'settings' => 'banner_bg_gradient_1',
					'label' => __( 'Gradient color 1', 'restoneu-ex' ),
					'description' => __( 'Add 1st Gradient color', 'restoneu-ex' ),
					) ) );
			
			//Banner background Gradient 2
			$wp_customize->add_setting( 'banner_bg_gradient_2', array(
				'capability' => 'edit_theme_options',
				'default' => '#3DC5EF',
				'transport' => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				) );
			
			$wp_customize->add_control( new WP_Customize_Color_Control(
				$wp_customize, 'banner_bg_gradient_2', array(
					'priority' => 2,
					'section' => 'banner_bg_gradient_section',
					'settings' => 'banner_bg_gradient_2',
					'label' => __( 'Gradient color 2', 'restoneu-ex' ),
					'description' => __( 'Add 2nd Gradient color', 'restoneu-ex' ),
					) ) );
			
			//Banner background Gradient 3
			$wp_customize->add_setting( 'banner_bg_gradient_3', array(
				'capability' => 'edit_theme_options',
				'default' => '#A4DAF6',
				'transport' => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				) );
			
			$wp_customize->add_control( new WP_Customize_Color_Control(
				$wp_customize, 'banner_bg_gradient_3', array(
					'priority' => 3,
					'section' => 'banner_bg_gradient_section',
					'settings' => 'banner_bg_gradient_3',
					'label' => __( 'Gradient color 3', 'restoneu-ex' ),
					'description' => __( 'Add 3rd Gradient color', 'restoneu-ex' ),
					) ) );
		
		//Banner background image section
		$wp_customize->add_section( 'banner_bg_img_section', array(
			'title' => __( 'Banner Background: Image' , 'restoneu-ex' ),
			'panel' => 'header_banner',
			'priority' => 4,
			'capability' => 'edit_theme_options',
			'description' => __( 'Upload image and set other settings for banner background', 'restoneu-ex' ),
			) );
		
			//Banner background image
			$wp_customize->add_setting( 'banner_bg_img', array(
				'capability' => 'edit_theme_options',
				'default' => get_stylesheet_directory_uri() . '/images/homebanner1.jpg',
				'transport' => 'refresh',
				'sanitize_callback' => 'esc_url_raw',
				) );
			
			$wp_customize->add_control( new WP_Customize_Image_Control(
				$wp_customize, 'banner_bg_img', array(
					'priority' => 1,
					'type' => 'image',
					'section' => 'banner_bg_img_section',
					'settings' => 'banner_bg_img',
					'label' => __( 'Upload image for background', 'restoneu-ex' ),
					) ) );
			
			//Background size
			$wp_customize->add_setting( 'banner_bg_size', array(
				'capability' => 'edit_theme_options',
				'default' => 'cover',
				'transport' => 'refresh',
				'sanitize_callback' => 'twx_sntz_bg_size',
				) );
			
			$wp_customize->add_control( 'banner_bg_size', array(
				'priority' => 2,
				'type' => 'radio',
				'section' => 'banner_bg_img_section',
				'settings' => 'banner_bg_size',
				'label' => __( 'Banner background size', 'restoneu-ex' ),
				'choices' => array(
					'cover' => __( 'cover', 'restoneu-ex' ),
					'contain' => __( 'contain', 'restoneu-ex' ),
				),
				) );
			
			//Background position
			$wp_customize->add_setting( 'banner_bg_pos', array(
				'capability' => 'edit_theme_options',
				'default' => 'center-center',
				'transport' => 'refresh',
				'sanitize_callback' => 'twx_sntz_bg_pos',
				) );
			
			$wp_customize->add_control( 'banner_bg_pos', array(
				'priority' => 3,
				'type' => 'select',
				'section' => 'banner_bg_img_section',
				'settings' => 'banner_bg_pos',
				'label' => __( 'Banner background position', 'restoneu-ex' ),
				'choices' => array(
					'top-left' => __( 'top left', 'restoneu-ex' ),
					'top-center' => __( 'top center', 'restoneu-ex' ),
					'top-right' => __( 'top right', 'restoneu-ex' ),
					'center-left' => __( 'center left', 'restoneu-ex' ),
					'center-center' => __( 'center center', 'restoneu-ex' ),
					'center-right' => __( 'center right', 'restoneu-ex' ),
					'bottom-left' => __( 'bottom left', 'restoneu-ex' ),
					'bottom-center' => __( 'bottom center', 'restoneu-ex' ),
					'bottom-right' => __( 'bottom right', 'restoneu-ex' ),
				),
				) );
			
			//Background repeat
			$wp_customize->add_setting( 'banner_bg_repeat', array(
				'capability' => 'edit_theme_options',
				'default' => 1,
				'transport' => 'refresh',
				'sanitize_callback' => 'twx_sntz_checkbox',
				) );
			
			$wp_customize->add_control( 'banner_bg_repeat', array(
				'priority' => 4,
				'type' => 'checkbox',
				'section' => 'banner_bg_img_section',
				'settings' => 'banner_bg_repeat',
				'label' => __( 'Banner background no-repeat', 'restoneu-ex' ),
				) );
			
		//Banner content section
		$wp_customize->add_section( 'banner_content_section', array(
			'title' => __( 'Banner content' , 'restoneu-ex' ),
			'panel' => 'header_banner',
			'priority' => 5,
			'capability' => 'edit_theme_options',
			'description' => __( 'Form here you can change the text of all titles, subititle and paragraph present on banner', 'restoneu-ex' ),
			) );
		
			//Banner main title
			$wp_customize->add_setting( 'banner_main_title', array(
				'capability' => 'edit_theme_options',
				'default' => __( 'Welcome to Maxagency', 'restoneu-ex' ),
				'transport' => 'refresh',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
				) );
			
			$wp_customize->add_control( 'banner_main_title', array(
				'priority' => 1,
				'type' => 'text',
				'section' => 'banner_content_section',
				'settings' => 'banner_main_title',
				'label' => __( 'Banner main title', 'restoneu-ex' ),
				) );
			
			//Banner sub title
			$wp_customize->add_setting( 'banner_sub_title', array(
				'capability' => 'edit_theme_options',
				'default' => __( 'Premium Quality Restaurant Theme', 'restoneu-ex' ),
				'transport' => 'refresh',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
				) );
			
			$wp_customize->add_control( 'banner_sub_title', array(
				'priority' => 2,
				'type' => 'text',
				'section' => 'banner_content_section',
				'settings' => 'banner_sub_title',
				'label' => __( 'Banner sub title', 'restoneu-ex' ),
				) );
			
			//Banner paragraph
			$wp_customize->add_setting( 'banner_para', array(
				'capability' => 'edit_theme_options',
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi imperdiet ullamcorper erat', 'restoneu-ex' ),
				'transport' => 'refresh',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
				) );
			
			$wp_customize->add_control( 'banner_para', array(
				'priority' => 3,
				'type' => 'textarea',
				'section' => 'banner_content_section',
				'settings' => 'banner_para',
				'label' => __( 'Banner paragraph', 'restoneu-ex' ),
				) );
			
			//Banner button1 text
			$wp_customize->add_setting( 'banner_btn1_txt', array(
				'capability' => 'edit_theme_options',
				'default' => __( 'Ready to start', 'restoneu-ex' ),
				'transport' => 'refresh',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
				) );
			
			$wp_customize->add_control( 'banner_btn1_txt', array(
				'priority' => 4,
				'type' => 'text',
				'section' => 'banner_content_section',
				'settings' => 'banner_btn1_txt',
				'label' => __( 'Banner 1st button text', 'restoneu-ex' ),
				) );
			
			//Banner button1 url
			$wp_customize->add_setting( 'banner_btn1_url', array(
				'capability' => 'edit_theme_options',
				'default' => __( '#', 'restoneu-ex' ),
				'transport' => 'refresh',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
				) );
			
			$wp_customize->add_control( 'banner_btn1_url', array(
				'priority' => 5,
				'type' => 'text',
				'section' => 'banner_content_section',
				'settings' => 'banner_btn1_url',
				'label' => __( 'Banner 1st button url', 'restoneu-ex' ),
				) );
			
			//Banner button2 text
			$wp_customize->add_setting( 'banner_btn2_txt', array(
				'capability' => 'edit_theme_options',
				'default' => __( 'Know More', 'restoneu-ex' ),
				'transport' => 'refresh',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
				) );
			
			$wp_customize->add_control( 'banner_btn2_txt', array(
				'priority' => 6,
				'type' => 'text',
				'section' => 'banner_content_section',
				'settings' => 'banner_btn2_txt',
				'label' => __( 'Banner 2nd button text', 'restoneu-ex' ),
				) );
			
			//Banner button2 url
			$wp_customize->add_setting( 'banner_btn2_url', array(
				'capability' => 'edit_theme_options',
				'default' => __( '#', 'restoneu-ex' ),
				'transport' => 'refresh',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
				) );
			
			$wp_customize->add_control( 'banner_btn2_url', array(
				'priority' => 7,
				'type' => 'text',
				'section' => 'banner_content_section',
				'settings' => 'banner_btn2_url',
				'label' => __( 'Banner 2nd button url', 'restoneu-ex' ),
				) );
			
			//Banner side image
			$wp_customize->add_setting( 'banner_side_img', array(
				'capability' => 'edit_theme_options',
				'default' => '',
				'transport' => 'refresh',
				'sanitize_callback' => 'esc_url_raw',
				) );
			
			$wp_customize->add_control( new WP_Customize_Image_Control(
				$wp_customize, 'banner_side_img', array(
					'priority' => 8,
					'type' => 'image',
					'section' => 'banner_content_section',
					'settings' => 'banner_side_img',
					'label' => __( 'Upload side image for banner', 'restoneu-ex' ),
					) ) );
				
	
	/*--- General settings ---*/
	$wp_customize->add_panel( 'general_settings', array(
		'title' => __( 'General settings', 'restoneu-ex' ),
		'priority' => 3,
		) );
		
		//Page padding section
		$wp_customize->add_section( 'page_padding', array(
			'title' => __( 'Main page padding' , 'restoneu-ex' ),
			'panel' => 'general_settings',
			'priority' => 1,
			'capability' => 'edit_theme_options',
			'description' => __( 'Form here you can change the top and bottom padding of page', 'restoneu-ex' ),
			) );
		
			//Top padding between header and page
			$wp_customize->add_setting( 'page_top_padding', array(
				'capability' => 'edit_theme_options',
				'default' => '80',
				'transport' => 'refresh',
				'sanitize_callback' => 'absint',
				) );
			
			$wp_customize->add_control( 'page_top_padding', array(
				'priority' => 1,
				'type' => 'number',
				'section' => 'page_padding',
				'settings' => 'page_top_padding',
				'label' => __( 'Space between header and page content (in px)', 'restoneu-ex' ),
				'input_attrs' => array(
					'min' => 0,
					'max' => 500,
					'step' => 1,
				),
				) );
				
			//Bottom padding between footer and page
			$wp_customize->add_setting( 'page_bottom_padding', array(
				'capability' => 'edit_theme_options',
				'default' => '0',
				'transport' => 'refresh',
				'sanitize_callback' => 'absint',
				) );
			
			$wp_customize->add_control( 'page_bottom_padding', array(
				'priority' => 2,
				'type' => 'number',
				'section' => 'page_padding',
				'settings' => 'page_bottom_padding',
				'label' => __( 'Space between footer and page content (in px)', 'restoneu-ex' ),
				'input_attrs' => array(
					'min' => 0,
					'max' => 500,
					'step' => 1,
				),
				) );
			
	/*--- Blogs panel ---*/
	$wp_customize->add_panel( 'theme_blog_panel', array(
		'title' => __( 'Blogs Panel', 'restoneu-ex' ),
		'priority' => 4,
		) );
		
		//Blog layout
		$wp_customize->add_section( 'theme_blog_layout', array(
			'title' => __( 'Blog layout' , 'restoneu-ex' ),
			'panel' => 'theme_blog_panel',
			'priority' => 1,
			'capability' => 'edit_theme_options',
			) );
			
			//Blog type
			$wp_customize->add_setting( 'theme_blog_type', array(
				'capability' => 'edit_theme_options',
				'default' => 'default',
				'transport' => 'refresh',
				'sanitize_callback' => 'twx_sntz_blog_type',
				) );
			
			$wp_customize->add_control( 'theme_blog_type', array(
				'priority' => 1,
				'type' => 'radio',
				'section' => 'theme_blog_layout',
				'settings' => 'theme_blog_type',
				'label' => __( 'Select blog layout', 'restoneu-ex' ),
				'description' => __( 'This will change the styling of blogs on blog and single blog page', 'restoneu-ex' ),
				'choices' => array(
					'default' => __( 'Default', 'restoneu-ex' ),
				),
				) );
				
			//Show/hide sidebar on blog page
			$wp_customize->add_setting( 'show_sidebar', array(
				'capability' => 'edit_theme_options',
				'default' => 1,
				'transport' => 'refresh',
				'sanitize_callback' => 'twx_sntz_checkbox',
				) );
			
			$wp_customize->add_control( 'show_sidebar', array(
				'priority' => 2,
				'type' => 'checkbox',
				'section' => 'theme_blog_layout',
				'settings' => 'show_sidebar',
				'label' => __( 'Check this box to show sidebar on blogs page', 'restoneu-ex' ),
				) );
				
			//Show/hide sidebar on single blog page
			$wp_customize->add_setting( 'show_sidebar_single', array(
				'capability' => 'edit_theme_options',
				'default' => 1,
				'transport' => 'refresh',
				'sanitize_callback' => 'twx_sntz_checkbox',
				) );
			
			$wp_customize->add_control( 'show_sidebar_single', array(
				'priority' => 3,
				'type' => 'checkbox',
				'section' => 'theme_blog_layout',
				'settings' => 'show_sidebar_single',
				'label' => __( 'Check this box to show sidebar on single blog page', 'restoneu-ex' ),
				) );
				
	/*--- Colors panel ---*/		
	
			//Primary color
			$wp_customize->add_setting( 'primary_color', array(
				'capability' => 'edit_theme_options',
				'default' => '#153043',
				'transport' => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				) );
			
			$wp_customize->add_control( new WP_Customize_Color_Control(
				$wp_customize, 'primary_color', array(
					'priority' => 1,
					'section' => 'colors',
					'settings' => 'primary_color',
					'label' => __( 'Primary color', 'restoneu-ex' ),
					'description' => __( 'Change in this color will change the primary color of whole theme', 'restoneu-ex' ),
					) ) );
					
			//Theme text color
			$wp_customize->add_setting( 'theme_text_color', array(
				'capability' => 'edit_theme_options',
				'default' => '#8e88aa',
				'transport' => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				) );
			
			$wp_customize->add_control( new WP_Customize_Color_Control(
				$wp_customize, 'theme_text_color', array(
					'priority' => 2,
					'section' => 'colors',
					'settings' => 'theme_text_color',
					'label' => __( 'Theme text color', 'restoneu-ex' ),
					'description' => __( 'Change in this color will change the text color of whole theme (basically all paragraphs).', 'restoneu-ex' ),
					) ) );
}

/*--- All Sanitize callback functions used in customizer ---*/

function twx_sntz_banner_type( $value ){
	$valid = array(
        'bg-color' => __( 'Color', 'restoneu-ex' ),
		'bg-gradient' => __( 'Gradient', 'restoneu-ex' ),
		'image' => __( 'Image', 'restoneu-ex' ),
		'video' => __( 'Video', 'restoneu-ex' ),
		'carousel' => __( 'Background Carousel', 'restoneu-ex' ),
	);
	return ( array_key_exists( $value, $valid ) ? $value : '' );
}

function restoneu_sanitize_menu( $value ){
	$valid = array(
		'sticky'   => __('Sticky', 'restoneu-ex'),
		'static'   => __('Static', 'restoneu-ex'),
	);
	return ( array_key_exists( $value, $valid ) ? $value : '' );
}

function twx_sntz_bg_size( $value ){
	$valid = array(
		'cover'     => __('Cover', 'restoneu-ex'),
		'contain'   => __('Contain', 'restoneu-ex'),
	);
	return ( array_key_exists( $value, $valid ) ? $value : '' );
}

function twx_sntz_bg_pos( $value ){
	$valid = array(
		'top-left' => __( 'top left', 'restoneu-ex' ),
		'top-center' => __( 'top center', 'restoneu-ex' ),
		'top-right' => __( 'top right', 'restoneu-ex' ),
		'center-left' => __( 'center left', 'restoneu-ex' ),
		'center-center' => __( 'center center', 'restoneu-ex' ),
		'center-right' => __( 'center right', 'restoneu-ex' ),
		'bottom-left' => __( 'bottom left', 'restoneu-ex' ),
		'bottom-center' => __( 'bottom center', 'restoneu-ex' ),
		'bottom-right' => __( 'bottom right', 'restoneu-ex' ),
	);
	return ( array_key_exists( $value, $valid ) ? $value : '' );
}

function twx_sntz_blog_type( $value ){
	$valid = array(
		'default'           => __( 'Default', 'restoneu-ex' ),
		'special'       => __( 'Special', 'restoneu-ex' )
	);
	return ( array_key_exists( $value, $valid ) ? $value : '' );
}

function twx_sntz_checkbox( $value ) {
	//returns true if checkbox is checked
    return ( $value == 1 ? 1 : '' );
}

function restoneu_sanitize_widget_area($value){
	$valid = array(
		'1'     => __('One', 'restoneu-ex'),
		'2'     => __('Two', 'restoneu-ex'),
		'3'     => __('Three', 'restoneu-ex'),
		'4'     => __('Four', 'restoneu-ex')
	);
	return ( array_key_exists( $value, $valid ) ? $value : '' );
}

function restoneu_sanitize_wc_content($value){
	$valid = array(
		'left'    => __('Left', 'restoneu-ex'),
		'right'     => __('Right', 'restoneu-ex'),
	);
	return ( array_key_exists( $value, $valid ) ? $value : '' );
}

function restoneu_sanitize_image( $input ){
 
    /* default output */
    $value = '';
 
    /* check file type */
    $filetype = wp_check_filetype( $input );
    $mime_type = $filetype['type'];
 
    /* only mime type "image" allowed */
    if ( strpos( $mime_type, 'image' ) !== false ){
        $value = $input;
    }
 
    return $value;
}