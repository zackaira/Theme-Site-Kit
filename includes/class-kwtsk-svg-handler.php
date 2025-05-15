<?php
/**
 * Enable secure SVG upload and display functionality
 * 
 * @package Theme_Site_Kit
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * SVG Handler class.
 */
class KWTSK_SVG_Handler {
    /**
     * Initialize the SVG handler
     */
    public function __construct() {
        $kwtskSavedOptions = get_option('kwtsk_options');
        $kwtskOptions = $kwtskSavedOptions ? json_decode($kwtskSavedOptions) : null;

        // Check if SVG upload is enabled in the settings.
        // If not set or not enabled, do nothing.
        if (
            !isset($kwtskOptions->svgupload) ||
            !isset($kwtskOptions->svgupload->enabled) ||
            $kwtskOptions->svgupload->enabled !== true
        ) {
            return;
        }

        // Add SVG to allowed mime types
        add_filter('upload_mimes', array($this, 'kwtsk_add_svg_mime_type'));
        
        // Basic SVG support
        add_filter('wp_check_filetype_and_ext', array($this, 'kwtsk_check_svg_filetype'), 10, 4);
        add_filter('wp_handle_upload_prefilter', array($this, 'kwtsk_sanitize_svg'));
        
        // Fix display issues
        add_filter('wp_get_attachment_image_src', array($this, 'kwtsk_fix_svg_size_attributes'), 10, 4);
        add_filter('wp_get_attachment_metadata', array($this, 'kwtsk_fix_svg_metadata'), 10, 2);
        
        // Add CSS fixes
        add_action('admin_head', array($this, 'kwtsk_add_svg_css'));
        add_action('wp_head', array($this, 'kwtsk_add_svg_css'));
    }

    /**
     * Add SVG mime type
     */
    public function kwtsk_add_svg_mime_type($mimes) {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }

    /**
     * Check SVG filetype
     */
    public function kwtsk_check_svg_filetype($data, $file, $filename, $mimes) {
        if (!empty($data['ext']) && $data['ext'] === 'svg') {
            return $data;
        }

        $filetype = wp_check_filetype($filename, $mimes);

        if ($filetype['ext'] === 'svg') {
            $data['type'] = 'image/svg+xml';
            $data['ext'] = 'svg';
        }

        return $data;
    }

    /**
     * Fix SVG size attributes
     */
    public function kwtsk_fix_svg_size_attributes($image, $attachment_id, $size, $icon) {
        if ($image && preg_match('/\.svg$/i', $image[0])) {
            $image[1] = isset($image[1]) && $image[1] ? $image[1] : 100;
            $image[2] = isset($image[2]) && $image[2] ? $image[2] : 100;
        }
        return $image;
    }

    /**
     * Fix SVG metadata
     */
    public function kwtsk_fix_svg_metadata($data, $attachment_id) {
        $attachment = get_post($attachment_id);
        if ($attachment && preg_match('/\.svg$/i', $attachment->guid)) {
            if (!$data) {
                $data = array(
                    'width' => 100,
                    'height' => 100,
                    'file' => get_post_meta($attachment_id, '_wp_attached_file', true),
                );
            }
        }
        return $data;
    }

    /**
     * Add CSS for SVG display
     */
    public function kwtsk_add_svg_css() {
        echo '<style>
            .attachment img[src$=".svg"] {
                width: 100% !important;
                height: auto !important;
            }
            .thumbnail img[src$=".svg"] {
                width: 100% !important;
                height: auto !important;
            }
            .wp-list-table .media-icon img[src$=".svg"] {
                width: 100% !important;
                height: auto !important;
            }
        </style>';
    }

    /**
     * Sanitize SVG
     */
    public function kwtsk_sanitize_svg($file) {
        if ($file['type'] === 'image/svg+xml') {
            if (!$this->check_svg_contents($file['tmp_name'])) {
                $file['error'] = __('Sorry, this file could not be uploaded for security reasons.', 'theme-site-kit');
            }
        }
        return $file;
    }

    /**
     * Check SVG contents for security
     */
    private function check_svg_contents($file_path) {
        $content = file_get_contents($file_path);
        
        // Basic security checks
        if (stripos($content, '<?php') !== false) {
            return false;
        }
        
        if (stripos($content, '<script') !== false) {
            return false;
        }
        
        // Check for suspicious attributes
        $suspicious = array('onload', 'onclick', 'onmouseover', 'onerror');
        foreach ($suspicious as $attr) {
            if (stripos($content, $attr) !== false) {
                return false;
            }
        }
        
        return true;
    }
}
new KWTSK_SVG_Handler();
