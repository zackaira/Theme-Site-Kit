<?php
/**
 * Patterns Setup.
 */
if (!defined('ABSPATH')) { exit; }

/**
 * Patterns.
 */
class KWTSK_Patterns {
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

			register_block_pattern_category(
				'kwtsk-maintenance',
				array(
					'label'       => __( 'Theme Site Kit Maintenance Mode', 'theme-site-kit' ),
					'description' => __( 'A collection of Maintenance Mode patterns provided by the Theme Site Kit plugin.', 'theme-site-kit' ),
				)
			);
		}
	}

	/**
	 * Register Block Patterns
	 */
	function kwtsk_register_block_patterns() {
		if ( function_exists( 'register_block_pattern' ) ) {
			$pattern_images_url = esc_url( KWTSK_PLUGIN_URL . 'assets/patterns/' );
			
			/*
			 * Headers
			 */
			register_block_pattern(
				'kwtsk/simple-header-top-bar',
				array(
					'title'       => __( 'Simple Header with Top Bar', 'theme-site-kit' ),
					'description' => _x( 'A simple header layout with a top bar.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-header' ),
					'content'     => trim( '<!-- wp:group {"metadata":{"name":"Top Bar"},"style":{"spacing":{"padding":{"top":"0.5rem","bottom":"0.5rem"}}},"backgroundColor":"contrast-font","layout":{"type":"constrained"}} -->
											<div class="wp-block-group has-contrast-font-background-color has-background" style="padding-top:0.5rem;padding-bottom:0.5rem"><!-- wp:columns {"verticalAlignment":"center"} -->
											<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
											<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%"><!-- wp:social-links {"iconColor":"contrast","iconColorValue":"#111111","openInNewTab":true,"className":"is-style-logos-only","style":{"spacing":{"blockGap":{"left":"1.2rem"}}}} -->
											<ul class="wp-block-social-links has-icon-color is-style-logos-only"><!-- wp:social-link {"url":"#","service":"wordpress"} /-->
											<!-- wp:social-link {"url":"#","service":"x"} /-->
											<!-- wp:social-link {"url":"#","service":"instagram"} /-->
											<!-- wp:social-link {"url":"#","service":"mail"} /--></ul>
											<!-- /wp:social-links --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"center","width":"100%"} -->
											<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:100%"><!-- wp:group {"style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"1.2rem"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
											<div class="wp-block-group" style="padding-right:0;padding-left:0"><!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
											<p style="font-size:14px">082 467 4532</p>
											<!-- /wp:paragraph -->
											<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
											<p style="font-size:14px">email@yourodmain.com</p>
											<!-- /wp:paragraph -->
											<!-- wp:buttons -->
											<div class="wp-block-buttons"><!-- wp:button {"style":{"spacing":{"padding":{"left":"var:preset|spacing|30","right":"var:preset|spacing|30","top":"6px","bottom":"6px"}},"typography":{"fontSize":"14px"}}} -->
											<div class="wp-block-button"><a class="wp-block-button__link has-custom-font-size wp-element-button" style="padding-top:6px;padding-right:var(--wp--preset--spacing--30);padding-bottom:6px;padding-left:var(--wp--preset--spacing--30);font-size:14px">Contact Us</a></div>
											<!-- /wp:button --></div>
											<!-- /wp:buttons --></div>
											<!-- /wp:group --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns --></div>
											<!-- /wp:group -->
											<!-- wp:separator {"className":"is-style-wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"color":{"background":"#ebebeb"}}} -->
											<hr class="wp-block-separator has-text-color has-alpha-channel-opacity has-background is-style-wide" style="margin-top:0;margin-bottom:0;background-color:#ebebeb;color:#ebebeb"/>
											<!-- /wp:separator -->
											<!-- wp:group {"metadata":{"name":"Main Header"},"align":"full","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"2rem","bottom":"2rem"}}},"layout":{"type":"default"}} -->
											<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:2rem;padding-bottom:2rem"><!-- wp:columns {"isStackedOnMobile":false} -->
											<div class="wp-block-columns is-not-stacked-on-mobile"><!-- wp:column {"width":"40%"} -->
											<div class="wp-block-column" style="flex-basis:40%"><!-- wp:site-title {"level":0} /--></div>
											<!-- /wp:column -->
											<!-- wp:column -->
											<div class="wp-block-column"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
											<div class="wp-block-group"><!-- wp:navigation {"ref":4,"overlayBackgroundColor":"base","overlayTextColor":"contrast","layout":{"type":"flex","justifyContent":"right","flexWrap":"wrap"}} /--></div>
											<!-- /wp:group --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns --></div>
											<!-- /wp:group -->'
					),
				)
			);
			register_block_pattern(
				'kwtsk/centered-header-top-bar',
				array(
					'title'       => __( 'Centered Header with Top Bar', 'theme-site-kit' ),
					'description' => _x( 'A centered header layout with a top bar.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-header' ),
					'content'     => trim( '<!-- wp:group {"metadata":{"name":"Top Bar"},"style":{"spacing":{"padding":{"top":"0.8rem","bottom":"0.8rem"}},"elements":{"link":{"color":{"text":"var:preset|color|contrast-font"}}}},"backgroundColor":"base-heading","textColor":"contrast-font","layout":{"type":"constrained"}} -->
											<div class="wp-block-group has-contrast-font-color has-base-heading-background-color has-text-color has-background has-link-color" style="padding-top:0.8rem;padding-bottom:0.8rem"><!-- wp:columns {"verticalAlignment":"center"} -->
											<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
											<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%"><!-- wp:social-links {"iconColor":"contrast-font","iconColorValue":"#f6f6f6","openInNewTab":true,"className":"is-style-logos-only","style":{"spacing":{"blockGap":{"left":"1.2rem"}}}} -->
											<ul class="wp-block-social-links has-icon-color is-style-logos-only"><!-- wp:social-link {"url":"#","service":"wordpress"} /-->
											<!-- wp:social-link {"url":"#","service":"x"} /-->
											<!-- wp:social-link {"url":"#","service":"instagram"} /-->
											<!-- wp:social-link {"url":"#","service":"mail"} /--></ul>
											<!-- /wp:social-links --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"center","width":"100%"} -->
											<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:100%"><!-- wp:group {"style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"1.2rem"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
											<div class="wp-block-group" style="padding-right:0;padding-left:0"><!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
											<p style="font-size:14px">082 467 4532</p>
											<!-- /wp:paragraph -->
											<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
											<p style="font-size:14px">email@yourodmain.com</p>
											<!-- /wp:paragraph -->
											<!-- wp:social-links {"iconColor":"contrast-font","iconColorValue":"#f6f6f6","openInNewTab":true,"className":"is-style-logos-only","style":{"spacing":{"blockGap":{"left":"1.2rem"}}},"layout":{"type":"flex","justifyContent":"right"}} -->
											<ul class="wp-block-social-links has-icon-color is-style-logos-only"><!-- wp:social-link {"url":"#","service":"whatsapp"} /--></ul>
											<!-- /wp:social-links --></div>
											<!-- /wp:group --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns --></div>
											<!-- /wp:group -->
											<!-- wp:group {"metadata":{"name":"Main Header"},"style":{"spacing":{"padding":{"right":"20px","top":"20px","bottom":"20px","left":"20px"},"blockGap":"0px","margin":{"top":"0","bottom":"0"}},"position":{"type":""}},"textColor":"black","gradient":"black-to-green","layout":{"type":"constrained","justifyContent":"center","contentSize":""}} -->
											<div class="wp-block-group has-black-color has-black-to-green-gradient-background has-text-color has-background" style="margin-top:0;margin-bottom:0;padding-top:20px;padding-right:20px;padding-bottom:20px;padding-left:20px"><!-- wp:columns {"verticalAlignment":"center","isStackedOnMobile":false,"style":{"spacing":{"blockGap":{"top":"0","left":"var:preset|spacing|30"}}}} -->
											<div class="wp-block-columns are-vertically-aligned-center is-not-stacked-on-mobile"><!-- wp:column {"verticalAlignment":"center","width":"200px","style":{"spacing":{"blockGap":"0"}}} -->
											<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:200px"><!-- wp:site-title {"level":0,"fontSize":"larger"} /--></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"center"} -->
											<div class="wp-block-column is-vertically-aligned-center"><!-- wp:navigation {"ref":4,"textColor":"site-black","icon":"menu","overlayBackgroundColor":"basebg","overlayTextColor":"textonbase","kwtskEnableMobileMenu":true,"className":"custom-mobile-menu","style":{"typography":{"textTransform":"uppercase"},"spacing":{"blockGap":"2.4rem"}},"fontFamily":"inter","layout":{"type":"flex","justifyContent":"center"}} /--></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"center","width":"200px"} -->
											<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:200px"><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"}} -->
											<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline","style":{"typography":{"fontSize":"15px"},"spacing":{"padding":{"left":"1.2rem","right":"1.2rem","top":"0.6rem","bottom":"0.6rem"}}}} -->
											<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-custom-font-size wp-element-button" style="padding-top:0.6rem;padding-right:1.2rem;padding-bottom:0.6rem;padding-left:1.2rem;font-size:15px">Contact</a></div>
											<!-- /wp:button --></div>
											<!-- /wp:buttons --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns --></div>
											<!-- /wp:group -->'
					),
				)
			);
			register_block_pattern(
				'kwtsk/header-simple-two-navigation',
				array(
					'title'       => __( 'Simple Header with 2 Navigations', 'theme-site-kit' ),
					'description' => _x( 'A simple header layout with 2 navigation, one normal and one popup/slide-out.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-header' ),
					'content'     => trim( '<!-- wp:group {"metadata":{"name":"Main Header"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"},"blockGap":"0px"},"position":{"type":""}},"layout":{"type":"constrained","justifyContent":"center","contentSize":""}} -->
											<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|60"}}}} -->
											<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"250px","style":{"spacing":{"blockGap":"0"}}} -->
											<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:250px"><!-- wp:site-title {"level":0,"fontSize":"larger"} /--></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"center"} -->
											<div class="wp-block-column is-vertically-aligned-center"><!-- wp:group {"className":"header-right","style":{"spacing":{"padding":{"right":"0","left":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right","verticalAlignment":"center","orientation":"horizontal"}} -->
											<div class="wp-block-group header-right" style="padding-right:0;padding-left:0"><!-- wp:paragraph {"metadata":{"name":"Phone Number"}} -->
											<p>082 456 5432</p>
											<!-- /wp:paragraph -->
											<!-- wp:navigation {"ref":4,"icon":"menu","overlayBackgroundColor":"basebg","overlayTextColor":"textonbase","metadata":{"ignoredHookedBlocks":["woocommerce/customer-account","woocommerce/mini-cart"]},"className":"custom-mobile-menu","style":{"typography":{"textTransform":"uppercase"},"spacing":{"blockGap":"2rem"}},"fontFamily":"inter","layout":{"type":"flex","justifyContent":"right"}} /-->
											<!-- wp:navigation {"ref":30,"overlayMenu":"always","metadata":{"ignoredHookedBlocks":["woocommerce/customer-account"]},"kwtskEnableMobileMenu":true,"className":"custom-mobile-menu","style":{"typography":{"textTransform":"uppercase"}},"fontFamily":"inter","layout":{"type":"flex","justifyContent":"right"}} /-->
											<!-- wp:buttons -->
											<div class="wp-block-buttons"><!-- wp:button {"style":{"spacing":{"padding":{"left":"var:preset|spacing|40","right":"var:preset|spacing|40","top":"0.6rem","bottom":"0.6rem"}}}} -->
											<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" style="padding-top:0.6rem;padding-right:var(--wp--preset--spacing--40);padding-bottom:0.6rem;padding-left:var(--wp--preset--spacing--40)">Contact Us</a></div>
											<!-- /wp:button --></div>
											<!-- /wp:buttons --></div>
											<!-- /wp:group --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns --></div>
											<!-- /wp:group -->'
					),
				)
			);
			register_block_pattern(
				'kwtsk/centered-logo-two-navigation',
				array(
					'title'       => __( 'A Center Logo Header with 2 Side Navigations and a Top Bar', 'theme-site-kit' ),
					'description' => _x( 'A header layout with a top bar and 2 navigations on each side and a centered logo.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-header' ),
					'content'     => trim( '<!-- wp:group {"metadata":{"name":"Top Bar"},"style":{"spacing":{"padding":{"top":"0.5rem","bottom":"0.5rem"}},"elements":{"link":{"color":{"text":"var:preset|color|contrast-font"}}}},"backgroundColor":"base-heading","textColor":"contrast-font","layout":{"type":"constrained"}} -->
											<div class="wp-block-group has-contrast-font-color has-base-heading-background-color has-text-color has-background has-link-color" style="padding-top:0.5rem;padding-bottom:0.5rem"><!-- wp:columns {"verticalAlignment":"center"} -->
											<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
											<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%"><!-- wp:social-links {"iconColor":"base-heading","iconColorValue":"#222231","iconBackgroundColor":"contrast-font","iconBackgroundColorValue":"#f6f6f6","openInNewTab":true,"size":"has-small-icon-size","className":"is-style-default","style":{"spacing":{"blockGap":{"left":"1.2rem"}}}} -->
											<ul class="wp-block-social-links has-small-icon-size has-icon-color has-icon-background-color is-style-default"><!-- wp:social-link {"url":"#","service":"wordpress"} /-->
											<!-- wp:social-link {"url":"#","service":"x"} /-->
											<!-- wp:social-link {"url":"#","service":"instagram"} /-->
											<!-- wp:social-link {"url":"#","service":"mail"} /--></ul>
											<!-- /wp:social-links --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"center","width":"100%"} -->
											<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:100%"><!-- wp:group {"style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"1.2rem"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
											<div class="wp-block-group" style="padding-right:0;padding-left:0"><!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
											<p style="font-size:14px">082 467 4532</p>
											<!-- /wp:paragraph -->
											<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
											<p style="font-size:14px">email@yourodmain.com</p>
											<!-- /wp:paragraph -->
											<!-- wp:social-links {"iconColor":"base-heading","iconColorValue":"#222231","iconBackgroundColor":"contrast-font","iconBackgroundColorValue":"#f6f6f6","openInNewTab":true,"size":"has-small-icon-size","className":"is-style-default","style":{"spacing":{"blockGap":{"left":"1.2rem"}}}} -->
											<ul class="wp-block-social-links has-small-icon-size has-icon-color has-icon-background-color is-style-default"><!-- wp:social-link {"url":"#","service":"whatsapp"} /--></ul>
											<!-- /wp:social-links -->
											<!-- wp:buttons -->
											<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"contrast-font","textColor":"base-heading","style":{"spacing":{"padding":{"left":"var:preset|spacing|30","right":"var:preset|spacing|30","top":"6px","bottom":"6px"}},"typography":{"fontSize":"14px"},"elements":{"link":{"color":{"text":"var:preset|color|base-heading"}}}}} -->
											<div class="wp-block-button"><a class="wp-block-button__link has-base-heading-color has-contrast-font-background-color has-text-color has-background has-link-color has-custom-font-size wp-element-button" style="padding-top:6px;padding-right:var(--wp--preset--spacing--30);padding-bottom:6px;padding-left:var(--wp--preset--spacing--30);font-size:14px">Contact Us</a></div>
											<!-- /wp:button --></div>
											<!-- /wp:buttons --></div>
											<!-- /wp:group --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns --></div>
											<!-- /wp:group -->
											<!-- wp:group {"metadata":{"name":"Main Header"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"},"blockGap":"0px","margin":{"top":"0","bottom":"0"}},"position":{"type":""}},"layout":{"type":"constrained","justifyContent":"center","contentSize":""}} -->
											<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|60","left":"var:preset|spacing|60"}}}} -->
											<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","metadata":{"name":"Left Navigation"}} -->
											<div class="wp-block-column is-vertically-aligned-center"><!-- wp:group {"className":"header-right","style":{"spacing":{"padding":{"right":"0","left":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right","verticalAlignment":"center","orientation":"horizontal"}} -->
											<div class="wp-block-group header-right" style="padding-right:0;padding-left:0"><!-- wp:navigation {"ref":4,"overlayMenu":"never","icon":"menu","overlayBackgroundColor":"basebg","overlayTextColor":"textonbase","metadata":{"ignoredHookedBlocks":["woocommerce/customer-account","woocommerce/mini-cart"]},"className":"custom-mobile-menu","style":{"typography":{"textTransform":"uppercase"},"spacing":{"blockGap":"2rem"}},"fontFamily":"inter","layout":{"type":"flex","justifyContent":"right"}} /--></div>
											<!-- /wp:group --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"center","width":"220px","metadata":{"name":"Logo"},"style":{"spacing":{"blockGap":"0"}}} -->
											<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:220px"><!-- wp:group {"backgroundColor":"contrast-font","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
											<div class="wp-block-group has-contrast-font-background-color has-background"><!-- wp:site-title {"level":0,"style":{"layout":{"selfStretch":"fit","flexSize":null}},"fontSize":"larger"} /--></div>
											<!-- /wp:group --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"center","metadata":{"name":"Right Navigation"}} -->
											<div class="wp-block-column is-vertically-aligned-center"><!-- wp:group {"className":"header-right","style":{"spacing":{"padding":{"right":"0","left":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center","orientation":"horizontal"}} -->
											<div class="wp-block-group header-right" style="padding-right:0;padding-left:0"><!-- wp:navigation {"ref":4,"overlayMenu":"never","icon":"menu","overlayBackgroundColor":"basebg","overlayTextColor":"textonbase","metadata":{"ignoredHookedBlocks":["woocommerce/customer-account","woocommerce/mini-cart"]},"className":"custom-mobile-menu","style":{"typography":{"textTransform":"uppercase"},"spacing":{"blockGap":"2rem"}},"fontFamily":"inter","layout":{"type":"flex","justifyContent":"right"}} /--></div>
											<!-- /wp:group --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns --></div>
											<!-- /wp:group -->'
					),
				)
			);
			
			/*
			 * Footers
			 */
			register_block_pattern(
				'kwtsk/footer-business-light',
				array(
					'title'       => __( 'Business Footer Light', 'theme-site-kit' ),
					'description' => _x( 'A light business style footer.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-footer' ),
					'content'     => trim( '<!-- wp:group {"align":"full","className":"is-style-top-shadow","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"0","right":"20px","left":"20px"},"blockGap":"0px","margin":{"top":"0px","bottom":"0px"}}},"layout":{"type":"constrained","contentSize":""}} -->
											<div class="wp-block-group alignfull is-style-top-shadow" style="margin-top:0px;margin-bottom:0px;padding-top:var(--wp--preset--spacing--60);padding-right:20px;padding-bottom:0;padding-left:20px"><!-- wp:separator {"className":"is-style-wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"color":{"background":"#dedede"}}} -->
											<hr class="wp-block-separator has-text-color has-alpha-channel-opacity has-background is-style-wide" style="margin-top:0;margin-bottom:0;background-color:#dedede;color:#dedede"/>
											<!-- /wp:separator -->
											<!-- wp:columns {"verticalAlignment":"top","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|80"},"margin":{"top":"0px","bottom":"0px"},"padding":{"top":"3.8rem","bottom":"3.8rem"}}}} -->
											<div class="wp-block-columns are-vertically-aligned-top" style="margin-top:0px;margin-bottom:0px;padding-top:3.8rem;padding-bottom:3.8rem"><!-- wp:column {"verticalAlignment":"top","width":"48%","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:48%"><!-- wp:site-logo {"width":150,"style":{"spacing":{"margin":{"bottom":"1.86rem"}}}} /-->
											<!-- wp:paragraph -->
											<p>
												Craft stunning, tailor-made websites with ease using the WordPress Site
												Editor and the powerful Kairaweb block theme, no coding knowledge
												required!<br>Get it for free today!
											</p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":"28%","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:28%"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"#999999"}}},"typography":{"letterSpacing":"1px"},"spacing":{"margin":{"bottom":"15px"}},"color":{"text":"#999999"}}} -->
											<p class="has-text-color has-link-color" style="color:#999999;margin-bottom:15px;letter-spacing:1px">
													Services
												</p>
											<!-- /wp:paragraph -->
											<!-- wp:list {"className":"is-style-unstyled","style":{"typography":{"fontSize":"15px"}}} -->
											<ul style="font-size:15px" class="wp-block-list is-style-unstyled"><!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">
													Web Design &amp; Development
													</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">
													WooCommerce Development
													</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">
													SEO Services
													</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">
													Support &amp; Maintenance
													</li>
											<!-- /wp:list-item --></ul>
											<!-- /wp:list --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":"15%","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:15%"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"#999999"}}},"typography":{"letterSpacing":"1px"},"spacing":{"margin":{"bottom":"15px"}},"color":{"text":"#999999"}}} -->
											<p class="has-text-color has-link-color" style="color:#999999;margin-bottom:15px;letter-spacing:1px">
												Company
											</p>
											<!-- /wp:paragraph -->
											<!-- wp:list {"className":"is-style-unstyled","style":{"typography":{"fontSize":"15px"}}} -->
											<ul style="font-size:15px" class="wp-block-list is-style-unstyled"><!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">About</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">Services</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">Products</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">
												Case Studies
											</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Contact</li>
											<!-- /wp:list-item --></ul>
											<!-- /wp:list --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":"17%","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:17%"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"#999999"}}},"typography":{"letterSpacing":"1px"},"spacing":{"margin":{"bottom":"15px"}},"color":{"text":"#999999"}}} -->
											<p class="has-text-color has-link-color" style="color:#999999;margin-bottom:15px;letter-spacing:1px">
													Resources
												</p>
											<!-- /wp:paragraph -->
											<!-- wp:list {"className":"is-style-unstyled","style":{"typography":{"fontSize":"15px"}}} -->
											<ul style="font-size:15px" class="wp-block-list is-style-unstyled"><!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">Blog</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">
													Support Docs
													</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">Get Help</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">
													View Kaira
													</li>
											<!-- /wp:list-item --></ul>
											<!-- /wp:list --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns -->
											<!-- wp:separator {"className":"is-style-wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"color":{"background":"#dedede"}}} -->
											<hr class="wp-block-separator has-text-color has-alpha-channel-opacity has-background is-style-wide" style="margin-top:0;margin-bottom:0;background-color:#dedede;color:#dedede"/>
											<!-- /wp:separator --></div>
											<!-- /wp:group -->
											<!-- wp:group {"align":"full","className":"is-style-top-shadow","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|40","right":"var:preset|spacing|50","left":"var:preset|spacing|50"},"blockGap":"0px","margin":{"top":"0","bottom":"0"}}},"backgroundColor":"site-black","textColor":"site-background","layout":{"type":"constrained","contentSize":""}} -->
											<div class="wp-block-group alignfull is-style-top-shadow has-site-background-color has-site-black-background-color has-text-color has-background" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
											<div class="wp-block-columns are-vertically-aligned-center" style="margin-bottom:var(--wp--preset--spacing--30)"><!-- wp:column {"verticalAlignment":"center"} -->
											<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"fontSize":"xsmall"} -->
											<p class="has-xsmall-font-size">© 2025 Powered by <a href="https://kairaweb.com/" target="_blank" rel="noreferrer noopener">Kaira</a> <sub><sup><mark style="background-color: rgba(0, 0, 0, 0); color: #ababab" class="has-inline-color">|</mark></sup></sub> Privacy Policy</p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"center","style":{"spacing":{"blockGap":"5px"}}} -->
											<div class="wp-block-column is-vertically-aligned-center"><!-- wp:social-links {"iconColor":"black","iconColorValue":"#000000","openInNewTab":true,"size":"has-normal-icon-size","className":"is-style-logos-only","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|30"}}},"layout":{"type":"flex","justifyContent":"right"}} -->
											<ul class="wp-block-social-links has-normal-icon-size has-icon-color is-style-logos-only"><!-- wp:social-link {"url":"#","service":"x"} /-->
											<!-- wp:social-link {"url":"#","service":"facebook"} /-->
											<!-- wp:social-link {"url":"#","service":"instagram"} /-->
											<!-- wp:social-link {"url":"#","service":"linkedin"} /-->
											<!-- wp:social-link {"url":"#","service":"wordpress"} /--></ul>
											<!-- /wp:social-links --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns --></div>
											<!-- /wp:group -->' ),
				)
			);
			register_block_pattern(
				'kwtsk/footer-business-dark',
				array(
					'title'       => __( 'Business Footer Dark', 'theme-site-kit' ),
					'description' => _x( 'A light business style footer.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-footer' ),
					'content'     => trim( '<!-- wp:group {"align":"full","className":"is-style-top-shadow","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"0","right":"20px","left":"20px"},"blockGap":"0px","margin":{"top":"0px","bottom":"0px"}},"elements":{"link":{"color":{"text":"var:preset|color|contrast-font"}}}},"backgroundColor":"base-heading","textColor":"contrast-font","layout":{"type":"constrained","contentSize":""}} -->
											<div class="wp-block-group alignfull is-style-top-shadow has-contrast-font-color has-base-heading-background-color has-text-color has-background has-link-color" style="margin-top:0px;margin-bottom:0px;padding-top:var(--wp--preset--spacing--60);padding-right:20px;padding-bottom:0;padding-left:20px"><!-- wp:separator {"className":"is-style-wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"color":{"background":"#b6b6b6"}}} -->
											<hr class="wp-block-separator has-text-color has-alpha-channel-opacity has-background is-style-wide" style="margin-top:0;margin-bottom:0;background-color:#b6b6b6;color:#b6b6b6"/>
											<!-- /wp:separator -->
											<!-- wp:columns {"verticalAlignment":"top","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|80"},"margin":{"top":"0px","bottom":"0px"},"padding":{"top":"3.8rem","bottom":"3.8rem"}}}} -->
											<div class="wp-block-columns are-vertically-aligned-top" style="margin-top:0px;margin-bottom:0px;padding-top:3.8rem;padding-bottom:3.8rem"><!-- wp:column {"verticalAlignment":"top","width":"48%","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:48%"><!-- wp:site-logo {"width":150,"style":{"spacing":{"margin":{"bottom":"1.86rem"}}}} /-->
											<!-- wp:paragraph -->
											<p>
													Craft stunning, tailor-made websites with ease using the WordPress Site
													Editor and the powerful Kairaweb block theme, no coding knowledge
													required!<br>Get it for free today!
												</p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":"28%","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:28%"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"#b6b6b6"}}},"typography":{"letterSpacing":"1px"},"spacing":{"margin":{"bottom":"15px"}},"color":{"text":"#b6b6b6"}}} -->
											<p class="has-text-color has-link-color" style="color:#b6b6b6;margin-bottom:15px;letter-spacing:1px">
													Services
												</p>
											<!-- /wp:paragraph -->
											<!-- wp:list {"className":"is-style-unstyled","style":{"typography":{"fontSize":"15px"}}} -->
											<ul style="font-size:15px" class="wp-block-list is-style-unstyled"><!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">
													Web Design &amp; Development
													</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">
													WooCommerce Development
													</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">
													SEO Services
													</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">
													Support &amp; Maintenance
													</li>
											<!-- /wp:list-item --></ul>
											<!-- /wp:list --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":"15%","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:15%"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"#b6b6b6"}}},"typography":{"letterSpacing":"1px"},"spacing":{"margin":{"bottom":"15px"}},"color":{"text":"#b6b6b6"}}} -->
											<p class="has-text-color has-link-color" style="color:#b6b6b6;margin-bottom:15px;letter-spacing:1px">
													Company
												</p>
											<!-- /wp:paragraph -->
											<!-- wp:list {"className":"is-style-unstyled","style":{"typography":{"fontSize":"15px"}}} -->
											<ul style="font-size:15px" class="wp-block-list is-style-unstyled"><!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">About</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">Services</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">Products</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">
													Case Studies
													</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Contact</li>
											<!-- /wp:list-item --></ul>
											<!-- /wp:list --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":"17%","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:17%"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"#b6b6b6"}}},"typography":{"letterSpacing":"1px"},"spacing":{"margin":{"bottom":"15px"}},"color":{"text":"#b6b6b6"}}} -->
											<p class="has-text-color has-link-color" style="color:#b6b6b6;margin-bottom:15px;letter-spacing:1px">
													Resources
												</p>
											<!-- /wp:paragraph -->
											<!-- wp:list {"className":"is-style-unstyled","style":{"typography":{"fontSize":"15px"}}} -->
											<ul style="font-size:15px" class="wp-block-list is-style-unstyled"><!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">Blog</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">
													Support Docs
													</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">Get Help</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
											<li style="margin-bottom:var(--wp--preset--spacing--20)">
													View Kaira
													</li>
											<!-- /wp:list-item --></ul>
											<!-- /wp:list --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns -->
											<!-- wp:separator {"className":"is-style-wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"color":{"background":"#b6b6b6"}}} -->
											<hr class="wp-block-separator has-text-color has-alpha-channel-opacity has-background is-style-wide" style="margin-top:0;margin-bottom:0;background-color:#b6b6b6;color:#b6b6b6"/>
											<!-- /wp:separator --></div>
											<!-- /wp:group -->
											<!-- wp:group {"align":"full","className":"is-style-top-shadow","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|40","right":"var:preset|spacing|50","left":"var:preset|spacing|50"},"blockGap":"0px","margin":{"top":"0","bottom":"0"}}},"backgroundColor":"base-heading","textColor":"contrast-font","layout":{"type":"constrained","contentSize":""}} -->
											<div class="wp-block-group alignfull is-style-top-shadow has-contrast-font-color has-base-heading-background-color has-text-color has-background" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
											<div class="wp-block-columns are-vertically-aligned-center" style="margin-bottom:var(--wp--preset--spacing--30)"><!-- wp:column {"verticalAlignment":"center"} -->
											<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"fontSize":"xsmall"} -->
											<p class="has-xsmall-font-size">© 2025 Powered by <a href="https://kairaweb.com/" target="_blank" rel="noreferrer noopener">Kaira</a> <sub><sup><mark style="background-color: rgba(0, 0, 0, 0); color: #ababab" class="has-inline-color">|</mark></sup></sub> Privacy Policy</p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"center","style":{"spacing":{"blockGap":"5px"}}} -->
											<div class="wp-block-column is-vertically-aligned-center"><!-- wp:social-links {"customIconColor":"#b6b6b6","iconColorValue":"#b6b6b6","openInNewTab":true,"size":"has-normal-icon-size","className":"is-style-logos-only","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|30"}}},"layout":{"type":"flex","justifyContent":"right"}} -->
											<ul class="wp-block-social-links has-normal-icon-size has-icon-color is-style-logos-only"><!-- wp:social-link {"url":"#","service":"x"} /-->
											<!-- wp:social-link {"url":"#","service":"facebook"} /-->
											<!-- wp:social-link {"url":"#","service":"instagram"} /-->
											<!-- wp:social-link {"url":"#","service":"linkedin"} /-->
											<!-- wp:social-link {"url":"#","service":"wordpress"} /--></ul>
											<!-- /wp:social-links --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns --></div>
											<!-- /wp:group -->'
					),
				)
			);

			register_block_pattern(
				'kwtsk/footer-designer-light',
				array(
					'title'       => __( 'Designer Footer Light', 'theme-site-kit' ),
					'description' => _x( 'A neat light designer style footer.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-footer' ),
					'content'     => trim( '<!-- wp:group {"align":"full","className":"is-style-top-shadow","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"0","right":"20px","left":"20px"},"blockGap":"0px","margin":{"top":"0px","bottom":"0px"}}},"layout":{"type":"constrained","contentSize":""}} -->
											<div class="wp-block-group alignfull is-style-top-shadow" style="margin-top:0px;margin-bottom:0px;padding-top:var(--wp--preset--spacing--70);padding-right:20px;padding-bottom:0;padding-left:20px"><!-- wp:group {"style":{"spacing":{"padding":{"bottom":"9.4rem","right":"0","left":"0"}},"background":{"backgroundImage":{"url":"' . $pattern_images_url . 'footer-bg-logo.png","id":5567,"source":"file"},"backgroundSize":"contain","backgroundAttachment":"scroll","backgroundRepeat":"no-repeat","backgroundPosition":"50% 100%"}},"layout":{"type":"constrained"}} -->
											<div class="wp-block-group" style="padding-right:0;padding-bottom:9.4rem;padding-left:0"><!-- wp:columns {"verticalAlignment":"top","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"},"margin":{"top":"0px","bottom":"0px"}}}} -->
											<div class="wp-block-columns are-vertically-aligned-top" style="margin-top:0px;margin-bottom:0px"><!-- wp:column {"verticalAlignment":"top","width":"260px","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:260px"><!-- wp:site-logo {"width":220} /--></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":"130px","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:130px"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"1px","lineHeight":"0.8"},"spacing":{"margin":{"bottom":"15px","top":"0"}}}} -->
											<p style="margin-top:0;margin-bottom:15px;letter-spacing:1px;line-height:0.8;text-transform:uppercase"><strong>Browse</strong></p>
											<!-- /wp:paragraph -->
											<!-- wp:list {"className":"is-style-unstyled"} -->
											<ul class="wp-block-list is-style-unstyled"><!-- wp:list-item -->
											<li>About</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Company</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Services</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Contact</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li><br>Portfolio</li>
											<!-- /wp:list-item --></ul>
											<!-- /wp:list --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"1px","lineHeight":"0.8"},"spacing":{"margin":{"bottom":"15px","top":"0"}}}} -->
											<p style="margin-top:0;margin-bottom:15px;letter-spacing:1px;line-height:0.8;text-transform:uppercase"><strong>Address</strong></p>
											<!-- /wp:paragraph -->
											<!-- wp:list {"className":"is-style-unstyled"} -->
											<ul class="wp-block-list is-style-unstyled"><!-- wp:list-item -->
											<li>28 Youraddress Street,</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Cape Town, 7945</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>South Africa</li>
											<!-- /wp:list-item --></ul>
											<!-- /wp:list -->
											<!-- wp:spacer {"height":"25px"} -->
											<div style="height:25px" aria-hidden="true" class="wp-block-spacer"></div>
											<!-- /wp:spacer -->
											<!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"1px","lineHeight":"0.8"},"spacing":{"margin":{"bottom":"15px","top":"0"}}}} -->
											<p style="margin-top:0;margin-bottom:15px;letter-spacing:1px;line-height:0.8;text-transform:uppercase"><strong>Contact Us</strong></p>
											<!-- /wp:paragraph -->
											<!-- wp:list {"className":"is-style-unstyled"} -->
											<ul class="wp-block-list is-style-unstyled"><!-- wp:list-item -->
											<li>Phone: +27 82 444 4444</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>WhatsApp: +27 82 444 4444</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Email: email@yourdomain.com</li>
											<!-- /wp:list-item --></ul>
											<!-- /wp:list --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":"190px","className":"last-col","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top last-col" style="flex-basis:190px"><!-- wp:paragraph {"align":"right","style":{"typography":{"textTransform":"uppercase","letterSpacing":"1px","lineHeight":"0.8"},"spacing":{"margin":{"bottom":"15px","top":"0"}}}} -->
											<p class="has-text-align-right" style="margin-top:0;margin-bottom:15px;letter-spacing:1px;line-height:0.8;text-transform:uppercase"><strong>Find Us Online</strong></p>
											<!-- /wp:paragraph -->
											<!-- wp:social-links {"iconColor":"contrast-font","iconColorValue":"#f6f6f6","iconBackgroundColor":"brand","iconBackgroundColorValue":"#256cc9","openInNewTab":true,"className":"is-style-default","style":{"spacing":{"margin":{"right":"0","left":"0"},"blockGap":{"left":"var:preset|spacing|30"}}},"layout":{"type":"flex","justifyContent":"right"}} -->
											<ul class="wp-block-social-links has-icon-color has-icon-background-color is-style-default" style="margin-right:0;margin-left:0"><!-- wp:social-link {"url":"#","service":"facebook"} /-->
											<!-- wp:social-link {"url":"#","service":"instagram"} /-->
											<!-- wp:social-link {"url":"#","service":"youtube"} /-->
											<!-- wp:social-link {"url":"#","service":"linkedin"} /--></ul>
											<!-- /wp:social-links -->
											<!-- wp:spacer {"height":"20px"} -->
											<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
											<!-- /wp:spacer -->
											<!-- wp:paragraph {"align":"right"} -->
											<p class="has-text-align-right">Privacy<br>Terms</p>
											<!-- /wp:paragraph -->
											<!-- wp:spacer {"height":"36px"} -->
											<div style="height:36px" aria-hidden="true" class="wp-block-spacer"></div>
											<!-- /wp:spacer -->
											<!-- wp:paragraph {"align":"right","style":{"typography":{"fontSize":"13px"}}} -->
											<p class="has-text-align-right" style="font-size:13px">© 2025 Powered by Kaira.</p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns --></div>
											<!-- /wp:group --></div>
											<!-- /wp:group -->'
					),
				)
			);
			register_block_pattern(
				'kwtsk/footer-designer-dark',
				array(
					'title'       => __( 'Designer Footer Dark', 'theme-site-kit' ),
					'description' => _x( 'A neat dark designer style footer.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-footer' ),
					'content'     => trim( '<!-- wp:group {"align":"full","className":"is-style-top-shadow","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"0","right":"20px","left":"20px"},"blockGap":"0px","margin":{"top":"0px","bottom":"0px"}},"elements":{"link":{"color":{"text":"var:preset|color|contrast-font"}}}},"backgroundColor":"base-heading","textColor":"contrast-font","layout":{"type":"constrained","contentSize":""}} -->
											<div class="wp-block-group alignfull is-style-top-shadow has-contrast-font-color has-base-heading-background-color has-text-color has-background has-link-color" style="margin-top:0px;margin-bottom:0px;padding-top:var(--wp--preset--spacing--70);padding-right:20px;padding-bottom:0;padding-left:20px"><!-- wp:group {"style":{"spacing":{"padding":{"bottom":"9.4rem","right":"0","left":"0"}},"background":{"backgroundImage":{"url":"' . $pattern_images_url . 'footer-bg-logo-white.png","id":5568,"source":"file"},"backgroundSize":"contain","backgroundAttachment":"scroll","backgroundRepeat":"no-repeat","backgroundPosition":"50% 100%"}},"layout":{"type":"constrained"}} -->
											<div class="wp-block-group" style="padding-right:0;padding-bottom:9.4rem;padding-left:0"><!-- wp:columns {"verticalAlignment":"top","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"},"margin":{"top":"0px","bottom":"0px"}}}} -->
											<div class="wp-block-columns are-vertically-aligned-top" style="margin-top:0px;margin-bottom:0px"><!-- wp:column {"verticalAlignment":"top","width":"260px","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:260px"><!-- wp:site-logo {"width":220} /--></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":"130px","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:130px"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|base-bg"}}},"typography":{"textTransform":"uppercase","letterSpacing":"1px","lineHeight":"0.8"},"spacing":{"margin":{"bottom":"15px","top":"0"}}},"textColor":"base-bg"} -->
											<p class="has-base-bg-color has-text-color has-link-color" style="margin-top:0;margin-bottom:15px;letter-spacing:1px;line-height:0.8;text-transform:uppercase"><strong>Browse</strong></p>
											<!-- /wp:paragraph -->
											<!-- wp:list {"className":"is-style-unstyled"} -->
											<ul class="wp-block-list is-style-unstyled"><!-- wp:list-item -->
											<li>About</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Company</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Services</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Contact</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li><br>Portfolio</li>
											<!-- /wp:list-item --></ul>
											<!-- /wp:list --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|base-bg"}}},"typography":{"textTransform":"uppercase","letterSpacing":"1px","lineHeight":"0.8"},"spacing":{"margin":{"bottom":"15px","top":"0"}}},"textColor":"base-bg"} -->
											<p class="has-base-bg-color has-text-color has-link-color" style="margin-top:0;margin-bottom:15px;letter-spacing:1px;line-height:0.8;text-transform:uppercase"><strong>Address</strong></p>
											<!-- /wp:paragraph -->
											<!-- wp:list {"className":"is-style-unstyled"} -->
											<ul class="wp-block-list is-style-unstyled"><!-- wp:list-item -->
											<li>28 Youraddress Street,</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Cape Town, 7945</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>South Africa</li>
											<!-- /wp:list-item --></ul>
											<!-- /wp:list -->
											<!-- wp:spacer {"height":"25px"} -->
											<div style="height:25px" aria-hidden="true" class="wp-block-spacer"></div>
											<!-- /wp:spacer -->
											<!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|base-bg"}}},"typography":{"textTransform":"uppercase","letterSpacing":"1px","lineHeight":"0.8"},"spacing":{"margin":{"bottom":"15px","top":"0"}}},"textColor":"base-bg"} -->
											<p class="has-base-bg-color has-text-color has-link-color" style="margin-top:0;margin-bottom:15px;letter-spacing:1px;line-height:0.8;text-transform:uppercase"><strong>Contact Us</strong></p>
											<!-- /wp:paragraph -->
											<!-- wp:list {"className":"is-style-unstyled"} -->
											<ul class="wp-block-list is-style-unstyled"><!-- wp:list-item -->
											<li>Phone: +27 82 444 4444</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>WhatsApp: +27 82 444 4444</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Email: email@yourdomain.com</li>
											<!-- /wp:list-item --></ul>
											<!-- /wp:list --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":"190px","className":"last-col","style":{"spacing":{"blockGap":"12px"}}} -->
											<div class="wp-block-column is-vertically-aligned-top last-col" style="flex-basis:190px"><!-- wp:paragraph {"align":"right","style":{"elements":{"link":{"color":{"text":"var:preset|color|base-bg"}}},"typography":{"textTransform":"uppercase","letterSpacing":"1px","lineHeight":"0.8"},"spacing":{"margin":{"bottom":"15px","top":"0"}}},"textColor":"base-bg"} -->
											<p class="has-text-align-right has-base-bg-color has-text-color has-link-color" style="margin-top:0;margin-bottom:15px;letter-spacing:1px;line-height:0.8;text-transform:uppercase"><strong>Find Us Online</strong></p>
											<!-- /wp:paragraph -->
											<!-- wp:social-links {"iconColor":"contrast-font","iconColorValue":"#f6f6f6","iconBackgroundColor":"brand","iconBackgroundColorValue":"#256cc9","openInNewTab":true,"className":"is-style-default","style":{"spacing":{"margin":{"right":"0","left":"0"},"blockGap":{"left":"var:preset|spacing|30"}}},"layout":{"type":"flex","justifyContent":"right"}} -->
											<ul class="wp-block-social-links has-icon-color has-icon-background-color is-style-default" style="margin-right:0;margin-left:0"><!-- wp:social-link {"url":"#","service":"facebook"} /-->
											<!-- wp:social-link {"url":"#","service":"instagram"} /-->
											<!-- wp:social-link {"url":"#","service":"youtube"} /-->
											<!-- wp:social-link {"url":"#","service":"linkedin"} /--></ul>
											<!-- /wp:social-links -->
											<!-- wp:spacer {"height":"20px"} -->
											<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
											<!-- /wp:spacer -->
											<!-- wp:paragraph {"align":"right"} -->
											<p class="has-text-align-right">Privacy<br>Terms</p>
											<!-- /wp:paragraph -->
											<!-- wp:spacer {"height":"36px"} -->
											<div style="height:36px" aria-hidden="true" class="wp-block-spacer"></div>
											<!-- /wp:spacer -->
											<!-- wp:paragraph {"align":"right","style":{"typography":{"fontSize":"13px"}}} -->
											<p class="has-text-align-right" style="font-size:13px">© 2025 Powered by Kaira.</p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns --></div>
											<!-- /wp:group --></div>
											<!-- /wp:group -->'
					),
				)
			);

			register_block_pattern(
				'kwtsk/footer-simple-columns-light',
				array(
					'title'       => __( 'Simple Columns Footer Light', 'theme-site-kit' ),
					'description' => _x( 'A light style footer with simple columns.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-footer' ),
					'content'     => trim( '<!-- wp:group {"align":"full","className":"is-style-top-shadow","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|70"},"blockGap":"0px","margin":{"top":"0px","bottom":"0px"}},"typography":{"fontSize":"14px"}},"layout":{"type":"constrained","contentSize":""}} -->
											<div class="wp-block-group alignfull is-style-top-shadow" style="margin-top:0px;margin-bottom:0px;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--70);font-size:14px"><!-- wp:columns {"verticalAlignment":"top","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|70"},"margin":{"top":"0px","bottom":"0px"}}}} -->
											<div class="wp-block-columns are-vertically-aligned-top" style="margin-top:0px;margin-bottom:0px"><!-- wp:column {"verticalAlignment":"top","width":"","style":{"spacing":{"padding":{"right":"0","left":"0"}}}} -->
											<div class="wp-block-column is-vertically-aligned-top" style="padding-right:0;padding-left:0"><!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|base-heading"}}},"typography":{"fontSize":"22px"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|40"}}},"textColor":"base-heading"} -->
											<h4 class="wp-block-heading has-base-heading-color has-text-color has-link-color" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--40);font-size:22px">ABOUT US</h4>
											<!-- /wp:heading -->
											<!-- wp:group {"style":{"spacing":{"padding":{"right":"0","left":"0"}}},"layout":{"type":"constrained","contentSize":"300px","justifyContent":"left"}} -->
											<div class="wp-block-group" style="padding-right:0;padding-left:0"><!-- wp:paragraph -->
											<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sagittis risus ut tellus sodales, sed eleifend tellus hendrerit.</p>
											<!-- /wp:paragraph -->
											<!-- wp:paragraph -->
											<p>Donec sagittis risus ut tellus sodales, sed eleifend tellus hendrerit.</p>
											<!-- /wp:paragraph -->
											<!-- wp:paragraph {"style":{"color":{"text":"#c9c9c9"},"elements":{"link":{"color":{"text":"#c9c9c9"}}}}} -->
											<p class="has-text-color has-link-color" style="color:#c9c9c9"><a href="#" data-type="page" data-id="8">Read More</a></p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:group --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":""} -->
											<div class="wp-block-column is-vertically-aligned-top"><!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|base-heading"}}},"typography":{"fontSize":"22px"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|40"}}},"textColor":"base-heading"} -->
											<h4 class="wp-block-heading has-base-heading-color has-text-color has-link-color" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--40);font-size:22px">COMPANY</h4>
											<!-- /wp:heading -->
											<!-- wp:list {"className":"is-style-unstyled","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
											<ul style="margin-top:var(--wp--preset--spacing--50)" class="wp-block-list is-style-unstyled"><!-- wp:list-item -->
											<li>Home</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>About Us</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Our Services</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Meet the Team</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Contact Us</li>
											<!-- /wp:list-item --></ul>
											<!-- /wp:list --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":""} -->
											<div class="wp-block-column is-vertically-aligned-top"><!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|base-heading"}}},"typography":{"fontSize":"22px"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|40"}}},"textColor":"base-heading"} -->
											<h4 class="wp-block-heading has-base-heading-color has-text-color has-link-color" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--40);font-size:22px">CONTACT US</h4>
											<!-- /wp:heading -->
											<!-- wp:group {"style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"0"}},"layout":{"type":"constrained"}} -->
											<div class="wp-block-group" style="padding-right:0;padding-left:0"><!-- wp:paragraph -->
											<p>Telephone: (021) 760 0044</p>
											<!-- /wp:paragraph -->
											<!-- wp:paragraph -->
											<p>Fax: (021) 760 0044</p>
											<!-- /wp:paragraph -->
											<!-- wp:paragraph -->
											<p>Email: email@yourdomain.com</p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:group -->
											<!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|base-heading"}}},"typography":{"fontSize":"22px"},"spacing":{"margin":{"top":"1.8rem","bottom":"var:preset|spacing|40"}}},"textColor":"base-heading"} -->
											<h4 class="wp-block-heading has-base-heading-color has-text-color has-link-color" style="margin-top:1.8rem;margin-bottom:var(--wp--preset--spacing--40);font-size:22px">ADDRESS</h4>
											<!-- /wp:heading -->
											<!-- wp:group {"style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"0"}},"layout":{"type":"constrained"}} -->
											<div class="wp-block-group" style="padding-right:0;padding-left:0"><!-- wp:paragraph -->
											<p>28 Youraddress Street</p>
											<!-- /wp:paragraph -->
											<!-- wp:paragraph -->
											<p>Fax: (021) 760 0044</p>
											<!-- /wp:paragraph -->
											<!-- wp:paragraph -->
											<p>Email: email@yourdomain.com</p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:group --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":""} -->
											<div class="wp-block-column is-vertically-aligned-top"><!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|base-heading"}}},"typography":{"fontSize":"22px"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|40"}}},"textColor":"base-heading"} -->
											<h4 class="wp-block-heading has-base-heading-color has-text-color has-link-color" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--40);font-size:22px">FIND US ONLINE</h4>
											<!-- /wp:heading -->
											<!-- wp:social-links {"iconColor":"base-off","iconColorValue":"#f9f9f9","iconBackgroundColor":"contrast-off","iconBackgroundColorValue":"#31313f","openInNewTab":true,"layout":{"type":"flex"}} -->
											<ul class="wp-block-social-links has-icon-color has-icon-background-color"><!-- wp:social-link {"url":"#","service":"facebook"} /-->
											<!-- wp:social-link {"url":"#","service":"instagram"} /-->
											<!-- wp:social-link {"url":"#","service":"x"} /--></ul>
											<!-- /wp:social-links --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns --></div>
											<!-- /wp:group -->
											<!-- wp:group {"align":"full","className":"is-style-top-shadow","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|30"},"blockGap":"0px","margin":{"top":"0","bottom":"0"}},"elements":{"link":{"color":{"text":"var:preset|color|contrast-font"}}},"typography":{"fontSize":"13px"}},"backgroundColor":"base-heading","textColor":"contrast-font","layout":{"type":"constrained","contentSize":""}} -->
											<div class="wp-block-group alignfull is-style-top-shadow has-contrast-font-color has-base-heading-background-color has-text-color has-background has-link-color" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--30);font-size:13px"><!-- wp:columns {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
											<div class="wp-block-columns" style="margin-bottom:var(--wp--preset--spacing--30)"><!-- wp:column {"verticalAlignment":"bottom"} -->
											<div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:paragraph -->
											<p>© Kairaweb 2025</p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"bottom","style":{"spacing":{"blockGap":"5px"}}} -->
											<div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:paragraph {"align":"right"} -->
											<p class="has-text-align-right">Built by <a href="https://kairaweb.com/" target="_blank" rel="noreferrer noopener">Kaira</a></p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns --></div>
											<!-- /wp:group -->' ),
				)
			);
			register_block_pattern(
				'kwtsk/footer-simple-columns-dark',
				array(
					'title'       => __( 'Simple Columns Footer Dark', 'theme-site-kit' ),
					'description' => _x( 'A dark style footer with simple columns.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-footer' ),
					'content'     => trim( '<!-- wp:group {"align":"full","className":"is-style-top-shadow","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|70"},"blockGap":"0px","margin":{"top":"0px","bottom":"0px"}},"typography":{"fontSize":"14px"},"elements":{"link":{"color":{"text":"var:preset|color|contrast-font"}}}},"backgroundColor":"base-heading","textColor":"contrast-font","layout":{"type":"constrained","contentSize":""}} -->
											<div class="wp-block-group alignfull is-style-top-shadow has-contrast-font-color has-base-heading-background-color has-text-color has-background has-link-color" style="margin-top:0px;margin-bottom:0px;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--70);font-size:14px"><!-- wp:columns {"verticalAlignment":"top","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|70"},"margin":{"top":"0px","bottom":"0px"}}}} -->
											<div class="wp-block-columns are-vertically-aligned-top" style="margin-top:0px;margin-bottom:0px"><!-- wp:column {"verticalAlignment":"top","width":"","style":{"spacing":{"padding":{"right":"0","left":"0"}}}} -->
											<div class="wp-block-column is-vertically-aligned-top" style="padding-right:0;padding-left:0"><!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|base-bg"}}},"typography":{"fontSize":"22px"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|40"}}},"textColor":"base-bg"} -->
											<h4 class="wp-block-heading has-base-bg-color has-text-color has-link-color" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--40);font-size:22px">ABOUT US</h4>
											<!-- /wp:heading -->
											<!-- wp:group {"style":{"spacing":{"padding":{"right":"0","left":"0"}}},"layout":{"type":"constrained","contentSize":"300px","justifyContent":"left"}} -->
											<div class="wp-block-group" style="padding-right:0;padding-left:0"><!-- wp:paragraph -->
											<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sagittis risus ut tellus sodales, sed eleifend tellus hendrerit.</p>
											<!-- /wp:paragraph -->
											<!-- wp:paragraph -->
											<p>Donec sagittis risus ut tellus sodales, sed eleifend tellus hendrerit.</p>
											<!-- /wp:paragraph -->
											<!-- wp:paragraph {"style":{"color":{"text":"#c9c9c9"},"elements":{"link":{"color":{"text":"#c9c9c9"}}}}} -->
											<p class="has-text-color has-link-color" style="color:#c9c9c9"><a href="#" data-type="page" data-id="8">Read More</a></p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:group --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":""} -->
											<div class="wp-block-column is-vertically-aligned-top"><!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|base-bg"}}},"typography":{"fontSize":"22px"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|40"}}},"textColor":"base-bg"} -->
											<h4 class="wp-block-heading has-base-bg-color has-text-color has-link-color" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--40);font-size:22px">COMPANY</h4>
											<!-- /wp:heading -->
											<!-- wp:list {"className":"is-style-unstyled","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
											<ul style="margin-top:var(--wp--preset--spacing--50)" class="wp-block-list is-style-unstyled"><!-- wp:list-item -->
											<li>Home</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>About Us</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Our Services</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Meet the Team</li>
											<!-- /wp:list-item -->
											<!-- wp:list-item -->
											<li>Contact Us</li>
											<!-- /wp:list-item --></ul>
											<!-- /wp:list --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":""} -->
											<div class="wp-block-column is-vertically-aligned-top"><!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|base-bg"}}},"typography":{"fontSize":"22px"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|40"}}},"textColor":"base-bg"} -->
											<h4 class="wp-block-heading has-base-bg-color has-text-color has-link-color" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--40);font-size:22px">CONTACT US</h4>
											<!-- /wp:heading -->
											<!-- wp:group {"style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"0"}},"layout":{"type":"constrained"}} -->
											<div class="wp-block-group" style="padding-right:0;padding-left:0"><!-- wp:paragraph -->
											<p>Telephone: (021) 760 0044</p>
											<!-- /wp:paragraph -->
											<!-- wp:paragraph -->
											<p>Fax: (021) 760 0044</p>
											<!-- /wp:paragraph -->
											<!-- wp:paragraph -->
											<p>Email: email@yourdomain.com</p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:group -->
											<!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|base-bg"}}},"typography":{"fontSize":"22px"},"spacing":{"margin":{"top":"1.8rem","bottom":"var:preset|spacing|40"}}},"textColor":"base-bg"} -->
											<h4 class="wp-block-heading has-base-bg-color has-text-color has-link-color" style="margin-top:1.8rem;margin-bottom:var(--wp--preset--spacing--40);font-size:22px">ADDRESS</h4>
											<!-- /wp:heading -->
											<!-- wp:group {"style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"0"}},"layout":{"type":"constrained"}} -->
											<div class="wp-block-group" style="padding-right:0;padding-left:0"><!-- wp:paragraph -->
											<p>28 Youraddress Street</p>
											<!-- /wp:paragraph -->
											<!-- wp:paragraph -->
											<p>Fax: (021) 760 0044</p>
											<!-- /wp:paragraph -->
											<!-- wp:paragraph -->
											<p>Email: email@yourdomain.com</p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:group --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"top","width":""} -->
											<div class="wp-block-column is-vertically-aligned-top"><!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|base-bg"}}},"typography":{"fontSize":"22px"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|40"}}},"textColor":"base-bg"} -->
											<h4 class="wp-block-heading has-base-bg-color has-text-color has-link-color" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--40);font-size:22px">FIND US ONLINE</h4>
											<!-- /wp:heading -->
											<!-- wp:social-links {"iconColor":"base-heading","iconColorValue":"#222231","iconBackgroundColor":"contrast-font","iconBackgroundColorValue":"#f6f6f6","openInNewTab":true,"layout":{"type":"flex"}} -->
											<ul class="wp-block-social-links has-icon-color has-icon-background-color"><!-- wp:social-link {"url":"#","service":"facebook"} /-->
											<!-- wp:social-link {"url":"#","service":"instagram"} /-->
											<!-- wp:social-link {"url":"#","service":"x"} /--></ul>
											<!-- /wp:social-links --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns --></div>
											<!-- /wp:group -->
											<!-- wp:group {"align":"full","className":"is-style-top-shadow","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|30"},"blockGap":"0px","margin":{"top":"0","bottom":"0"}},"elements":{"link":{"color":{"text":"var:preset|color|contrast-font"}}},"typography":{"fontSize":"13px"}},"backgroundColor":"almost-black-bg","textColor":"contrast-font","layout":{"type":"constrained","contentSize":""}} -->
											<div class="wp-block-group alignfull is-style-top-shadow has-contrast-font-color has-almost-black-bg-background-color has-text-color has-background has-link-color" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--30);font-size:13px"><!-- wp:columns {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
											<div class="wp-block-columns" style="margin-bottom:var(--wp--preset--spacing--30)"><!-- wp:column {"verticalAlignment":"bottom"} -->
											<div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:paragraph -->
											<p>© Kairaweb 2025</p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:column -->
											<!-- wp:column {"verticalAlignment":"bottom","style":{"spacing":{"blockGap":"5px"}}} -->
											<div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:paragraph {"align":"right"} -->
											<p class="has-text-align-right">Built by <a href="https://kairaweb.com/" target="_blank" rel="noreferrer noopener">Kaira</a></p>
											<!-- /wp:paragraph --></div>
											<!-- /wp:column --></div>
											<!-- /wp:columns --></div>
											<!-- /wp:group -->' ),
				)
			);

			/*
			 * Content
			 */
			// Add more patterns as needed

			/*
			 * PRO: MAINTENANCE MODE Patterns
			 */
			if (kwtsk_fs()->can_use_premium_code__premium_only()) {
				// 1
				register_block_pattern(
					'kwtsk/mm-one',
					array(
						'title'       => __( 'Maintenance Mode', 'theme-site-kit' ),
						'description' => _x( 'A Maintenance Mode Template.', 'Block pattern description', 'theme-site-kit' ),
						'categories'  => array( 'kwtsk-maintenance' ),
						'content'     => trim( '<!-- wp:cover {"url":"' . $pattern_images_url . 'background-dots.png","id":5601,"isRepeated":true,"dimRatio":50,"customOverlayColor":"#fbfafa","isUserOverlayColor":false,"minHeight":100,"minHeightUnit":"vh","contentPosition":"center center","isDark":false,"sizeSlug":"full","align":"center","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"right":"0","left":"0"}}},"layout":{"type":"constrained"}} -->
												<div class="wp-block-cover aligncenter is-light is-repeated" style="margin-top:0;margin-bottom:0;padding-right:0;padding-left:0;min-height:100vh"><div class="wp-block-cover__image-background wp-image-5601 size-full is-repeated" style="background-position:50% 50%;background-image:url(' . $pattern_images_url . 'background-dots.png)"></div><span aria-hidden="true" class="wp-block-cover__background has-background-dim" style="background-color:#fbfafa"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|60","left":"var:preset|spacing|60","top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained","contentSize":"920px"}} -->
												<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:var(--wp--preset--spacing--60);padding-bottom:0;padding-left:var(--wp--preset--spacing--60)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
												<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:0;padding-bottom:0"><!-- wp:paragraph {"align":"center","metadata":{"name":"YOUR LOGO"},"style":{"typography":{"fontSize":"22px"}}} -->
												<p class="has-text-align-center" style="font-size:22px"><strong>ADD YOUR LOGO</strong></p>
												<!-- /wp:paragraph -->
												<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"letterSpacing":"5px"},"elements":{"link":{"color":{"text":"var:preset|color|base-heading"}}}},"textColor":"base-heading","fontSize":"xxxlarge"} -->
												<h1 class="wp-block-heading has-text-align-center has-base-heading-color has-text-color has-link-color has-xxxlarge-font-size" style="letter-spacing:5px">MAINTENANCE MODE</h1>
												<!-- /wp:heading --></div>
												<!-- /wp:group -->
												<!-- wp:group {"style":{"spacing":{"padding":{"top":"40px","bottom":"40px","left":"40px","right":"40px"},"blockGap":"var:preset|spacing|50"},"elements":{"link":{"color":{"text":"#f3f3f3"}}},"color":{"text":"#f3f3f3"}},"backgroundColor":"base-heading","layout":{"type":"constrained","contentSize":""}} -->
												<div class="wp-block-group has-base-heading-background-color has-text-color has-background has-link-color" style="color:#f3f3f3;padding-top:40px;padding-right:40px;padding-bottom:40px;padding-left:40px"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|70"}}}} -->
												<div class="wp-block-columns"><!-- wp:column -->
												<div class="wp-block-column"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontSize":"24px"}}} -->
												<h2 class="wp-block-heading has-text-align-center" style="font-size:24px">Create A Vision</h2>
												<!-- /wp:heading -->
												<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"14px"}}} -->
												<p class="has-text-align-center" style="font-size:14px">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In eu tincidunt velit.</p>
												<!-- /wp:paragraph --></div>
												<!-- /wp:column -->
												<!-- wp:column -->
												<div class="wp-block-column"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontSize":"24px"}}} -->
												<h2 class="wp-block-heading has-text-align-center" style="font-size:24px">Build Your Empire</h2>
												<!-- /wp:heading -->
												<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"14px"}}} -->
												<p class="has-text-align-center" style="font-size:14px">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In eu tincidunt velit.</p>
												<!-- /wp:paragraph --></div>
												<!-- /wp:column -->
												<!-- wp:column -->
												<div class="wp-block-column"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontSize":"24px"}}} -->
												<h2 class="wp-block-heading has-text-align-center" style="font-size:24px">Ready to Launch</h2>
												<!-- /wp:heading -->
												<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"14px"}}} -->
												<p class="has-text-align-center" style="font-size:14px">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In eu tincidunt velit.</p>
												<!-- /wp:paragraph --></div>
												<!-- /wp:column --></div>
												<!-- /wp:columns -->
												<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"50px","bottom":"50px"}},"typography":{"fontSize":"24px"}}} -->
												<p class="has-text-align-center" style="margin-top:50px;margin-bottom:50px;font-size:24px">Our website is coming soon... Stay Tuned!</p>
												<!-- /wp:paragraph -->
												<!-- wp:social-links {"iconColor":"base-heading","iconColorValue":"#222231","iconBackgroundColor":"contrast-font","iconBackgroundColorValue":"#f6f6f6","openInNewTab":true,"align":"center","layout":{"type":"flex","justifyContent":"center"}} -->
												<ul class="wp-block-social-links aligncenter has-icon-color has-icon-background-color"><!-- wp:social-link {"url":"#","service":"facebook"} /-->
												<!-- wp:social-link {"url":"#","service":"x"} /-->
												<!-- wp:social-link {"url":"#","service":"instagram"} /-->
												<!-- wp:social-link {"url":"#","service":"linkedin"} /--></ul>
												<!-- /wp:social-links --></div>
												<!-- /wp:group --></div>
												<!-- /wp:group --></div></div>
												<!-- /wp:cover -->'
						),
					)
				);
				// 2
				register_block_pattern(
					'kwtsk/mm-two',
					array(
						'title'       => __( 'Maintenance Mode', 'theme-site-kit' ),
						'description' => _x( 'A Maintenance Mode Template.', 'Block pattern description', 'theme-site-kit' ),
						'categories'  => array( 'kwtsk-maintenance' ),
						'content'     => trim( '<!-- wp:cover {"dimRatio":0,"isUserOverlayColor":false,"minHeight":100,"minHeightUnit":"vh","contentPosition":"center center","isDark":false,"sizeSlug":"full","align":"center","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"right":"0","left":"0"}}},"layout":{"type":"constrained"}} -->
												<div class="wp-block-cover aligncenter is-light" style="margin-top:0;margin-bottom:0;padding-right:0;padding-left:0;min-height:100vh"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|60","left":"var:preset|spacing|60","top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained","contentSize":"920px"}} -->
												<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:var(--wp--preset--spacing--60);padding-bottom:0;padding-left:var(--wp--preset--spacing--60)"><!-- wp:columns {"verticalAlignment":null} -->
												<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast-font"}}},"spacing":{"padding":{"right":"var:preset|spacing|60","left":"var:preset|spacing|60","top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"}}},"backgroundColor":"base-heading","textColor":"contrast-font"} -->
												<div class="wp-block-column is-vertically-aligned-center has-contrast-font-color has-base-heading-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--60)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
												<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:paragraph {"metadata":{"name":"YOUR LOGO"},"style":{"typography":{"fontSize":"22px"}}} -->
												<p style="font-size:22px"><strong>ADD YOUR LOGO</strong></p>
												<!-- /wp:paragraph -->
												<!-- wp:heading {"level":1,"style":{"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"textColor":"white","fontSize":"xxlarge"} -->
												<h1 class="wp-block-heading has-white-color has-text-color has-link-color has-xxlarge-font-size">We\'re Working on it!</h1>
												<!-- /wp:heading --></div>
												<!-- /wp:group -->
												<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"50px","bottom":"50px"}},"typography":{"fontSize":"24px"}}} -->
												<p style="margin-top:50px;margin-bottom:50px;font-size:24px">Stay Tuned... We\'re working on something great and will have it ready soon!</p>
												<!-- /wp:paragraph -->
												<!-- wp:social-links {"iconColor":"base-heading","iconColorValue":"#222231","iconBackgroundColor":"contrast-font","iconBackgroundColorValue":"#f6f6f6","openInNewTab":true,"align":"center","layout":{"type":"flex","justifyContent":"center"}} -->
												<ul class="wp-block-social-links aligncenter has-icon-color has-icon-background-color"><!-- wp:social-link {"url":"#","service":"facebook"} /-->
												<!-- wp:social-link {"url":"#","service":"x"} /-->
												<!-- wp:social-link {"url":"#","service":"instagram"} /-->
												<!-- wp:social-link {"url":"#","service":"linkedin"} /--></ul>
												<!-- /wp:social-links --></div>
												<!-- /wp:column -->
												<!-- wp:column -->
												<div class="wp-block-column"><!-- wp:image {"id":58,"sizeSlug":"full","linkDestination":"none","style":{"spacing":{"margin":{"right":"0","top":"0","bottom":"0","left":"-24px"}}}} -->
												<figure class="wp-block-image size-full" style="margin-top:0;margin-right:0;margin-bottom:0;margin-left:-24px"><img src="' . $pattern_images_url . 'maintenance-1.png" alt="" class="wp-image-58"/></figure>
												<!-- /wp:image --></div>
												<!-- /wp:column --></div>
												<!-- /wp:columns --></div>
												<!-- /wp:group --></div></div>
												<!-- /wp:cover -->'
						),
					)
				);
				// 3
				register_block_pattern(
					'kwtsk/mm-three',
					array(
						'title'       => __( 'Maintenance Mode', 'theme-site-kit' ),
						'description' => _x( 'A Maintenance Mode Template.', 'Block pattern description', 'theme-site-kit' ),
						'categories'  => array( 'kwtsk-maintenance' ),
						'content'     => trim( '<!-- wp:cover {"dimRatio":0,"isUserOverlayColor":false,"minHeight":100,"minHeightUnit":"vh","contentPosition":"center center","isDark":false,"sizeSlug":"full","align":"center","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"right":"0","left":"0"}}},"layout":{"type":"constrained"}} -->
												<div class="wp-block-cover aligncenter is-light" style="margin-top:0;margin-bottom:0;padding-right:0;padding-left:0;min-height:100vh"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|60","left":"var:preset|spacing|60","top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained","contentSize":"920px"}} -->
												<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:var(--wp--preset--spacing--60);padding-bottom:0;padding-left:var(--wp--preset--spacing--60)"><!-- wp:columns {"verticalAlignment":null} -->
												<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"bottom"} -->
												<div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:image {"id":56,"sizeSlug":"full","linkDestination":"none","style":{"spacing":{"margin":{"right":"0","top":"0","bottom":"0","left":"-24px"}}}} -->
												<figure class="wp-block-image size-full" style="margin-top:0;margin-right:0;margin-bottom:0;margin-left:-24px"><img src="' . $pattern_images_url . 'maintenance-2.png" alt="" class="wp-image-56"/></figure>
												<!-- /wp:image --></div>
												<!-- /wp:column -->
												<!-- wp:column {"verticalAlignment":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast-font"}}},"spacing":{"padding":{"right":"var:preset|spacing|60","left":"var:preset|spacing|60","top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"}}},"backgroundColor":"brand","textColor":"contrast-font"} -->
												<div class="wp-block-column is-vertically-aligned-center has-contrast-font-color has-brand-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--60)"><!-- wp:social-links {"iconColor":"base-heading","iconColorValue":"#222231","iconBackgroundColor":"contrast-font","iconBackgroundColorValue":"#f6f6f6","openInNewTab":true,"align":"center","layout":{"type":"flex","justifyContent":"center"}} -->
												<ul class="wp-block-social-links aligncenter has-icon-color has-icon-background-color"><!-- wp:social-link {"url":"#","service":"facebook"} /-->
												<!-- wp:social-link {"url":"#","service":"x"} /-->
												<!-- wp:social-link {"url":"#","service":"instagram"} /-->
												<!-- wp:social-link {"url":"#","service":"linkedin"} /--></ul>
												<!-- /wp:social-links -->
												<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"constrained"}} -->
												<div class="wp-block-group" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:heading {"level":1,"style":{"elements":{"link":{"color":{"text":"var:preset|color|white"}}},"typography":{"letterSpacing":"4px"}},"textColor":"white","fontSize":"xxlarge"} -->
												<h1 class="wp-block-heading has-white-color has-text-color has-link-color has-xxlarge-font-size" style="letter-spacing:4px">COMING SOON !</h1>
												<!-- /wp:heading -->
												<!-- wp:paragraph {"metadata":{"name":"YOUR LOGO"},"style":{"typography":{"fontSize":"22px"}}} -->
												<p style="font-size:22px"><strong>ADD YOUR LOGO</strong></p>
												<!-- /wp:paragraph --></div>
												<!-- /wp:group -->
												<!-- wp:paragraph {"style":{"typography":{"fontSize":"24px"}}} -->
												<p style="font-size:24px">Stay Tuned... We\'re working on something great and will have it ready soon!</p>
												<!-- /wp:paragraph -->
												<!-- wp:group {"style":{"spacing":{"padding":{"right":"0","left":"0"}}},"layout":{"type":"constrained"}} -->
												<div class="wp-block-group" style="padding-right:0;padding-left:0"><!-- wp:paragraph -->
												<p><strong>Signup to our newsletter:</strong></p>
												<!-- /wp:paragraph -->
												<!-- wp:search {"label":"","showLabel":false,"placeholder":"Email Address","buttonText":"Signup","style":{"spacing":{"margin":{"right":"0","left":"0"}}}} /--></div>
												<!-- /wp:group --></div>
												<!-- /wp:column --></div>
												<!-- /wp:columns --></div>
												<!-- /wp:group --></div></div>
												<!-- /wp:cover -->'
						),
					)
				);
				// 4
				register_block_pattern(
					'kwtsk/mm-four',
					array(
						'title'       => __( 'Maintenance Mode', 'theme-site-kit' ),
						'description' => _x( 'A Maintenance Mode Template.', 'Block pattern description', 'theme-site-kit' ),
						'categories'  => array( 'kwtsk-maintenance' ),
						'content'     => trim( '<!-- wp:cover {"overlayColor":"contrast-font","isUserOverlayColor":true,"minHeight":100,"minHeightUnit":"vh","contentPosition":"center center","isDark":false,"sizeSlug":"full","metadata":{"categories":["kwtsk-maintenance"],"patternName":"kwtsk/mm-one","name":"Maintenance Mode"},"align":"center","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"right":"0","left":"0"}}},"layout":{"type":"constrained"}} -->
												<div class="wp-block-cover aligncenter is-light" style="margin-top:0;margin-bottom:0;padding-right:0;padding-left:0;min-height:100vh"><span aria-hidden="true" class="wp-block-cover__background has-contrast-font-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|60","left":"var:preset|spacing|60","top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained","contentSize":"920px"}} -->
												<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:var(--wp--preset--spacing--60);padding-bottom:0;padding-left:var(--wp--preset--spacing--60)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
												<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:0;padding-bottom:0"><!-- wp:image {"id":57,"sizeSlug":"full","linkDestination":"none","align":"center"} -->
												<figure class="wp-block-image aligncenter size-full"><img src="' . $pattern_images_url . 'maintenance-3.png" alt="" class="wp-image-57"/></figure>
												<!-- /wp:image --></div>
												<!-- /wp:group -->
												<!-- wp:group {"style":{"spacing":{"padding":{"top":"20px","bottom":"20px","left":"40px","right":"40px"},"blockGap":"0.8rem","margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained","contentSize":""}} -->
												<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:20px;padding-right:40px;padding-bottom:20px;padding-left:40px"><!-- wp:social-links {"openInNewTab":true,"align":"center","layout":{"type":"flex","justifyContent":"center"}} -->
												<ul class="wp-block-social-links aligncenter"><!-- wp:social-link {"url":"#","service":"facebook"} /-->
												<!-- wp:social-link {"url":"#","service":"x"} /-->
												<!-- wp:social-link {"url":"#","service":"instagram"} /-->
												<!-- wp:social-link {"url":"#","service":"linkedin"} /--></ul>
												<!-- /wp:social-links -->
												<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"letterSpacing":"5px"},"elements":{"link":{"color":{"text":"var:preset|color|base-heading"}}}},"textColor":"base-heading","fontSize":"xxxlarge"} -->
												<h1 class="wp-block-heading has-text-align-center has-base-heading-color has-text-color has-link-color has-xxxlarge-font-size" style="letter-spacing:5px">MAINTENANCE MODE</h1>
												<!-- /wp:heading -->
												<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"24px"}}} -->
												<p class="has-text-align-center" style="font-size:24px">Our website is coming soon... Stay Tuned!</p>
												<!-- /wp:paragraph -->
												<!-- wp:paragraph {"align":"center","metadata":{"name":"YOUR LOGO"},"style":{"typography":{"fontSize":"22px"}}} -->
												<p class="has-text-align-center" style="font-size:22px"><strong>ADD YOUR LOGO</strong></p>
												<!-- /wp:paragraph --></div>
												<!-- /wp:group --></div>
												<!-- /wp:group --></div></div>
												<!-- /wp:cover -->'
						),
					)
				);
			}
		}
	}

}
new KWTSK_Patterns();
