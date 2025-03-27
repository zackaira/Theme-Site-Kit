<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Frontend functions.
 */
class Theme_Site_Kit_Frontend {
    /**
     * Constructor function.
     */
    public function __construct() {
        $kwtskSavedOptions = get_option( 'kwtsk_options' );
        $kwtskOptions      = $kwtskSavedOptions ? json_decode( $kwtskSavedOptions ) : null;

        add_filter( 'body_class', array( $this, 'kwtsk_frontend_body_classes' ));

        // Only hook our changes if mobile menu is enabled.
        if ( isset( $kwtskOptions->social->enabled ) && $kwtskOptions->social->enabled === true ) {
            add_action( 'wp_footer', array( $this, 'kwtsk_add_footer_social_icons' ), 10, 1);
        }

        // Only hook our changes if mobile menu is enabled.
        if ( isset( $kwtskOptions->mobilemenu->enabled ) && $kwtskOptions->mobilemenu->enabled === true ) {
            // Register our custom attributes for the navigation block.
            add_filter( 'register_block_type_args', array( $this, 'kwtsk_register_nav_attributes' ), 10, 2 );
            // Add custom classes to the navigation block on the frontend.
            add_action( 'render_block', array( $this, 'kwtsk_add_nav_classes' ), 10, 2 );
            if ( isset( $kwtskOptions->mobilemenu->style ) && $kwtskOptions->mobilemenu->style === 'custom' ) {
                add_action( 'wp_enqueue_scripts', array( $this, 'kwtsk_add_nav_custom_styles' ), 10, 2 );
            }
        }
    }

    /**
	 * Function to check for active plugins
	 */
	public function kwtsk_frontend_body_classes($classes) {
		$kwtskSavedOptions = get_option( 'kwtsk_options' );
        $kwtskOptions      = $kwtskSavedOptions ? json_decode( $kwtskSavedOptions ) : null;

		if ( isset( $kwtskOptions->social->enabled ) && $kwtskOptions->social->enabled === true ) {
			$classes[] = sanitize_html_class('kwtsk-hide-icons');
		}

        return $classes;
    }

    /**
	 * Add Social Icons
	 */
	public function kwtsk_add_footer_social_icons() {
		$allowed_html = array( 'div' => array('id' => array()) );
		$html = '<div id="kwtsk-social-root"></div>';
	
		echo wp_kses($html ,$allowed_html);
	}

    /**
     * Register custom attributes for the core/navigation block.
     *
     * @param array  $settings Block settings.
     * @param string $name     Block name.
     * @return array Modified block settings.
     */
    public function kwtsk_register_nav_attributes( $settings, $name ) {
        if ( 'core/navigation' !== $name ) {
            return $settings;
        }

        // Merge our new attributes into the block's existing attributes.
        $settings['attributes'] = array_merge(
            isset( $settings['attributes'] ) ? $settings['attributes'] : array(),
            array(
                'kwtskEnableMobileMenu' => array(
                    'type'    => 'boolean',
                    'default' => false,
                ),
                'kwtskPosition' => array(
                    'type'    => 'string',
                    'default' => 'right',
                ),
            )
        );

        return $settings;
    }

    /**
     * Add custom classes to the navigation block for the Mobile Menu on the frontend.
     *
     * @param string $block_content The block's content.
     * @param array  $block         Block details.
     * @return string Modified block content.
     */
    public function kwtsk_add_nav_classes( $block_content, $block ) {
        $kwtskSavedOptions = get_option( 'kwtsk_options' );
        $kwtskOptions      = $kwtskSavedOptions ? json_decode( $kwtskSavedOptions ) : null;

        if ( empty( $block['blockName'] ) || 'core/navigation' !== $block['blockName'] ) {
            return $block_content;
        }

        // Only add classes if the Mobile Menu is enabled.
        if ( empty( $block['attrs']['kwtskEnableMobileMenu'] ) ) {
            return $block_content;
        }

        $position         = isset( $block['attrs']['kwtskPosition'] ) ? $block['attrs']['kwtskPosition'] : 'right';
        $style            = isset( $kwtskOptions->mobilemenu->style ) ? $kwtskOptions->mobilemenu->style : 'dark';
        $additional_class = "kwtsk-mm kwtsk-mm-$position kwtsk-$style";

        // Append the additional class to the first class attribute in the block's wrapper.
        $block_content = preg_replace(
            '/class="([^"]*)"/',
            'class="$1 ' . esc_attr( $additional_class ) . '"',
            $block_content,
            1
        );

        return $block_content;
    }

    /**
     * Add custom styles to the navigation block for the Mobile Menu on the frontend.
     *
     * @param string $block_content The block's content.
     * @param array  $block         Block details.
     * @return string Modified block content.
     */
    public function kwtsk_add_nav_custom_styles() {
        $kwtskSavedOptions = get_option( 'kwtsk_options' );
        $kwtskOptions      = $kwtskSavedOptions ? json_decode( $kwtskSavedOptions ) : null;
        $bgcolor          = isset( $kwtskOptions->mobilemenu->bgcolor ) ? $kwtskOptions->mobilemenu->bgcolor : '#1d2327';
        $textcolor        = isset( $kwtskOptions->mobilemenu->textcolor ) ? $kwtskOptions->mobilemenu->textcolor : '#b4b4b4';
        $selectedcolor    = isset( $kwtskOptions->mobilemenu->selectedcolor ) ? $kwtskOptions->mobilemenu->selectedcolor : '#FFFFFF';
    
        // Register a style handle without a file.
        wp_register_style( 'kwtsk-custom-nav-style', false );
        wp_enqueue_style( 'kwtsk-custom-nav-style' );
        
        // Define your custom inline CSS.
        $custom_css = "
            .wp-block-navigation.kwtsk-mm.kwtsk-custom.mobile-on
                .wp-block-navigation__responsive-container {
                background-color: $bgcolor !important;
                color: $textcolor !important;
                fill: $textcolor !important;
                box-shadow:
                    inset 1px 0 0 rgb(0 0 0 / 14%),
                    inset 2px 0 15px rgb(0 0 0 / 10%);
            }
            body.kwtsk-mmenu
                header.wp-block-template-part
                nav.wp-block-navigation.kwtsk-custom
                .wp-block-navigation__container
                li:hover > a,
            body.kwtsk-mmenu
                header.wp-block-template-part
                nav.wp-block-navigation.kwtsk-custom
                .wp-block-navigation__container
                li a.current-menu-ancestor,
            body.kwtsk-mmenu
                header.wp-block-template-part
                nav.wp-block-navigation.kwtsk-custom
                .wp-block-navigation__container
                li.current-menu-item > a {
                    color: $selectedcolor !important;
            }
        ";
        wp_add_inline_style( 'kwtsk-custom-nav-style', $custom_css );
    }
}

new Theme_Site_Kit_Frontend();
