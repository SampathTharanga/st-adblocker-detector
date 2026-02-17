<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function st_adb_settings_init() {

    // Plugin action links
    add_filter( 'plugin_action_links_' . ST_ADB_PLUGIN_NAME, function ( $links ) {
        $links[] = '<a style="color:#009688;font-weight:bold;" href="' . admin_url( 'admin.php?page=st-adblocker-detector' ) . '">Settings</a>';
        return $links;
    } );

    // Admin menu
    add_action( 'admin_menu', function () {
        add_menu_page(
            __( 'ST AdBlock', 'st-adblocker-detector' ),
            __( 'ST AdBlock', 'st-adblocker-detector' ),
            'manage_options',
            'st-adblocker-detector',
            'st_adb_render_settings_page',
            'dashicons-shield',
            25
        );
    } );

    // Admin scripts
    add_action( 'admin_enqueue_scripts', function ( $hook ) {
        if ( strpos( $hook, 'st-adblocker-detector' ) === false ) return;
        wp_enqueue_style(  'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script(
            'st-adb-admin',
            ST_ADB_URL . 'assets/js/admin.js',
            array( 'jquery', 'wp-color-picker' ),
            ST_ADB_VERSION,
            true
        );
        wp_enqueue_style(
            'st-adb-admin',
            ST_ADB_URL . 'assets/css/admin.css',
            array(),
            ST_ADB_VERSION
        );
        wp_localize_script( 'st-adb-admin', 'st_adb', array(
            'plugin_url' => ST_ADB_URL,
            'nonce'      => wp_create_nonce( 'st_adb_save_settings' ),
        ) );
    } );
}

/**
 * Render admin settings page
 */
