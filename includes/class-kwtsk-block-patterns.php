<?php
/**
 * Patterns Setup.
 */
if (!defined('ABSPATH')) { exit; }

/**
 * Patterns.
 */
class Theme_Site_Kit_Patterns {
	/**
	 * Constructor function.
	 */
	public function __construct() {
		add_action( 'init', array($this, 'kwtsk_register_pattern_categories'));
		add_action( 'init', array($this, 'kwtsk_register_block_patterns'));
	}

	/**
	 * Register Theme Site Kit Pattern Categories
	 */
	public function kwtsk_register_pattern_categories() {
		if ( function_exists( 'register_block_pattern_category' ) ) {
			register_block_pattern_category(
				'kwtsk-header',
				array(
					'label'       => __( 'Theme Site Kit Headers', 'theme-site-kit' ),
					'description' => __( 'A collection of header designs provided by the Theme Site Kit plugin.', 'theme-site-kit' ),
				)
			);
			register_block_pattern_category(
				'kwtsk-footer',
				array(
					'label'       => __( 'Theme Site Kit Footers', 'theme-site-kit' ),
					'description' => __( 'A collection of footer designs provided by the Theme Site Kit plugin.', 'theme-site-kit' ),
				)
			);
			register_block_pattern_category(
				'kwtsk-content',
				array(
					'label'       => __( 'Theme Site Kit Patterns', 'theme-site-kit' ),
					'description' => __( 'A collection of custom patterns provided by the Theme Site Kit plugin.', 'theme-site-kit' ),
				)
			);
		}
	}

