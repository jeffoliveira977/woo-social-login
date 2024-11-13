<?php

/**
 * Woo Social Login - Main login form
 * 
 * @version 1.0
 */

if (!defined('ABSPATH')) {
  exit;
}
?>

<div class="wsl-lost-password-form" style=" display: none;">
    <h1 class="wsl-title">Lost Password</h1>
    <form class="wsl-form" action="#">
        <p>
            <?php echo apply_filters('woocommerce_lost_password_message', esc_html__('Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce')); ?>
        </p><?php // @codingStandardsIgnoreLine ?>

        <div class="wsl-user">
            <label for="wsl-lp-user"><?php esc_html_e('Username or email', 'woocommerce'); ?></label>
            <input type="text" name="wsl-lp-user" id="wsl-lp-user" placeholder="Email" required="" />
        </div>
        <button type="submit">Reset password</button>
        <div class="forgot-password-container">
            <a href="#" id="wsl-back-to-login" class="wsl-link forgot-password">Back</a>
        </div>
    </form>
</div>