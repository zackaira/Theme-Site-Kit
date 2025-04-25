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
			$pattern_images_url = esc_url( KWTSK_PLUGIN_URL . 'assets/patterns/' );
			
			/*
			 * Headers
			 */
			register_block_pattern(
				'kwtsk/header-one',
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
						<!-- /wp:group -->'
					),
				)
			);
			register_block_pattern(
				'kwtsk/header-two',
				array(
					'title'       => __( 'Two Header', 'theme-site-kit' ),
					'description' => _x( 'A Two header.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-header' ),
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
						<!-- /wp:group -->'
					),
				)
			);
			register_block_pattern(
				'kwtsk/header-three',
				array(
					'title'       => __( ' Header', 'theme-site-kit' ),
					'description' => _x( 'A Two header.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-header' ),
					'content'     => trim( '<!-- wp:group {"style":{"spacing":{"padding":{"right":"20px","top":"15px","bottom":"0px","left":"20px"},"blockGap":"0px"},"position":{"type":""}},"textColor":"black","layout":{"type":"constrained","justifyContent":"center","contentSize":""}} -->
						<div class="wp-block-group has-black-color has-text-color" style="padding-top:15px;padding-right:20px;padding-bottom:0px;padding-left:20px"><!-- wp:columns {"verticalAlignment":"center","isStackedOnMobile":false,"className":"headler-top-columns","style":{"spacing":{"blockGap":{"top":"0","left":"0"},"margin":{"top":"0","bottom":"0"},"padding":{"top":"0","bottom":"0"}}}} -->
						<div class="wp-block-columns are-vertically-aligned-center is-not-stacked-on-mobile headler-top-columns" style="margin-top:0;margin-bottom:0;padding-top:0;padding-bottom:0"><!-- wp:column {"verticalAlignment":"center","width":"","className":"header-logo-column","style":{"spacing":{"blockGap":"0","padding":{"right":"0","left":"0"}}}} -->
						<div class="wp-block-column is-vertically-aligned-center header-logo-column" style="padding-right:0;padding-left:0"><!-- wp:blockons/icon-list {"alignment":"align-right","listItems":[{"itemId":"021-762-4444","itemText":"(021) 762 4444","itemTextSize":null,"itemTextColor":null,"itemIcon":"phone","itemIconSize":null,"itemIconColor":null,"itemSpacing":""},{"itemId":"emailyourdomaincom","itemText":"email@yourdomain.com","itemTextSize":null,"itemTextColor":null,"itemIcon":"envelope","itemIconSize":null,"itemIconColor":null,"itemSpacing":""}],"listItemsLayout":"horizontal","listItemSpacing":14,"listItemIconSize":16,"listItemFontSize":15} -->
						<div class="wp-block-blockons-icon-list align-right items-horizontal"><div class="blockons-list-align"><ul class="blockons-list-wrap"><li class="blockons-list-item" style="margin-right:14px;font-size:15px;color:#4f4f4f"><div class="blockons-list-item-icon" style="margin-right:5px;font-size:16px;color:#4f4f4f"><span class="blockons-fontawesome fa-solid fa-phone" style="font-size:inherit"></span></div><div class="blockons-list-item-text">(021) 762 4444</div></li><li class="blockons-list-item" style="margin-right:14px;font-size:15px;color:#4f4f4f"><div class="blockons-list-item-icon" style="margin-right:5px;font-size:16px;color:#4f4f4f"><span class="blockons-fontawesome fa-solid fa-envelope" style="font-size:inherit"></span></div><div class="blockons-list-item-text">email@yourdomain.com</div></li></ul></div></div>
						<!-- /wp:blockons/icon-list --></div>
						<!-- /wp:column --></div>
						<!-- /wp:columns --></div>
						<!-- /wp:group -->
						<!-- wp:group {"style":{"spacing":{"padding":{"right":"20px","top":"0px","bottom":"15px","left":"20px"},"blockGap":"0px"},"position":{"type":""}},"textColor":"black","layout":{"type":"constrained","justifyContent":"center","contentSize":""}} -->
						<div class="wp-block-group has-black-color has-text-color" style="padding-top:0px;padding-right:20px;padding-bottom:15px;padding-left:20px"><!-- wp:columns {"className":"headler-top-columns","style":{"spacing":{"blockGap":{"top":"0","left":"var:preset|spacing|20"},"padding":{"top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"}}}} -->
						<div class="wp-block-columns headler-top-columns" style="margin-top:0;margin-bottom:0;padding-top:0;padding-bottom:0"><!-- wp:column {"verticalAlignment":"center","width":"280px","className":"header-logo-column","style":{"spacing":{"blockGap":"0"}}} -->
						<div class="wp-block-column is-vertically-aligned-center header-logo-column" style="flex-basis:280px"><!-- wp:site-logo {"style":{"spacing":{"margin":{"top":"-45px"}}}} /--></div>
						<!-- /wp:column -->
						<!-- wp:column {"verticalAlignment":"center","className":"header-nav-column","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
						<div class="wp-block-column is-vertically-aligned-center header-nav-column" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"className":"header-nav-row","style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"0"},"padding":{"right":"0","left":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
						<div class="wp-block-group header-nav-row" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:0;padding-right:0;padding-left:0"><!-- wp:navigation {"ref":1140,"textColor":"site-dark","icon":"menu","overlayBackgroundColor":"basebg","overlayTextColor":"textonbase","className":"custom-mobile-menu","style":{"typography":{"textTransform":"uppercase"},"spacing":{"blockGap":"2.4rem"},"layout":{"selfStretch":"fill","flexSize":null}},"fontFamily":"inter","layout":{"type":"flex","justifyContent":"right"}} /-->
						<!-- wp:buttons {"className":"nav-contact-btn","style":{"spacing":{"blockGap":{"top":"0","left":"0"},"padding":{"right":"0","left":"0"}}}} -->
						<div class="wp-block-buttons nav-contact-btn" style="padding-right:0;padding-left:0"><!-- wp:button {"style":{"spacing":{"padding":{"top":"0.7rem","bottom":"0.7rem"}}}} -->
						<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" style="padding-top:0.7rem;padding-bottom:0.7rem">Contact Us</a></div>
						<!-- /wp:button --></div>
						<!-- /wp:buttons --></div>
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
		}
	}

}
new Theme_Site_Kit_Patterns();