function st_adb_render_settings_page() {
    $s = st_adb_get_settings();
    ?>
    <div class="st-adb-wrap">
        <div class="st-adb-header">
            <img src="<?php echo esc_url( ST_ADB_URL . 'assets/img/icon.png' ); ?>" alt="ST AdBlock">
            <div>
                <h1>ST AdBlocker Detector</h1>
                <span>By <strong>Sampath Tharanga</strong> &bull; v<?php echo ST_ADB_VERSION; ?></span>
            </div>
        </div>

        <div class="st-adb-body">
            <div class="st-adb-main">
                <table class="st-adb-table">
                    <thead><tr><th colspan="2">General Settings</th></tr></thead>
                    <tbody>

                        <tr>
                            <td>Enable Plugin</td>
                            <td>
                                <label class="st-toggle">
                                    <input type="checkbox" name="enable" class="st-field" <?php checked( filter_var( $s->enable, FILTER_VALIDATE_BOOLEAN ) ); ?>>
                                    <span class="st-slider"></span>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td>Popup Title</td>
                            <td><input type="text" name="title" class="st-field" value="<?php echo esc_attr( $s->title ); ?>" placeholder="Ad Blocker Detected!"></td>
                        </tr>

                        <tr>
                            <td>Message Content</td>
                            <td>
                                <?php wp_editor( $s->content, 'st_adb_content', array(
                                    'tinymce'       => array( 'toolbar1' => 'bold,italic,underline,link,unlink', 'toolbar2' => '' ),
                                    'media_buttons' => false,
                                    'textarea_rows' => 4,
                                    'textarea_name' => 'content_hidden',
                                ) ); ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Popup Width (%)</td>
                            <td><input type="number" name="width" class="st-field" value="<?php echo esc_attr( $s->width ); ?>" min="20" max="90" style="width:80px;"></td>
                        </tr>

                        <tr>
                            <td>Overlay Background Color</td>
                            <td><input type="text" name="bg_color" class="st-field st-color-picker" value="<?php echo esc_attr( $s->bg_color ); ?>"></td>
                        </tr>

                        <tr>
                            <td>Overlay Opacity (%)</td>
                            <td>
                                <input type="range" name="bg_opacity" class="st-field" value="<?php echo esc_attr( $s->bg_opacity ); ?>" min="50" max="100" oninput="this.nextElementSibling.value=this.value">
                                <output><?php echo esc_html( $s->bg_opacity ); ?></output>
                            </td>
                        </tr>

                        <tr>
                            <td>Blur Background</td>
                            <td>
                                <label class="st-toggle">
                                    <input type="checkbox" name="blur_bg" class="st-field" <?php checked( filter_var( $s->blur_bg, FILTER_VALIDATE_BOOLEAN ) ); ?>>
                                    <span class="st-slider"></span>
                                </label>
                            </td>
                        </tr>

                    </tbody>
                </table>

                <table class="st-adb-table" style="margin-top:20px;">
                    <thead><tr><th colspan="2">Button Settings</th></tr></thead>
                    <tbody>

                        <tr>
                            <td>Show Refresh Button</td>
                            <td>
                                <label class="st-toggle">
                                    <input type="checkbox" name="btn1_show" class="st-field" <?php checked( filter_var( $s->btn1_show, FILTER_VALIDATE_BOOLEAN ) ); ?>>
                                    <span class="st-slider"></span>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td>Refresh Button Text</td>
                            <td><input type="text" name="btn1_text" class="st-field" value="<?php echo esc_attr( $s->btn1_text ); ?>"></td>
                        </tr>

                        <tr>
                            <td>Show Close Button</td>
                            <td>
                                <label class="st-toggle">
                                    <input type="checkbox" name="btn2_show" class="st-field" <?php checked( filter_var( $s->btn2_show, FILTER_VALIDATE_BOOLEAN ) ); ?>>
                                    <span class="st-slider"></span>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td>Close Button Text</td>
                            <td><input type="text" name="btn2_text" class="st-field" value="<?php echo esc_attr( $s->btn2_text ); ?>"></td>
                        </tr>

                    </tbody>
                </table>

                <table class="st-adb-table" style="margin-top:20px;">
                    <thead><tr><th colspan="2">Advanced Settings</th></tr></thead>
                    <tbody>

                        <tr>
                            <td>Hide on Mobile</td>
                            <td>
                                <label class="st-toggle">
                                    <input type="checkbox" name="hidemobile" class="st-field" <?php checked( filter_var( $s->hidemobile, FILTER_VALIDATE_BOOLEAN ) ); ?>>
                                    <span class="st-slider"></span>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td>Enable NoScript Fallback</td>
                            <td>
                                <label class="st-toggle">
                                    <input type="checkbox" name="noscript" class="st-field" <?php checked( filter_var( $s->noscript, FILTER_VALIDATE_BOOLEAN ) ); ?>>
                                    <span class="st-slider"></span>
                                </label>
                                <p class="st-hint">Shows block screen for users with JavaScript disabled.</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Load Scripts in Header</td>
                            <td>
                                <label class="st-toggle">
                                    <input type="checkbox" name="header" class="st-field" <?php checked( filter_var( $s->header, FILTER_VALIDATE_BOOLEAN ) ); ?>>
                                    <span class="st-slider"></span>
                                </label>
                                <p class="st-hint">Requires theme to support <code>wp_body_open</code> hook.</p>
                            </td>
                        </tr>

                    </tbody>
                </table>

                <div class="st-adb-actions">
                    <?php wp_nonce_field( 'st_adb_save_settings', '_st_nonce' ); ?>
                    <button id="st_adb_save" class="button button-primary" type="button">
                        <span class="dashicons dashicons-saved" style="vertical-align:middle;margin-top:3px;"></span> Save Settings
                    </button>
                    <button id="st_adb_reset" class="button button-secondary" type="button">Reset Defaults</button>
                    <span id="st_adb_msg" class="st-msg"></span>
                </div>
            </div><!-- .st-adb-main -->

            <div class="st-adb-sidebar">
                <div class="st-adb-sidebar-box">
                    <h3>About Plugin</h3>
                    <p><strong>ST AdBlocker Detector</strong> detects ad blocker extensions and blocks website access with a customizable overlay until the visitor disables their ad blocker.</p>
                    <hr>
                    <p><strong>Developer:</strong> Sampath Tharanga</p>
                    <p><strong>Version:</strong> <?php echo ST_ADB_VERSION; ?></p>
                </div>
                <div class="st-adb-sidebar-box st-how">
                    <h3>How It Works</h3>
                    <ul>
                        <li>🔍 Checks Google Ads script loading</li>
                        <li>🏷️ Checks ad-related CSS classes</li>
                        <li>🚫 Blocks page if ad blocker found</li>
                        <li>✅ Uses randomized class names to evade blocklists</li>
                        <li>📵 NoScript fallback for JS-disabled browsers</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php
}
