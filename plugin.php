<?php
/*
Plugin Name: ICC Webmaster Settings
Plugin URI: https://git.icc.gg/ivancarlos/yourlsiccwebmastersettings
Description: Customize logo, title, footer, CSS, favicons, add 2FA & reCAPTCHA, HTTP, 301/302 redirects, allow dash/underscore, force lowercase, remove share buttons.
Version: 3.0
Author: Ivan Carlos
Author URI: https://ivancarlos.com.br/
*/

// No direct call
if (!defined('YOURLS_ABSPATH'))
    die();

// Default redirect delay in seconds (used when option unset)
define('ICC_MRDR_DEFAULT_DELAY', 1);

// Load 2FA library
require_once 'authenticator.php';

// Register unified config page
yourls_add_action('plugins_loaded', 'icc_config_add_page');
function icc_config_add_page()
{
    yourls_register_plugin_page('icc_logo_title_footer_favicon_config', 'Webmaster Settings', 'icc_config_do_page');
}

// Handle and display unified config page
function icc_config_do_page()
{
    if (isset($_POST['icc_submit']))
        icc_config_update_option();

    // Options
    $icc_logo_imageurl = yourls_get_option('icc_logo_imageurl');
    $icc_logo_imageurl_tag = yourls_get_option('icc_logo_imageurl_tag');
    $icc_logo_imageurl_title = yourls_get_option('icc_logo_imageurl_title');
    $icc_title_custom = yourls_get_option('icc_title_custom');
    $icc_footer_text = yourls_get_option('icc_footer_text');
    if ($icc_footer_text === false)
        $icc_footer_text = '';
    $footer_text_escaped = htmlspecialchars($icc_footer_text);

    // Custom CSS option
    $icc_custom_css = yourls_get_option('icc_custom_css');
    if ($icc_custom_css === false)
        $icc_custom_css = '';
    $custom_css_escaped = htmlspecialchars($icc_custom_css);

    $defaults = [
        'favicon_icon32' => '',
        'favicon_icon16' => '',
        'favicon_shortcut_icon' => '',
    ];
    $favicon_options = [];
    foreach ($defaults as $key => $default_value) {
        $val = yourls_get_option($key);
        if ($val === false)
            $val = $default_value;
        $favicon_options[$key] = $val;
    }

    // reCAPTCHA options
    $icc_recaptcha_enabled = yourls_get_option('icc_recaptcha_enabled');
    $icc_recaptcha_site_key = yourls_get_option('icc_recaptcha_site_key');
    $icc_recaptcha_secret_key = yourls_get_option('icc_recaptcha_secret_key');

    $recaptcha_checked = $icc_recaptcha_enabled ? 'checked' : '';
    $escape_attr = function ($str) {
        return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5);
    };

    // Meta Redirect options
    $icc_mrdr_url_prefix = yourls_get_option('icc_mrdr_url_prefix');
    if ($icc_mrdr_url_prefix === false)
        $icc_mrdr_url_prefix = '.';

    $icc_mrdr_delay = yourls_get_option('icc_mrdr_delay');
    if ($icc_mrdr_delay === false || !is_numeric($icc_mrdr_delay) || (int) $icc_mrdr_delay < 0) {
        $icc_mrdr_delay = ICC_MRDR_DEFAULT_DELAY;
    }
    $escaped_delay = (int) $icc_mrdr_delay;

    // 302 Redirect options
    $icc_302_redirect_enabled = yourls_get_option('icc_302_redirect_enabled');
    // 302 Redirect options
    $icc_302_redirect_enabled = yourls_get_option('icc_302_redirect_enabled');
    $redirect_302_checked = $icc_302_redirect_enabled ? 'checked' : '';

    // Remove Share options
    $icc_remove_share_enabled = yourls_get_option('icc_remove_share_enabled');
    $remove_share_checked = $icc_remove_share_enabled ? 'checked' : '';

    // Allow Dash/Underscore options
    $icc_allow_dash_underscore_enabled = yourls_get_option('icc_allow_dash_underscore_enabled');
    $allow_dash_underscore_checked = $icc_allow_dash_underscore_enabled ? 'checked' : '';

    // Force Lowercase options
    $icc_force_lowercase_enabled = yourls_get_option('icc_force_lowercase_enabled');
    $force_lowercase_checked = $icc_force_lowercase_enabled ? 'checked' : '';

    // 2FA options
    $icc_2fa_tokens = json_decode(yourls_get_option('icc_2fa_tokens', '{}'), true);
    $user_2fa = isset($icc_2fa_tokens[YOURLS_USER]) ? $icc_2fa_tokens[YOURLS_USER] : ['active' => false, 'secret' => '', 'type' => ''];
    $is_2fa_active = $user_2fa['active'];

    // Handle 2FA Actions
    $twofa_message = '';
    if (isset($_POST['icc_2fa_activate'])) {
        $ga = new PHPGangsta_GoogleAuthenticator();
        $secret = $ga->createSecret();
        $icc_2fa_tokens[YOURLS_USER] = [
            'active' => false,
            'secret' => $secret,
            'type' => 'otp'
        ];
        yourls_update_option('icc_2fa_tokens', json_encode($icc_2fa_tokens));
        $user_2fa = $icc_2fa_tokens[YOURLS_USER];
    } elseif (isset($_POST['icc_2fa_verify'])) {
        $token = isset($_POST['icc_2fa_token']) ? trim($_POST['icc_2fa_token']) : '';
        $ga = new PHPGangsta_GoogleAuthenticator();
        if ($ga->verifyCode($user_2fa['secret'], $token, 2)) {
            $icc_2fa_tokens[YOURLS_USER]['active'] = true;
            yourls_update_option('icc_2fa_tokens', json_encode($icc_2fa_tokens));
            $is_2fa_active = true;
            $twofa_message = '<p style="color:green;">2FA Activated successfully!</p>';
        } else {
            $twofa_message = '<p style="color:red;">Invalid token. Please try again.</p>';
        }
    } elseif (isset($_POST['icc_2fa_deactivate'])) {
        $icc_2fa_tokens[YOURLS_USER]['active'] = false;
        $icc_2fa_tokens[YOURLS_USER]['secret'] = '';
        yourls_update_option('icc_2fa_tokens', json_encode($icc_2fa_tokens));
        $is_2fa_active = false;
        $twofa_message = '<p style="color:blue;">2FA Deactivated.</p>';
    }

    $twofa_html = '';
    if ($is_2fa_active) {
        $twofa_html = '<p>2FA is currently <strong>enabled</strong>.</p>
                       <form method="post">
                           <input type="submit" name="icc_2fa_deactivate" value="Deactivate 2FA" class="button" />
                       </form>';
    } else {
        if (isset($_POST['icc_2fa_activate']) || (isset($_POST['icc_2fa_verify']) && !$is_2fa_active)) {
            $ga = new PHPGangsta_GoogleAuthenticator();
            $qrCodeUrl = $ga->getQRCodeGoogleUrl('YOURLS (' . YOURLS_USER . ')', $user_2fa['secret']);
            $twofa_html = '<p>1. Scan this QR code with your Authenticator app (Google Authenticator, Authy, etc.):</p>
                           <p><img src="' . $qrCodeUrl . '" style="border:1px solid #ccc;" /></p>
                           <p>2. Enter the 6-digit code from the app to verify:</p>
                           <form method="post">
                               <input type="text" name="icc_2fa_token" size="6" maxlength="6" autocomplete="off" />
                               <input type="submit" name="icc_2fa_verify" value="Verify and Activate" class="button" />
                           </form>';
        } else {
            $twofa_html = '<p>2FA is currently <strong>disabled</strong>.</p>
                           <form method="post">
                               <input type="submit" name="icc_2fa_activate" value="Setup 2FA" class="button" />
                           </form>';
        }
    }

    echo <<<HTML
