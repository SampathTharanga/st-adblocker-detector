<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function st_adb_frontend_init() {
    $s = st_adb_get_settings();
    if ( ! filter_var( $s->enable, FILTER_VALIDATE_BOOLEAN ) ) return;

    // CSS always in head
    add_action( 'wp_head', 'st_adb_output_css', 1 );

    // HTML + JS: header (wp_body_open) or footer
    if ( filter_var( $s->header, FILTER_VALIDATE_BOOLEAN ) && has_action( 'wp_body_open' ) ) {
        add_action( 'wp_body_open', 'st_adb_output_html', 1 );
    } else {
        add_action( 'wp_footer', 'st_adb_output_html', 1 );
    }

    // NoScript
    if ( filter_var( $s->noscript, FILTER_VALIDATE_BOOLEAN ) ) {
        add_action( 'wp_footer', 'st_adb_output_noscript', 2 );
    }
}

/* ------------------------------------------------------------------
   CSS output
------------------------------------------------------------------ */
function st_adb_output_css() {
    $s = st_adb_get_settings();

    $modal   = st_adb_rclass( 'modal' );
    $content = st_adb_rclass( 'content' );
    $show    = st_adb_rclass( 'show' );
    $fid     = st_adb_rclass( 'fadeInDown' );
    $width   = intval( $s->width );
    $blur    = filter_var( $s->blur_bg, FILTER_VALIDATE_BOOLEAN );
    $mobile  = filter_var( $s->hidemobile, FILTER_VALIDATE_BOOLEAN );

    // Convert hex+opacity to rgba
    $hex = ltrim( $s->bg_color, '#' );
    $r   = hexdec( substr( $hex, 0, 2 ) );
    $g   = hexdec( substr( $hex, 2, 2 ) );
    $b   = hexdec( substr( $hex, 4, 2 ) );
    $opacity = intval( $s->bg_opacity ) / 100;
    ?>
<style id="st-adb-css">
/* ST AdBlocker Detector by Sampath Tharanga */
.<?php echo esc_attr($modal); ?>{
    display:none;
    position:fixed;
    z-index:2147483647;
    inset:0;
    width:100%;
    height:100%;
    background:rgba(<?php echo "$r,$g,$b,$opacity"; ?>);
    <?php if($blur): ?>backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);<?php endif; ?>
    justify-content:center;
    align-items:center;
    overflow:hidden;
}
body.<?php echo esc_attr(st_adb_rclass('active')); ?>{
    overflow:hidden !important;
    pointer-events:none;
}
body.<?php echo esc_attr(st_adb_rclass('active')); ?> .<?php echo esc_attr($modal); ?>{
    pointer-events:all;
}
.<?php echo esc_attr($modal); ?>.<?php echo esc_attr($show); ?>{
    display:flex;
}
.<?php echo esc_attr($content); ?>{
    background:#fff;
    border-radius:12px;
    padding:35px 30px 25px;
    width:<?php echo $width; ?>%;
    max-width:600px;
    text-align:center;
    position:relative;
    box-shadow:0 20px 60px rgba(0,0,0,.4);
}
@media(max-width:900px){.<?php echo esc_attr($content); ?>{width:<?php echo min($width+15,90); ?>%;}}
@media(max-width:600px){.<?php echo esc_attr($content); ?>{width:92%;padding:25px 18px 20px;}}
.<?php echo esc_attr($content); ?> img.st-adb-icon{
    width:90px;display:block;margin:0 auto 15px;
}
.<?php echo esc_attr($content); ?> .st-adb-title{
    margin:0 0 12px;font-size:22px;font-weight:700;color:#1a1a2e;
}
.<?php echo esc_attr($content); ?> .st-adb-msg{
    color:#555;font-size:15px;line-height:1.6;margin-bottom:22px;
}
.<?php echo esc_attr($content); ?> .st-adb-msg p{margin:0;}
.st-adb-btn-row{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:5px;}
.st-adb-btn-ok{
    background:linear-gradient(135deg,#667eea,#764ba2);
    color:#fff!important;border:none;border-radius:6px;
    padding:10px 28px;font-size:14px;font-weight:600;cursor:pointer;
    text-decoration:none;display:inline-block;
    transition:opacity .2s;
}
.st-adb-btn-ok:hover{opacity:.88;}
.st-adb-btn-close{
    background:#f0f0f0;color:#555!important;border:none;border-radius:6px;
    padding:10px 22px;font-size:14px;font-weight:500;cursor:pointer;
    text-decoration:none;display:inline-block;transition:background .2s;
}
.st-adb-btn-close:hover{background:#e0e0e0;}
.st-adb-footer{margin-top:18px;font-size:11px;color:#aaa;}
/* Fake decoy ad element (hidden - used for detection) */
#<?php echo esc_attr(st_adb_rclass('fake_ad')); ?>{
    position:absolute;width:1px;height:1px;left:-9999px;top:-9999px;
    z-index:-1;
}
/* Animate */
@keyframes st-fade-down{0%{opacity:0;transform:translateY(-20px)}100%{opacity:1;transform:translateY(0)}}
.<?php echo esc_attr($fid); ?>{animation:st-fade-down .35s ease both;}
<?php if($mobile): ?>
@media(max-width:768px){.<?php echo esc_attr($modal); ?>{display:none!important;}}
<?php endif; ?>
</style>
    <?php
}

/* ------------------------------------------------------------------
   HTML overlay + detection JS
------------------------------------------------------------------ */
function st_adb_output_html() {
    $s       = st_adb_get_settings();
    $modal   = st_adb_rclass( 'modal' );
    $content = st_adb_rclass( 'content' );
    $show    = st_adb_rclass( 'show' );
    $fid     = st_adb_rclass( 'fadeInDown' );
    $fake_ad = st_adb_rclass( 'fake_ad' );
    $active  = st_adb_rclass( 'active' );

    $icon_url = ST_ADB_URL . 'assets/img/icon.png';
    $icon_url = apply_filters( 'st_adb/icon_url', $icon_url );
    ?>
<!-- ST AdBlocker Detector by Sampath Tharanga -->
<div id="<?php echo esc_attr($modal); ?>" class="<?php echo esc_attr($modal); ?>" role="dialog" aria-modal="true" aria-label="Ad Blocker Detected">
    <div class="<?php echo esc_attr($content); ?> <?php echo esc_attr($fid); ?>">
        <img src="<?php echo esc_url($icon_url); ?>" alt="Ad Blocker Detected" class="st-adb-icon">
        <h2 class="st-adb-title"><?php echo esc_html( $s->title ); ?></h2>
        <div class="st-adb-msg"><?php echo wp_kses_post( $s->content ); ?></div>
        <div class="st-adb-btn-row">
            <?php if ( filter_var( $s->btn2_show, FILTER_VALIDATE_BOOLEAN ) ) : ?>
                <a id="st-adb-close-btn" class="st-adb-btn-close" href="#" role="button"><?php echo esc_html( $s->btn2_text ); ?></a>
            <?php endif; ?>
            <?php if ( filter_var( $s->btn1_show, FILTER_VALIDATE_BOOLEAN ) ) : ?>
                <a class="st-adb-btn-ok" href="#" role="button" onclick="window.location.reload(); return false;"><?php echo esc_html( $s->btn1_text ); ?></a>
            <?php endif; ?>
        </div>
        <p class="st-adb-footer">Please disable your ad blocker and refresh the page to continue.</p>
    </div>
</div>

<!-- Decoy ad element for detection -->
<div id="<?php echo esc_attr($fake_ad); ?>" class="adsbygoogle Ad-Container ad-slot ads adsbox BannerAd ad-placement" data-ad-module="1">
    <div style="height:0;width:1px;visibility:hidden;"></div>
</div>

<script>
(function(){
    'use strict';

    var MODAL_ID  = '<?php echo esc_js($modal); ?>';
    var SHOW_CLS  = '<?php echo esc_js($show); ?>';
    var ACTIVE_CLS= '<?php echo esc_js($active); ?>';
    var FAKE_ID   = '<?php echo esc_js($fake_ad); ?>';
    var displayed = false;

    function addClass(el, cls){ if(el && !el.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'))) el.className += ' '+cls; }
    function removeClass(el, cls){ if(el){ var r=new RegExp('(\\s|^)'+cls+'(\\s|$)'); el.className=el.className.replace(r,' ').trim(); } }

    function showBlock(){
        if(displayed) return;
        displayed = true;
        var modal = document.getElementById(MODAL_ID);
        if(modal){
            addClass(modal, SHOW_CLS);
            addClass(document.body, ACTIVE_CLS);
        }
    }

    function hideBlock(){
        var modal = document.getElementById(MODAL_ID);
        if(modal){
            removeClass(modal, SHOW_CLS);
            removeClass(document.body, ACTIVE_CLS);
        }
        displayed = false;
    }

    // Close button
    document.addEventListener('DOMContentLoaded', function(){
        var closeBtn = document.getElementById('st-adb-close-btn');
        if(closeBtn){
            closeBtn.addEventListener('click', function(e){
                e.preventDefault();
                hideBlock();
            });
        }
    });

    /* -------------------------------------------------------
       Detection method 1: CSS class-based (fake ad element)
    ------------------------------------------------------- */
    function detectByClass(callback){
        var el = document.getElementById(FAKE_ID);
        if(!el){ callback(false); return; }
        var blocked = !el || el.offsetHeight === 0 || el.offsetParent === null ||
                      window.getComputedStyle(el).display === 'none' ||
                      window.getComputedStyle(el).visibility === 'hidden';
        callback(blocked);
    }

    /* -------------------------------------------------------
       Detection method 2: Google Ads script probe
    ------------------------------------------------------- */
    function detectByScript(callback){
        if(!navigator.onLine){ callback(false); return; }
        var done   = false;
        var called = false;
        var reqURL = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js';

        // XMLHttpRequest probe
        try {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', reqURL, true);
            xhr.timeout = 3000;
            xhr.onreadystatechange = function(){
                if(called) return;
                if(this.responseText &&
                   (this.responseText.toLowerCase().indexOf('ublock') > -1 ||
                    this.responseText.toLowerCase().indexOf('height:1px') > -1)){
                    called = true; callback(true); return;
                }
                if(this.readyState === 4){
                    called = true;
                    callback(this.status === 0 || this.responseURL !== reqURL);
                }
            };
            xhr.onerror = function(){ if(!called){ called=true; callback(true); } };
            xhr.ontimeout= function(){ if(!called){ called=true; callback(true); } };
            xhr.send();
        } catch(e){ callback(true); }

        // Script tag probe (backup)
        var script = document.createElement('script');
        script.src = reqURL;
        script.async = true;
        script.onload = function(){
            if(!done){
                done = true;
                if(typeof window.adsbygoogle === 'undefined'){
                    if(!called){ called=true; callback(true); }
                }
            }
            script.parentNode && script.parentNode.removeChild(script);
        };
        script.onerror = function(){
            if(!done){ done=true; if(!called){ called=true; callback(true); } }
            script.parentNode && script.parentNode.removeChild(script);
        };
        (document.head || document.body).appendChild(script);
    }

    /* -------------------------------------------------------
       Detection method 3: FairAdBlock / stndz check
    ------------------------------------------------------- */
    function detectFairAdblock(){
        return document.getElementById('stndz-style') !== null;
    }

    /* -------------------------------------------------------
       Run all detection checks
    ------------------------------------------------------- */
    function runDetection(){
        if(detectFairAdblock()){
            showBlock();
            return;
        }
        detectByScript(function(byScript){
            if(byScript){
                showBlock();
            } else {
                detectByClass(function(byClass){
                    if(byClass) showBlock();
                });
            }
        });
    }

    // Run on DOMContentLoaded
    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', runDetection);
    } else {
        runDetection();
    }
})();
</script>
    <?php
}

/* ------------------------------------------------------------------
   NoScript fallback
------------------------------------------------------------------ */
function st_adb_output_noscript() {
    $s       = st_adb_get_settings();
    $content = st_adb_rclass( 'content' );
    $fid     = st_adb_rclass( 'fadeInDown' );
    $show    = st_adb_rclass( 'show' );
    $modal   = st_adb_rclass( 'modal' );
    $icon_url = ST_ADB_URL . 'assets/img/icon.png';
    ?>
<noscript>
<div class="<?php echo esc_attr($modal); ?> <?php echo esc_attr($show); ?>" role="dialog">
    <div class="<?php echo esc_attr($content); ?> <?php echo esc_attr($fid); ?>">
        <img src="<?php echo esc_url($icon_url); ?>" alt="Blocked" class="st-adb-icon">
        <h2 class="st-adb-title"><?php echo esc_html( $s->title ); ?></h2>
        <div class="st-adb-msg"><?php echo wp_kses_post( $s->content ); ?></div>
        <div class="st-adb-btn-row">
            <?php if ( filter_var( $s->btn1_show, FILTER_VALIDATE_BOOLEAN ) ) : ?>
                <a class="st-adb-btn-ok" href="<?php echo esc_url( home_url( add_query_arg( array() ) ) ); ?>"><?php echo esc_html( $s->btn1_text ); ?></a>
            <?php endif; ?>
        </div>
        <p class="st-adb-footer">JavaScript must be enabled to view this site.</p>
    </div>
</div>
</noscript>
    <?php
}
