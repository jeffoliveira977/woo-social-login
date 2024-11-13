<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
} ?>
<style>
.wsl-tab-container {
    display: flex;
    border-bottom: 1px solid #ccc;
}

.wsl-tab {
    text-decoration: none;
    padding: 10px 20px;
    cursor: pointer;
    color: #888;
    display: flex;
    align-items: center;
    font-weight: 600;
    font-size: 0.875rem;
    line-height: 1.25rem;
}

.wsl-tab:hover {
    border-bottom: 2px solid #D1D5DB;
    color: #4B5563;
}

.wsl-tab-icon {

    width: 1.1rem;
    height: 1.1rem;
    color: #9CA3AF;
}

.wsl-tab:hover .wsl-tab-icon {
    color: #4B5563
}

.wsl-tab.active {
    color: #007bff;
    border-bottom: 2px solid #007bff;
}

.wsl-tab-space {
    margin: 0;
}

.wsl-tab.active .wsl-tab-icon.active {
    color: #007bff;
}

.wsl-tab-content {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    padding: 20px;
    border-radius: 0 5px 5px 5px;
    display: none;
    font-family: "Roboto", sans-serif;
    width: 100%;
    max-width: 1100px;
}

.wsl-tab-content.active {
    display: block;
}

.container-table {
    background-color: #fff;
}

.container-table h4 {
    color: #222;

}

.container-table p {
    margin-top: -15px;
    font-size: 13px;
    font-weight: 400;
    color: #555;
}

.tabela-customizada {
    width: 100%;

    border-collapse: collapse;
    font-family: Arial, sans-serif;
}

.tabela-customizada th,
.tabela-customizada td {
    text-align: left;
    background-color: #f2f2f2;

}

.my-settings-field {
    margin-bottom: 10px;
    /* Adicione o espaçamento vertical desejado aqui */
}

.tabela-customizada th {
    background-color: #f2f2f2;

}

.input-group-text {
    padding: 6px 6px;
    line-height: 2;
    font-size: 0.8rem;
    font-weight: 400;
    color: #565973;
    background-color: #fff;
    border: 1px solid #d4d7e5;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
}

.tabela-customizada input[type="text"],
.tabela-customizada input[type="number"],
.tabela-customizada select {
    width: 100%;
    padding: 6px 6px;
    border: 1px solid #ccc;
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
    border-top-left-radius: 0px;
    border-bottom-left-radius: 0px;
    box-sizing: border-box;
    font-family: Arial, sans-serif;


    box-sizing: border-box;
}

.tabela-customizada input[type="text"]:focus,
.tabela-customizada input[type="number"]:focus,
.tabela-customizada select:focus {
    outline: none;
    border-color: #2196F3;
    box-shadow: 0 0 5px #2196F3;
}

.tabela-customizada input[type="text"]:hover,
.tabela-customizada input[type="number"]:hover,
.tabela-customizada select:hover {
    border-color: #ccc;
}

.tabela-customizada input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-family: Arial, sans-serif;
}

.tabela-customizada input[type="submit"]:hover {
    background-color: #45a049;
}

.switch-toggle {
    display: inline-block;
    position: relative;
    width: 44px;
    height: 24px;
}

.switch-toggle input[type="checkbox"] {
    opacity: 0;
    width: 0;
    height: 0;
}

.switch-toggle .switch-label {
    display: block;
    width: 100%;
    height: 100%;
    background: #ccc;
    border-radius: 12px;
    cursor: pointer;
    position: absolute;
    top: 0;
    left: 0;
    transition: background 0.3s ease;
}

.switch-toggle .switch-label::after {
    content: "";
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    background: #fff;
    border-radius: 50%;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    transition: left 0.3s ease;
}

.switch-toggle input[type="checkbox"]:checked+.switch-label {
    background: #2ecc71;
}

.switch-toggle input[type="checkbox"]:checked+.switch-label::after {
    left: calc(100% - 22px);
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const tabs = Array.from(document.getElementsByClassName("wsl-tab"));

    tabs.forEach(function(tab, index) {
        tab.addEventListener("click", function(event) {
            event.preventDefault();

            tabs.forEach(function(tab) {
                tab.classList.remove("active");
            });


            tab.classList.add("active");

            const targetId = tab.getAttribute("href");
            const targetContent = document.getElementById(targetId.substring(1));
            const tabContents = Array.from(document.getElementsByClassName("wsl-tab-content"));

            tabContents.forEach(function(content) {
                content.classList.remove("active");
            });

            targetContent.classList.add("active");
        });
    });
});
</script>

<div clas="wsl-tab-panel">
    <ul class="wsl-tab-container">
        <li class="wsl-tab-space">
            <a href="#login_facebook" class="wsl-tab active">
                Facebook
            </a>
        </li>
        <li class="wsl-tab-space">
            <a href="#login_google" class="wsl-tab">
                Google
            </a>
        </li>
        <li class="wsl-tab-space">
            <a href="#login_recaptcha" class="wsl-tab">
                reCAPTCHA
            </a>
        </li>
        <li class="wsl-tab-space">
            <a href="#customize" class="wsl-tab">
                Costumize
            </a>
        </li>
    </ul>

    <form method="post" action="options.php" name="woo-social-login">
        <?php settings_fields("wsl-page"); ?>
        <?php
        include_once 'html-settings.php';
        include_once 'html-customize.php'
            ?>
        <?php submit_button(); ?>
    </form>
</div>