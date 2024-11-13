<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
} ?>

<div id="login_facebook" class="wsl-tab-content active">
    <div class="container-table">
        <table class="tabela-customizada">
            <tr>
                <?php do_settings_fields('wsl-page', 'section_general-switch_login_facebook'); ?>
            </tr>
            <tr>
                <?php do_settings_fields('wsl-page', 'section_general-facebook_app_id'); ?>
            </tr>
            <tr>
                <?php do_settings_fields('wsl-page', 'section_general-facebok_app_secret'); ?>
            </tr>
        </table>
    </div>
</div>

<div id="login_google" class="wsl-tab-content">
    <div class="container-table">
        <table class="tabela-customizada">
            <tr>
                <?php do_settings_fields('wsl-page', 'section_general-switch_login_google'); ?>
            </tr>
            <tr>
                <?php do_settings_fields('wsl-page', 'section_general-google_app_id'); ?>
            </tr>
            <tr>
                <?php do_settings_fields('wsl-page', 'section_general-google_app_secret'); ?>
            </tr>
        </table>
    </div>
</div>


<div id="login_recaptcha" class="wsl-tab-content">
    <div class="container-table">
        <table class="tabela-customizada">
            <tr>
                <?php do_settings_fields('wsl-page', 'section_general-switch_login_recaptcha'); ?>
            </tr>
            <tr>
                <?php do_settings_fields('wsl-page', 'section_general-recaptcha_site_key'); ?>
            </tr>
            <tr>
                <?php do_settings_fields('wsl-page', 'section_general-recaptcha_secret_key'); ?>
            </tr>
        </table>
    </div>
</div>