<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function st_adb_ajax_init() {
    add_action( 'wp_ajax_st_adb_save', 'st_adb_ajax_save_handler' );
    add_action( 'wp_ajax_st_adb_reset', 'st_adb_ajax_reset_handler' );
}

function st_adb_ajax_save_handler() {
    if ( ! isset( $_POST['_st_nonce'] ) || ! wp_verify_nonce( $_POST['_st_nonce'], 'st_adb_save_settings' ) || ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Permission denied.' );
    }

    $allowed = array( 'enable', 'title', 'content', 'width', 'bg_color', 'bg_opacity', 'blur_bg',
                      'btn1_show', 'btn1_text', 'btn2_show', 'btn2_text',
                      'hidemobile', 'noscript', 'header' );

    $new = array();
    foreach ( $allowed as $key ) {
        if ( ! isset( $_POST[ $key ] ) ) continue;
        $val = $_POST[ $key ];
        switch ( $key ) {
            case 'content':
                $new[ $key ] = wp_kses_post( $val );
                break;
            case 'enable': case 'btn1_show': case 'btn2_show':
            case 'blur_bg': case 'hidemobile': case 'noscript': case 'header':
                $new[ $key ] = rest_sanitize_boolean( $val );
                break;
            case 'width': case 'bg_opacity':
                $new[ $key ] = intval( $val );
                break;
            case 'bg_color':
                $new[ $key ] = sanitize_hex_color( $val );
                break;
            default:
                $new[ $key ] = sanitize_text_field( $val );
        }
    }

    // Checkboxes not present in POST = false
    foreach ( array( 'enable', 'btn1_show', 'btn2_show', 'blur_bg', 'hidemobile', 'noscript', 'header' ) as $cb ) {
        if ( ! isset( $_POST[ $cb ] ) ) {
            $new[ $cb ] = false;
        }
    }

    update_option( 'st_adb_settings', json_encode( $new ) );
    wp_send_json_success( 'Settings saved successfully!' );
}

function st_adb_ajax_reset_handler() {
    if ( ! isset( $_POST['_st_nonce'] ) || ! wp_verify_nonce( $_POST['_st_nonce'], 'st_adb_save_settings' ) || ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Permission denied.' );
    }
    st_adb_set_defaults();
    wp_send_json_success( 'Settings reset to defaults!' );
}
