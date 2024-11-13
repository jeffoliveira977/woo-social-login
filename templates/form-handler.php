<?php

/**
 * Front Login Ajax - Main login form
 * 
 * @version 1.0
 */

if (!defined('ABSPATH')) {
  exit;
}

$show_social_newtworks = true;
?>
<div class="wsl-panel">
    <div class="wsl-container">
        <div class="wsl-align">
            <?php include 'login-form.php'; ?>
            <?php include 'register-form.php'; ?>
            <?php include 'lost-password-form.php'; ?>
        </div>
    </div>
</div>