<h2>Webmaster Settings</h2>
<form method="post">
    <h3>Logo Settings</h3>
    <p><label for="icc_logo_imageurl" style="display: inline-block; width: 200px;">Image URL</label>
    <input type="text" id="icc_logo_imageurl" name="icc_logo_imageurl" value="{$escape_attr($icc_logo_imageurl)}" size="80" /></p>
    <p><label for="icc_logo_imageurl_tag" style="display: inline-block; width: 200px;">Image ALT tag</label>
    <input type="text" id="icc_logo_imageurl_tag" name="icc_logo_imageurl_tag" value="{$escape_attr($icc_logo_imageurl_tag)}" size="80" /></p>
    <p><label for="icc_logo_imageurl_title" style="display: inline-block; width: 200px;">Image Title</label>
    <input type="text" id="icc_logo_imageurl_title" name="icc_logo_imageurl_title" value="{$escape_attr($icc_logo_imageurl_title)}" size="80" /></p>

    <h3>Title Settings</h3>
    <p><label for="icc_title_custom" style="display: inline-block; width: 200px;">Custom Title</label>
    <input type="text" id="icc_title_custom" name="icc_title_custom" value="{$escape_attr($icc_title_custom)}" size="80" /></p>

    <h3>Footer Settings</h3>
    <p><label for="icc_footer_text" style="display: inline-block; width: 200px; vertical-align: top;">Footer Text (HTML allowed)</label>
    <textarea id="icc_footer_text" name="icc_footer_text" rows="5" cols="80" style="vertical-align: top;">{$footer_text_escaped}</textarea></p>

    <h3>Custom CSS</h3>
    <p><label for="icc_custom_css" style="display: inline-block; width: 200px; vertical-align: top;">Custom CSS<br>
	<span style="color:#666;font-size:90%;">(Enter raw CSS. It will be added inside a <code>&lt;style&gt;</code> tag.)</span></label>
    <textarea id="icc_custom_css" name="icc_custom_css" rows="5" cols="80" style="vertical-align: top;">{$custom_css_escaped}</textarea></p>

    <h3>Favicon Lines Settings</h3>
    <p><label for="favicon_icon32" style="display: inline-block; width: 200px;">Icon PNG 32x32 URL</label>
    <input type="text" id="favicon_icon32" name="favicon_icon32" value="{$escape_attr($favicon_options['favicon_icon32'])}" size="80" /></p>
    <p><label for="favicon_icon16" style="display: inline-block; width: 200px;">Icon PNG 16x16 URL</label>
    <input type="text" id="favicon_icon16" name="favicon_icon16" value="{$escape_attr($favicon_options['favicon_icon16'])}" size="80" /></p>
    <p><label for="favicon_shortcut_icon" style="display: inline-block; width: 200px;">Shortcut Icon (favicon.ico) URL</label>
    <input type="text" id="favicon_shortcut_icon" name="favicon_shortcut_icon" value="{$escape_attr($favicon_options['favicon_shortcut_icon'])}" size="80" /></p>

    <h3>reCAPTCHA v3 Settings</h3>
    <p>
        <label for="icc_recaptcha_enabled" style="display: inline-block; width: 200px;">Enable reCAPTCHA v3</label>
        <input type="checkbox" id="icc_recaptcha_enabled" name="icc_recaptcha_enabled" value="1" {$recaptcha_checked} />
    </p>
    <p><label for="icc_recaptcha_site_key" style="display: inline-block; width: 200px;">Site Key</label>
    <input type="text" id="icc_recaptcha_site_key" name="icc_recaptcha_site_key" value="{$escape_attr($icc_recaptcha_site_key)}" size="80" placeholder="Required if enabled" /></p>
    <p><label for="icc_recaptcha_secret_key" style="display: inline-block; width: 200px;">Secret Key</label>
    <input type="text" id="icc_recaptcha_secret_key" name="icc_recaptcha_secret_key" value="{$escape_attr($icc_recaptcha_secret_key)}" size="80" placeholder="Required if enabled" /></p>

    <h3>Meta Redirect Settings</h3>
    <p>
        <label for="icc_mrdr_url_prefix" style="display:inline-block; width:200px;">Redirect Prefix Character</label>
        <input type="text" id="icc_mrdr_url_prefix" name="icc_mrdr_url_prefix" value="{$escape_attr($icc_mrdr_url_prefix)}" maxlength="1" size="80" />
        <br><span style="padding-left: 205px;"><small>Single character prefix to trigger meta redirect. Default is a dot (.)</small></span>
    </p>
    <p>
        <label for="icc_mrdr_delay" style="display:inline-block; width:200px;">Redirect Delay (seconds)</label>
        <input type="number" id="icc_mrdr_delay" name="icc_mrdr_delay" value="{$escaped_delay}" min="0" step="1" size="80" />
        <br><span style="padding-left: 205px;"><small>Delay before redirecting. Default is 1 second. Use 0 for immediate redirect.</small></span>
    </p>

    <h3>Redirect Code Settings</h3>
    <p>
        <label for="icc_302_redirect_enabled" style="display: inline-block; width: 200px;">Force 302 Redirect</label>
        <input type="checkbox" id="icc_302_redirect_enabled" name="icc_302_redirect_enabled" value="1" {$redirect_302_checked} />
        <br><span style="padding-left: 205px;"><small>Use 302 (Temporary) instead of 301 (Permanent) for standard redirects.</small></span>
    </p>

    <h3>Interface Settings</h3>
    <p>
        <label for="icc_remove_share_enabled" style="display: inline-block; width: 200px;">Remove Share Button</label>
        <input type="checkbox" id="icc_remove_share_enabled" name="icc_remove_share_enabled" value="1" {$remove_share_checked} />
        <br><span style="padding-left: 205px;"><small>Remove the Share button and box from the Admin Dashboard.</small></span>
    </p>
    <p>
        <label for="icc_allow_dash_underscore_enabled" style="display: inline-block; width: 200px;">Allow Dash & Underscore</label>
        <input type="checkbox" id="icc_allow_dash_underscore_enabled" name="icc_allow_dash_underscore_enabled" value="1" {$allow_dash_underscore_checked} />
        <br><span style="padding-left: 205px;"><small>Allow dashes (-) and underscores (_) in custom short URLs.</small></span>
    </p>
    <p>
        <label for="icc_force_lowercase_enabled" style="display: inline-block; width: 200px;">Force Lowercase</label>
        <input type="checkbox" id="icc_force_lowercase_enabled" name="icc_force_lowercase_enabled" value="1" {$force_lowercase_checked} />
        <br><span style="padding-left: 205px;"><small>Force uppercase keywords to be converted to lowercase (e.g., ABC -> abc).</small></span>
    </p>

    <p><input type="submit" name="icc_submit" value="Update values" /></p>
