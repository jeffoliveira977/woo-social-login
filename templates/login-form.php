<?php

/**
 * Front Login Ajax - Main login form
 *
 * @version 1.0
 */

if (!defined('ABSPATH')) {
  exit;
}

?>

<div class="wsl-login-form">

    <h1 class="wsl-title">Sign in to your account</h1>
    <form class="wsl-form" action="#">

        <?php if (get_option('wmf_values2')['show_social_newtworks']): ?>
        <div class="wsl-group">
            <div>
                <a href="#" id="wsl-log-google" class="social-network-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="social-network-icon" viewBox="0 0 16 16">
                        <path
                            d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z" />
                    </svg>
                    Google
                </a>
            </div>
            <div>
                <a href="#" id="wsl-log-facebook" class="social-network-button">
                    <svg class="social-network-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path
                            d="M480,257.35c0-123.7-100.3-224-224-224s-224,100.3-224,224c0,111.8,81.9,204.47,189,221.29V322.12H164.11V257.35H221V208c0-56.13,33.45-87.16,84.61-87.16,24.51,0,50.15,4.38,50.15,4.38v55.13H327.5c-27.81,0-36.51,17.26-36.51,35v42h62.12l-9.92,64.77H291V478.66C398.1,461.85,480,369.18,480,257.35Z"
                            fill-rule="evenodd" />
                    </svg>
                    Facebook
                </a>
            </div>
        </div>
        <div class="social-network-line">
            <hr><span>or</span>
            <hr>
        </div>
        <?php endif; ?>

        <div class="wsl-user">
            <label for="wsl-log-user">User name or Email</label>
            <input type="text" name="wsl-log-user" id="wsl-log-user" placeholder="User name or Email" required="" />
        </div>
        <div class="wsl-password">
            <label for="wsl-log-password">Password</label>
            <input type="password" name="wsl-log-password" id="wsl-log-password" placeholder="••••••••" required="" />
        </div>
        <div class="wsl-checkbox-container" style="justify-content: space-between;">
            <div>
                <input id="wsl-rememberme" type="checkbox" required="" />
                <label for="wsl-rememberme">Remember me</label>
            </div>
            <a href="#" id="wsl-lost-password" class="wsl-link">Forgot password?</a>
        </div>
        <div class="center-recaptcha">
            <div class="g-recaptcha" data-sitekey="6LdsE5spAAAAADtccX_HtWkeNGSZZ0sSK9bp8hVt" data-size="invisible">
            </div>
        </div>

        <button type="submit">Sign in to your account</button>
        <?php if ('yes' === get_option('woocommerce_enable_myaccount_registration')): ?>
        <p class="already-have-account">Don’t have an account yet? <a href="#" id="wsl-register" class="wsl-link">Sign
                up
                here</a></p>
        <?php endif; ?>
    </form>
</div>