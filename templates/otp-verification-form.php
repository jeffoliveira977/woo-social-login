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

<div class="wsl-container">
    <div class="wsl-align">
        <div class="wsl-otp-verification-form">
            <h1 class="wsl-title">Verification Code</h1>
            <form class="wsl-form" action="#">
                <div class="wsl-otp-user">
                    <p for="wsl-otp-code">Please enter the verification code sent to your email
                        <?php echo isset($user_email) ? $user_email : ''; ?></p>
                </div>
                <div class="wsl-otp-grid">
                    <input type="text" class="wsl-otp-input" maxLength="1" size="1" min="0" max="9"
                        pattern="[0-9]{1}" />
                    <input type="text" class="wsl-otp-input" maxLength="1" size="1" min="0" max="9"
                        pattern="[0-9]{1}" />
                    <input type="text" class="wsl-otp-input" maxLength="1" size="1" min="0" max="9"
                        pattern="[0-9]{1}" />
                    <input type="text" class="wsl-otp-input" maxLength="1" size="1" min="0" max="9"
                        pattern="[0-9]{1}" />
                    <input type="text" class="wsl-otp-input" maxLength="1" size="1" min="0" max="9"
                        pattern="[0-9]{1}" />
                    <input type="text" class="wsl-otp-input" maxLength="1" size="1" min="0" max="9"
                        pattern="[0-9]{1}" />
                </div>
                <button type="submit">Verify</button>
                <div class="wsl-otp-user">
                    <p for="wsl-otp-code">Didn't receive the code?<a href="#" id="wsl-otp-resend"
                            class="wsl-link">Resend</a></p>
                </div>
            </form>
        </div>
    </div>
</div>