</form>

<hr style="margin-top: 40px" />
<h3>2FA (Two-Factor Authentication)</h3>
{$twofa_message}
{$twofa_html}
<hr style="margin-top: 40px" />
<p><strong><a href="https://ivancarlos.me/" target="_blank">Ivan Carlos</a></strong>  &raquo; 
<a href="https://buymeacoffee.com/ivancarlos" target="_blank">Buy Me a Coffee</a></p>
HTML;
}

// Update options
function icc_config_update_option()
{
    $fields_logo = ['icc_logo_imageurl', 'icc_logo_imageurl_tag', 'icc_logo_imageurl_title'];
    foreach ($fields_logo as $key) {
        if (isset($_POST[$key]))
            yourls_update_option($key, strval($_POST[$key]));
    }
    if (isset($_POST['icc_title_custom']))
        yourls_update_option('icc_title_custom', strval($_POST['icc_title_custom']));
    if (isset($_POST['icc_footer_text']))
        yourls_update_option('icc_footer_text', $_POST['icc_footer_text']);
    if (isset($_POST['icc_custom_css']))
        yourls_update_option('icc_custom_css', $_POST['icc_custom_css']);
    $fields_favicon = ['favicon_icon32', 'favicon_icon16', 'favicon_shortcut_icon'];
    foreach ($fields_favicon as $key) {
        if (isset($_POST[$key]))
            yourls_update_option($key, strval($_POST[$key]));
    }

    // reCAPTCHA update
    $recaptcha_enabled = isset($_POST['icc_recaptcha_enabled']);
    $site_key = isset($_POST['icc_recaptcha_site_key']) ? trim($_POST['icc_recaptcha_site_key']) : '';
    $secret_key = isset($_POST['icc_recaptcha_secret_key']) ? trim($_POST['icc_recaptcha_secret_key']) : '';

    if ($recaptcha_enabled && (empty($site_key) || empty($secret_key))) {
        echo '<div class="error"><p><strong>Error:</strong> both Site Key and Secret Key are required to enable reCAPTCHA.</p></div>';
        // Do not update enabled status if validation fails
    } else {
        yourls_update_option('icc_recaptcha_enabled', $recaptcha_enabled);
        yourls_update_option('icc_recaptcha_site_key', $site_key);
        yourls_update_option('icc_recaptcha_secret_key', $secret_key);
    }

    // Meta Redirect update
    if (isset($_POST['icc_mrdr_url_prefix'])) {
        $prefix = substr(trim($_POST['icc_mrdr_url_prefix']), 0, 1);
        if ($prefix === '') {
            yourls_delete_option('icc_mrdr_url_prefix');
        } else {
            yourls_update_option('icc_mrdr_url_prefix', $prefix);
        }
    }
    if (isset($_POST['icc_mrdr_delay'])) {
        $delay = intval($_POST['icc_mrdr_delay']);
        if ($delay < 0)
            $delay = ICC_MRDR_DEFAULT_DELAY;
        yourls_update_option('icc_mrdr_delay', $delay);
    }

    // 302 Redirect update
    $redirect_302_enabled = isset($_POST['icc_302_redirect_enabled']);
    yourls_update_option('icc_302_redirect_enabled', $redirect_302_enabled);
    $redirect_302_enabled = isset($_POST['icc_302_redirect_enabled']);
    yourls_update_option('icc_302_redirect_enabled', $redirect_302_enabled);

    // Remove Share update
    $remove_share_enabled = isset($_POST['icc_remove_share_enabled']);
    yourls_update_option('icc_remove_share_enabled', $remove_share_enabled);

    // Allow Dash/Underscore update
    $allow_dash_underscore_enabled = isset($_POST['icc_allow_dash_underscore_enabled']);
    yourls_update_option('icc_allow_dash_underscore_enabled', $allow_dash_underscore_enabled);

    // Force Lowercase update
    $force_lowercase_enabled = isset($_POST['icc_force_lowercase_enabled']);
    yourls_update_option('icc_force_lowercase_enabled', $force_lowercase_enabled);
}

