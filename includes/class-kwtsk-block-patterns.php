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
				'kwtsk/footer-two',
				array(
					'title'       => __( 'Two Footer', 'theme-site-kit' ),
					'description' => _x( 'A Two Footer.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-footer' ),
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
			register_block_pattern(
				'kwtsk/footer-three',
				array(
					'title'       => __( 'Another Footer', 'theme-site-kit' ),
					'description' => _x( 'A Two Footer.', 'Block pattern description', 'theme-site-kit' ),
					'categories'  => array( 'kwtsk-footer' ),
					'content'     => trim( '<!-- wp:group {"align":"full","className":"is-style-top-shadow","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","right":"20px","left":"20px"},"blockGap":"0px","margin":{"top":"0px","bottom":"0px"}}},"layout":{"type":"constrained","contentSize":""}} -->
						<div class="wp-block-group alignfull is-style-top-shadow" style="margin-top:0px;margin-bottom:0px;padding-top:var(--wp--preset--spacing--80);padding-right:20px;padding-bottom:var(--wp--preset--spacing--80);padding-left:20px"><!-- wp:columns {"verticalAlignment":"top","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"},"margin":{"top":"0px","bottom":"0px"}}}} -->
						<div class="wp-block-columns are-vertically-aligned-top" style="margin-top:0px;margin-bottom:0px"><!-- wp:column {"verticalAlignment":"top","width":"37%","style":{"spacing":{"blockGap":"12px","padding":{"right":"0","left":"0"}}}} -->
						<div class="wp-block-column is-vertically-aligned-top" style="padding-right:0;padding-left:0;flex-basis:37%"><!-- wp:group {"style":{"spacing":{"padding":{"right":"0","left":"0"}}},"layout":{"type":"constrained","contentSize":"300px","justifyContent":"left"}} -->
						<div class="wp-block-group" style="padding-right:0;padding-left:0"><!-- wp:site-logo /-->
						<!-- wp:paragraph -->
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In pulvinar vel odio sit amet tempus. Vestibulum lacinia rhoncus lorem. Pellentesque consequat dignissim lacus vel sollicitudin.</p>
						<!-- /wp:paragraph -->
						<!-- wp:paragraph -->
						<p><a href="#" data-type="page" data-id="8">Read More</a></p>
						<!-- /wp:paragraph --></div>
						<!-- /wp:group --></div>
						<!-- /wp:column -->
						<!-- wp:column {"verticalAlignment":"top","width":"","style":{"spacing":{"blockGap":"12px"}}} -->
						<div class="wp-block-column is-vertically-aligned-top"><!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|site-dark"}}},"typography":{"fontSize":"23px"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|40"}}},"textColor":"site-dark"} -->
						<h4 class="wp-block-heading has-site-dark-color has-text-color has-link-color" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--40);font-size:23px">Overview</h4>
						<!-- /wp:heading -->
						<!-- wp:list {"className":"is-style-unstyled"} -->
						<ul class="wp-block-list is-style-unstyled"><!-- wp:list-item -->
						<li><a href="#" data-type="page" data-id="8">About</a></li>
						<!-- /wp:list-item -->
						<!-- wp:list-item -->
						<li>- <a href="#" data-type="page" data-id="11">Staff</a></li>
						<!-- /wp:list-item -->
						<!-- wp:list-item -->
						<li>- <a href="#" data-type="page" data-id="13">Gallery</a></li>
						<!-- /wp:list-item -->
						<!-- wp:list-item -->
						<li><a href="#" data-type="page" data-id="21">Articles</a></li>
						<!-- /wp:list-item -->
						<!-- wp:list-item -->
						<li><a href="#" data-type="category" data-id="6">Latest News</a></li>
						<!-- /wp:list-item -->
						<!-- wp:list-item -->
						<li><a href="#" data-type="page" data-id="15">Contact</a></li>
						<!-- /wp:list-item --></ul>
						<!-- /wp:list --></div>
						<!-- /wp:column -->
						<!-- wp:column {"verticalAlignment":"top","width":"32%","style":{"spacing":{"blockGap":"12px"}}} -->
						<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:32%"><!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|site-dark"}}},"typography":{"fontSize":"23px"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|40"}}},"textColor":"site-dark"} -->
						<h4 class="wp-block-heading has-site-dark-color has-text-color has-link-color" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--40);font-size:23px">Business Info</h4>
						<!-- /wp:heading -->
						<!-- wp:paragraph {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}}} -->
						<p style="margin-bottom:var(--wp--preset--spacing--50)">2 Roadname Road, Suburb, 7800,<br>Western Cape<br>P O Box 888444, Suburb, 7800</p>
						<!-- /wp:paragraph -->
						<!-- wp:blockons/marketing-button {"mbMinWidth":265,"theIcon":"phone","iconPosition":"three","title":"(021) 744 8644","showSubText":false,"bRadius":50,"vertPad":0,"horizPad":28,"bgColor":"#fef6f2","borderColor":"#fef6f2","titleColor":"#393939","iconColor":"#393939"} -->
						<div class="wp-block-blockons-marketing-button align-center left-align design-one icon-pos-three"><div class="blockons-marketing-button-block"><a class="blockons-marketing-button" style="padding-left:28px;padding-right:32px;padding-top:0;padding-bottom:0;min-width:265px;min-height:50px;border-radius:50px;background-color:#fef6f2;border-color:#fef6f2" rel="noopener"><div class="blockons-mb-icon" style="margin-right:8px;color:#393939"><span class="blockons-fontawesome fa-solid fa-phone" style="font-size:16px"></span></div><div class="blockons-marketing-button-inner"><div class="blockons-marketing-button-title-wrap"><div class="blockons-marketing-button-title" style="color:#393939;font-size:19px">(021) 744 8644</div></div></div></a></div></div>
						<!-- /wp:blockons/marketing-button -->
						<!-- wp:blockons/marketing-button {"mbMinWidth":265,"theIcon":"envelope","iconPosition":"three","title":"email@youromain.com","showSubText":false,"bRadius":50,"vertPad":0,"horizPad":28,"bgColor":"#fef6f2","borderColor":"#fef6f2","titleColor":"#393939","iconColor":"#393939"} -->
						<div class="wp-block-blockons-marketing-button align-center left-align design-one icon-pos-three"><div class="blockons-marketing-button-block"><a class="blockons-marketing-button" style="padding-left:28px;padding-right:32px;padding-top:0;padding-bottom:0;min-width:265px;min-height:50px;border-radius:50px;background-color:#fef6f2;border-color:#fef6f2" rel="noopener"><div class="blockons-mb-icon" style="margin-right:8px;color:#393939"><span class="blockons-fontawesome fa-solid fa-envelope" style="font-size:16px"></span></div><div class="blockons-marketing-button-inner"><div class="blockons-marketing-button-title-wrap"><div class="blockons-marketing-button-title" style="color:#393939;font-size:19px">email@youromain.com</div></div></div></a></div></div>
						<!-- /wp:blockons/marketing-button --></div>
						<!-- /wp:column --></div>
						<!-- /wp:columns --></div>
						<!-- /wp:group -->
						<!-- wp:group {"align":"full","className":"is-style-top-shadow","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|40","right":"var:preset|spacing|50","left":"var:preset|spacing|50"},"blockGap":"0px","margin":{"top":"0","bottom":"0"}},"color":{"background":"#414141"},"elements":{"link":{"color":{"text":"var:preset|color|site-background"}}}},"textColor":"site-background","layout":{"type":"constrained","contentSize":""}} -->
						<div class="wp-block-group alignfull is-style-top-shadow has-site-background-color has-text-color has-background has-link-color" style="background-color:#414141;margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
						<div class="wp-block-columns" style="margin-bottom:var(--wp--preset--spacing--30)"><!-- wp:column {"verticalAlignment":"bottom"} -->
						<div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:paragraph {"fontSize":"xsmall"} -->
						<p class="has-xsmall-font-size">© YourBusinessName 2025</p>
						<!-- /wp:paragraph --></div>
						<!-- /wp:column -->
						<!-- wp:column {"verticalAlignment":"bottom","style":{"spacing":{"blockGap":"5px"}}} -->
						<div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:paragraph {"align":"right","fontSize":"xsmall"} -->
						<p class="has-text-align-right has-xsmall-font-size"><mark style="background-color:rgba(0, 0, 0, 0)" class="has-inline-color has-light-grey-color">Built by <a href="https://kairaweb.com/" target="_blank" rel="noreferrer noopener">Kaira</a></mark></p>
						<!-- /wp:paragraph --></div>
						<!-- /wp:column --></div>
						<!-- /wp:columns --></div>
						<!-- /wp:group -->'
					),
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