	/**
	 * Register Block Patterns
	 */
	function kwtsk_register_block_patterns() {
		if ( function_exists( 'register_block_pattern' ) ) {
			/*
			 * Headers
			 */
			register_block_pattern(
				'kwtsk/header-simple',
				array(
					'title'       => __( 'Simple Header', 'theme-site-kit' ),
					'description' => _x( 'A simple header.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-header' ),
					'content'     => trim( '<!-- wp:group {"style":{"spacing":{"padding":{"right":"20px","top":"25px","bottom":"30px","left":"20px"},"blockGap":"0px"}},"textColor":"black","layout":{"type":"constrained","justifyContent":"center"}} -->
					<div class="wp-block-group has-black-color has-text-color" style="padding-top:25px;padding-right:20px;padding-bottom:30px;padding-left:20px"><!-- wp:columns {"verticalAlignment":"center","isStackedOnMobile":false} -->
					<div class="wp-block-columns are-vertically-aligned-center is-not-stacked-on-mobile"><!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:site-logo {"width":200,"shouldSyncIcon":false,"align":"left","className":"is-style-default nomargin"} /-->
					<!-- wp:social-links {"iconColor":"black","iconColorValue":"#000000","openInNewTab":true,"size":"has-normal-icon-size","className":"is-style-logos-only","style":{"spacing":{"blockGap":{"left":"15px"},"margin":{"right":"0","bottom":"0","left":"0"}}},"layout":{"type":"flex","justifyContent":"left","orientation":"horizontal","flexWrap":"nowrap"}} -->
					<ul class="wp-block-social-links has-normal-icon-size has-icon-color is-style-logos-only" style="margin-right:0;margin-bottom:0;margin-left:0"><!-- wp:social-link {"url":"#","service":"twitter"} /-->
					<!-- wp:social-link {"url":"#","service":"youtube"} /-->
					<!-- wp:social-link {"url":"#","service":"linkedin"} /--></ul>
					<!-- /wp:social-links --></div>
					<!-- /wp:column -->
					<!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:navigation {"ref":22,"textColor":"contrastbg","icon":"menu","overlayBackgroundColor":"basebg","overlayTextColor":"textonbase","style":{"typography":{"textTransform":"uppercase","fontSize":"15px","letterSpacing":"0.4px"},"spacing":{"blockGap":"var:preset|spacing|40"}},"fontFamily":"barlow","layout":{"type":"flex","justifyContent":"right"}} /--></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns --></div>
					<!-- /wp:group -->' ),
				)
			);
			register_block_pattern(
				'kwtsk/header-two',
				array(
					'title'       => __( 'Two Header', 'theme-site-kit' ),
					'description' => _x( 'A Two header.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-footer' ),
					'content'     => trim( '<!-- wp:group {"style":{"spacing":{"padding":{"right":"20px","top":"25px","bottom":"30px","left":"20px"},"blockGap":"0px"}},"textColor":"black","layout":{"type":"constrained","justifyContent":"center"}} -->
					<div class="wp-block-group has-black-color has-text-color" style="padding-top:25px;padding-right:20px;padding-bottom:30px;padding-left:20px"><!-- wp:columns {"verticalAlignment":"center","isStackedOnMobile":false} -->
					<div class="wp-block-columns are-vertically-aligned-center is-not-stacked-on-mobile"><!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:navigation {"ref":22,"textColor":"contrastbg","icon":"menu","overlayBackgroundColor":"basebg","overlayTextColor":"textonbase","style":{"typography":{"textTransform":"uppercase","fontSize":"15px","letterSpacing":"0.4px"},"spacing":{"blockGap":"var:preset|spacing|40"}},"fontFamily":"barlow","layout":{"type":"flex","justifyContent":"right"}} /--></div>
					<!-- /wp:column -->
					<!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:site-logo {"width":200,"shouldSyncIcon":false,"align":"left","className":"is-style-default nomargin"} /-->
					<!-- wp:social-links {"iconColor":"black","iconColorValue":"#000000","openInNewTab":true,"size":"has-normal-icon-size","className":"is-style-logos-only","style":{"spacing":{"blockGap":{"left":"15px"},"margin":{"right":"0","bottom":"0","left":"0"}}},"layout":{"type":"flex","justifyContent":"left","orientation":"horizontal","flexWrap":"nowrap"}} -->
					<ul class="wp-block-social-links has-normal-icon-size has-icon-color is-style-logos-only" style="margin-right:0;margin-bottom:0;margin-left:0"><!-- wp:social-link {"url":"#","service":"twitter"} /-->
					<!-- wp:social-link {"url":"#","service":"youtube"} /-->
					<!-- wp:social-link {"url":"#","service":"linkedin"} /--></ul>
					<!-- /wp:social-links --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns --></div>
					<!-- /wp:group -->' ),
				)
			);
			
			/*
			 * Footers
			 */
			register_block_pattern(
				'kwtsk/footer-two',
				array(
					'title'       => __( 'Two Footer', 'theme-site-kit' ),
					'description' => _x( 'A Two Footer.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-header' ),
					'content'     => trim( '<!-- wp:group {"style":{"spacing":{"padding":{"right":"20px","top":"25px","bottom":"30px","left":"20px"},"blockGap":"0px"}},"textColor":"black","layout":{"type":"constrained","justifyContent":"center"}} -->
					<div class="wp-block-group has-black-color has-text-color" style="padding-top:25px;padding-right:20px;padding-bottom:30px;padding-left:20px"><!-- wp:columns {"verticalAlignment":"center","isStackedOnMobile":false} -->
					<div class="wp-block-columns are-vertically-aligned-center is-not-stacked-on-mobile"><!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:navigation {"ref":22,"textColor":"contrastbg","icon":"menu","overlayBackgroundColor":"basebg","overlayTextColor":"textonbase","style":{"typography":{"textTransform":"uppercase","fontSize":"15px","letterSpacing":"0.4px"},"spacing":{"blockGap":"var:preset|spacing|40"}},"fontFamily":"barlow","layout":{"type":"flex","justifyContent":"right"}} /--></div>
					<!-- /wp:column -->
					<!-- wp:column {"verticalAlignment":"center"} -->
					<!-- /wp:column --></div>
					<!-- /wp:columns --></div>
					<!-- /wp:group -->' ),
				)
			);

			/*
			 * Content
			 */
			// Add more patterns as needed
		}
	}

}
new Theme_Site_Kit_Patterns();