// Show custom logo
yourls_add_filter('pre_html_logo', 'icc_hideoriginallogo');
function icc_hideoriginallogo()
{
    echo '<span id="hideYourlsLogo" style="display:none">';
}
yourls_add_filter('html_logo', 'icc_logo');
function icc_logo()
{
    echo '</span>';
    echo '<h1 id="yourls.logo">';
    echo '<a href="' . yourls_admin_url('index.php') . '" title="' . yourls_get_option('icc_logo_imageurl_title') . '"><span>';
    echo '<img src="' . yourls_get_option('icc_logo_imageurl') . '" alt="' . yourls_get_option('icc_logo_imageurl_tag') . '" title="' . yourls_get_option('icc_logo_imageurl_title') . '" border="0" style="border: 0px; max-width: 100px;" /></a>';
    echo '</h1>';
}

// Show custom title
yourls_add_filter('html_title', 'icc_change_title');
function icc_change_title($value)
{
    $custom = yourls_get_option('icc_title_custom');
    if ($custom !== '')
        return $custom;
    return $value;
}

// Replace footer text with custom footer from option
yourls_add_filter('html_footer_text', 'icc_change_footer');
function icc_change_footer($value)
{
    $custom_footer = yourls_get_option('icc_footer_text');
    if (!empty($custom_footer))
        return $custom_footer;
    return $value;
}

