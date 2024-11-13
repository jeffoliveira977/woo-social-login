<?php

/**
 * Front Login Ajax - Main login form
 * 
 * @version 1.0
 */

if (!defined('ABSPATH')) {
  exit;
}
$terms_conditions_url = "https://site-test-01.local/politica-de-privacidade";
?>

<div class="wsl-reset-password-form">
    <h1 class="wsl-title"><?php esc_attr_e('Reset password', 'woocommerce'); ?></h1>
    <form class="wsl-form" action="#">
        <div>
            <label for="wsl-rpw-email"><?php esc_html_e('Email address', 'woocommerce'); ?></label>
            <input type="email" name="wsl-rpw-email" id="wsl-rpw-email" placeholder="Email" required="" />
        </div>
        <div class="wsl-group wsl-password">
            <div>
                <label for="wsl-rpw-password"><?php esc_html_e('New password', 'woocommerce'); ?></label>
                <input type="password" name="wsl-rpw-password" id="wsl-rpw-password" placeholder="••••••••" />
            </div>
            <div>
                <label
                    for="wsl-rpw-confirm-password"><?php esc_html_e('Confirm new password', 'woocommerce'); ?></label>
                <input type="password" name="wsl-rpw-confirm-password" id="wsl-rpw-confirm-password"
                    placeholder="••••••••" />
            </div>
        </div>
        <?php if (isset($args['key']) && isset($args['login'])): ?>
        <input type="hidden" name="reset_key" value="<?php echo esc_attr($args['key']); ?>" />
        <input type="hidden" name="reset_login" value="<?php echo esc_attr($args['login']); ?>" />
        <?php endif ?>
        <div class="wsl-checkbox-container">
            <input id="wsl-rpw-terms" type="checkbox" required="" />
            <label for="wsl-rpw-terms">I accept the
                <a href="<?php echo esc_url($terms_conditions_url); ?>" class="wsl-link">Terms and Conditions</a>
            </label>
        </div>
        <button type="submit"><?php esc_html_e('Reset password', 'woocommerce'); ?></button>
    </form>
</div>