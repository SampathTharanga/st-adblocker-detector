<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Default plugin settings
 */
function st_adb_defaults() {
    return array(
        'enable'      => true,
        'title'       => 'Ad Blocker Detected!',
        'content'     => '<p>We have detected that you are using an ad blocker extension. Please disable it to continue viewing this website. We rely on advertisements to keep our content free.</p>',
        'btn1_show'   => true,
        'btn1_text'   => 'Refresh Page',
        'btn2_show'   => false,
        'btn2_text'   => 'Close',
        'width'       => 42,
        'hidemobile'  => false,
        'noscript'    => true,
        'header'      => false,
        'bg_color'    => '#000000',
        'bg_opacity'  => 80,
        'blur_bg'     => true,
    );
}

/**
 * Save default settings to DB
 */
function st_adb_set_defaults() {
    update_option( 'st_adb_settings', json_encode( st_adb_defaults() ) );
}

/**
 * Retrieve current settings merged with defaults
 */
function st_adb_get_settings() {
    $saved    = get_option( 'st_adb_settings' );
    $saved    = $saved ? (array) json_decode( $saved, true ) : array();
    $defaults = st_adb_defaults();
    return (object) wp_parse_args( $saved, $defaults );
}

/**
 * Generate a stable obfuscated class/id name based on a seed + site key.
 * This makes CSS class names harder for ad-blockers to target.
 */
function st_adb_rclass( $key ) {
    static $map = null;
    if ( $map === null ) {
        $seed = substr( md5( get_option( 'siteurl' ) . 'st_adb_salt_v1' ), 0, 8 );
        $map  = array(
            'modal'            => 'st_' . $seed . '_wrap',
            'overlay'          => 'st_' . $seed . '_ov',
            'content'          => 'st_' . $seed . '_box',
            'body'             => 'st_' . $seed . '_bd',
            'theme'            => 'st_' . $seed . '_thm',
            'wrapper'          => 'st_' . $seed . '_wpr',
            'icon'             => 'st_' . $seed . '_ico',
            'action'           => 'st_' . $seed . '_act',
            'action-btn-ok'    => 'st_' . $seed . '_bok',
            'action-btn-close' => 'st_' . $seed . '_bcl',
            'show'             => 'st_' . $seed . '_on',
            'active'           => 'st_' . $seed . '_active',
            'fadeInDown'       => 'st_' . $seed . '_fid',
            'fake_ad'          => 'st_' . $seed . '_fad',
            'branding'         => 'st_' . $seed . '_brand',
        );
    }
    return isset( $map[ $key ] ) ? $map[ $key ] : 'st_' . $key;
}