// Output favicon lines (only if set)
yourls_add_filter('shunt_html_favicon', 'icc_plugin_favicon');
function icc_plugin_favicon()
{
    $opts = [
        'favicon_icon32' => yourls_get_option('favicon_icon32'),
        'favicon_icon16' => yourls_get_option('favicon_icon16'),
        'favicon_shortcut_icon' => yourls_get_option('favicon_shortcut_icon'),
    ];
    if (!empty($opts['favicon_icon32'])) {
        echo '<link rel="icon" type="image/png" sizes="32x32" href="' . htmlspecialchars($opts['favicon_icon32'], ENT_QUOTES | ENT_HTML5) . '">' . "\n";
    }
    if (!empty($opts['favicon_icon16'])) {
        echo '<link rel="icon" type="image/png" sizes="16x16" href="' . htmlspecialchars($opts['favicon_icon16'], ENT_QUOTES | ENT_HTML5) . '">' . "\n";
    }
    if (!empty($opts['favicon_shortcut_icon'])) {
        echo '<link rel="shortcut icon" href="' . htmlspecialchars($opts['favicon_shortcut_icon'], ENT_QUOTES | ENT_HTML5) . '">' . "\n";
    }
    return true;
}

// Output custom CSS if set
yourls_add_action('html_head', 'icc_print_custom_css');
function icc_print_custom_css()
{
    $css = yourls_get_option('icc_custom_css');
    if ($css !== false && trim($css) !== '') {
        echo "<style>\n" . $css . "\n</style>\n";
    }
}

// reCAPTCHA v3 Integration
yourls_add_action('html_head', 'icc_recaptcha_v3_html_head');

function icc_recaptcha_v3_html_head()
{
    if (!yourls_get_option('icc_recaptcha_enabled'))
        return;

    $site_key = yourls_get_option('icc_recaptcha_site_key');
    if ($site_key) {
        echo '<script src="https://www.google.com/recaptcha/api.js?render=' . htmlspecialchars($site_key, ENT_QUOTES) . '"></script>';
    }
}

yourls_add_action('login_form_bottom', 'icc_recaptcha_v3_login_form');
function icc_recaptcha_v3_login_form()
{
    if (!yourls_get_option('icc_recaptcha_enabled'))
        return;
    echo '<div id="recaptcha"></div>';
    echo '<input type="hidden" name="token" id="tokenInput">';
}

yourls_add_action('login_form_end', 'icc_recaptcha_v3_inject_script');
function icc_recaptcha_v3_inject_script()
{
    if (!yourls_get_option('icc_recaptcha_enabled'))
        return;
    $site_key = yourls_get_option('icc_recaptcha_site_key');
    if ($site_key) {
        echo '<script>
            grecaptcha.ready(function() {
                grecaptcha.execute(\'' . htmlspecialchars($site_key, ENT_QUOTES) . '\', {action: \'submit\'}).then(function(token) {
                    document.getElementById(\'tokenInput\').value = token;
                });
            });
        </script>';
    }
}

yourls_add_action('pre_login_username_password', 'icc_recaptcha_v3_validation');
function icc_recaptcha_v3_validation()
{
    if (!yourls_get_option('icc_recaptcha_enabled'))
        return;

    $site_key = yourls_get_option('icc_recaptcha_site_key');
    $secret_key = yourls_get_option('icc_recaptcha_secret_key');

    if (empty($site_key) || empty($secret_key))
        return; // Should not happen if validation works, but safety net

    $token = isset($_POST['token']) ? $_POST['token'] : '';

    // call curl to POST request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $secret_key, 'response' => $token)));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $arrResponse = json_decode($response, true);

    // verify the response
    if (isset($arrResponse["success"]) && $arrResponse["success"] == '1' && isset($arrResponse["score"]) && $arrResponse["score"] >= 0.5) {
        // reCAPTCHA succeeded
        return true;
    } else {
        // reCAPTCHA failed
        yourls_login_screen($error_msg = 'reCAPTCHA verification failed');
        yourls_die('reCAPTCHA verification failed. Please try again.');
        return false;
    }
}

// Meta Redirect Logic
yourls_add_action('loader_failed', 'icc_mrdr_redirect');
function icc_mrdr_redirect($args)
{
    // Get prefix from option or fallback default
    $prefix = yourls_get_option('icc_mrdr_url_prefix');
    if ($prefix === false || $prefix === '') {
        $prefix = '.';
    }

    // Get delay from option or fallback default
    $delay = yourls_get_option('icc_mrdr_delay');
    if ($delay === false || !is_numeric($delay) || (int) $delay < 0) {
        $delay = ICC_MRDR_DEFAULT_DELAY;
    }
    $delay = (int) $delay;

    // Escape prefix safely for regex
    $escaped_prefix = preg_quote($prefix, '!');

    // Check if requested keyword starts with prefix
    if (isset($args[0]) && preg_match('!^' . $escaped_prefix . '(.*)!', $args[0], $matches)) {
        $keyword = yourls_sanitize_keyword($matches[1]);

        // Load YOURLS core to use the URL functions if not already available (usually available in this hook context)
        // require_once(dirname(__FILE__) . '/../../../includes/load-yourls.php'); // Not typically needed inside a plugin hook

        $url = yourls_get_keyword_longurl($keyword);
        if (!$url) {
            return; // No redirect
        }

        // Output meta refresh redirect with configured delay
        echo '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="' . $delay . '; url=' . htmlspecialchars($url, ENT_QUOTES) . '"></head><body>';
        echo 'You will be redirected to <a href="' . htmlspecialchars($url, ENT_QUOTES) . '">' . htmlspecialchars($url) . '</a>.';
        echo '</body></html>';
        exit;
    }
}

// 302 Redirect Logic
yourls_add_action('pre_redirect', 'icc_force_302_redirect');
function icc_force_302_redirect($args)
{
    if (!yourls_get_option('icc_302_redirect_enabled'))
        return;

    $url = $args[0];
    $code = $args[1];
    if ($code != 302) {
        // Redirect with 302 instead
        yourls_redirect($url, 302);
        die();
    }
}

// Allow dash and underscore in custom short URLs
if (yourls_get_option('icc_allow_dash_underscore_enabled')) {
    yourls_add_filter('get_shorturl_charset', 'icc_custom_shorturl_charset');
    yourls_add_filter('get_shorturl_charset_regex', 'icc_custom_shorturl_charset_regex');
}

function icc_custom_shorturl_charset($charset)
{
    return $charset . '-_';
}

function icc_custom_shorturl_charset_regex($pattern)
{
    return $pattern . '|[-_]';
}

// Force Lowercase Logic
if (yourls_get_option('icc_force_lowercase_enabled')) {
    // Redirection: http://sho.rt/ABC first converted to http://sho.rt/abc
    yourls_add_filter('get_request', 'icc_break_the_web_lowercase');

    // Short URL creation: custom keyword 'ABC' converted to 'abc'
    yourls_add_action('add_new_link_custom_keyword', 'icc_break_the_web_add_filter');

    // Force random keywords to be lowercase
    yourls_add_filter('random_keyword', 'icc_break_the_web_lowercase');
}

function icc_break_the_web_lowercase($keyword)
{
    return strtolower($keyword);
}

function icc_break_the_web_add_filter()
{
    yourls_add_filter('get_shorturl_charset', 'icc_break_the_web_add_uppercase');
    yourls_add_filter('custom_keyword', 'icc_break_the_web_lowercase');
}

function icc_break_the_web_add_uppercase($charset)
{
    return $charset . strtoupper($charset);
}

// Remove Share Functionality
if (yourls_get_option('icc_remove_share_enabled')) {
    // Dump the Share button
    yourls_add_filter('table_add_row_action_array', 'icc_rmv_row_action_share');
    // No Share Box either
    yourls_add_filter('shunt_share_box', 'icc_shunt_share_box');
}

function icc_rmv_row_action_share($links)
{
    if (array_key_exists('share', $links))
        unset($links['share']);

    return $links;
}

function icc_shunt_share_box($shunt)
{
    return true;
}

// 2FA Support Logic

// Add 2FA input to the login form
yourls_add_action('login_form_bottom', 'icc_2fa_add_input');
function icc_2fa_add_input()
{
    echo '<p>
        <label for="icc_2fa_otp">' . yourls__('2FA Token') . '</label><br />
        <input type="text" id="icc_2fa_otp" name="icc_2fa_otp" placeholder="' . yourls__('Leave empty if not enabled') . '" size="30" class="text" autocomplete="off" />
    </p>';
}

// Attach 2FA validate function
yourls_add_filter('is_valid_user', 'icc_2fa_validate');
function icc_2fa_validate($is_valid)
{
    // If user failed to properly authenticate, return
    if (!$is_valid) {
        return false;
    }

    // If cookies are set, we are already logged in OR if this is an API request, skip 2fa
    if (isset($_COOKIE[yourls_cookie_name()]) || yourls_is_API()) {
        return $is_valid;
    }

    $icc_2fa_tokens = json_decode(yourls_get_option('icc_2fa_tokens', '{}'), true);

    if (!isset($icc_2fa_tokens[YOURLS_USER]) || !$icc_2fa_tokens[YOURLS_USER]['active']) {
        // User has not enabled 2fa
        return $is_valid;
    }

    // User has enabled 2FA
    if ($icc_2fa_tokens[YOURLS_USER]['type'] == 'otp') {
        $token = isset($_REQUEST['icc_2fa_otp']) ? trim($_REQUEST['icc_2fa_otp']) : '';
        if (empty($token)) {
            return false;
        }

        $ga = new PHPGangsta_GoogleAuthenticator();
        if ($ga->verifyCode($icc_2fa_tokens[YOURLS_USER]['secret'], $token, 2)) {
            return true;
        }
    }

    return false;
}
