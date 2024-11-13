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

<div class="wsl-register-form" style=" display: none;">
  <h1 class="wsl-title">Create and account</h1>
  <form class="wsl-form" action="#">

    <?php if (get_option('wmf_values2')['show_social_newtworks']): ?>
      <div class="wsl-group">
        <div>
          <a href="#" id="wsl-reg-google" class="social-network-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="social-network-icon"
              viewBox="0 0 16 16">
              <path
                d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z" />
            </svg>
            Google
          </a>
        </div>
        <div>
          <a href="#" id="wsl-reg-facebook" class="social-network-button">
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

    <div class="wsl-group">
      <div>
        <label for="wsl-first-name"><?php esc_html_e('First name', 'woocommerce'); ?></label>
        <input type="text" name="wsl-first-name" id="wsl-first-name" placeholder="Fisrt Name" />
      </div>
      <div>
        <label for="wsl-last-name"><?php esc_html_e('Last name', 'woocommerce'); ?></label>
        <input type="text" name="wsl-last-name" id="wsl-last-name" placeholder="Last Name" />
      </div>
    </div>

    <div class="wsl-email">
      <label for="wsl-reg-email"><?php esc_html_e('Email address', 'woocommerce'); ?></label>
      <input type="email" name="wsl-reg-email" id="wsl-reg-email" placeholder="name@company.com" />
    </div>

    <div class="wsl-group wsl-password">
      <div>
        <label for="wsl-reg-password"><?php esc_html_e('Password', 'woocommerce'); ?></label>
        <input type="password" name="wsl-reg-password" id="wsl-reg-password" placeholder="••••••••" />
      </div>
      <div>
        <label for="wsl-reg-confirm-password"><?php esc_html_e('Re-enter new password', 'woocommerce'); ?></label>
        <input type="password" name="wsl-reg-confirm-password" id="wsl-reg-confirm-password" placeholder="••••••••" />
      </div>
    </div>

    <?php if (get_option('wmf_values2')['show_birthdate']): ?>
      <div>
        <label>Birth Date</label>
        <div class="wsl-birthdate">
          <select id="wsl-birthdate-day" name="wsl-birthdate-day">
            <option>Day</option>
            <?php for ($day = 1; $day <= 31; $day++): ?>
              <option><?php echo sprintf("%02d", $day); ?></option>
            <?php endfor; ?>
          </select>

          <select id="wsl-birthdate-month" name="wsl-birthdate-month">
            <option>Month</option>
            <?php foreach (get_option('wmf_values2')['months'] as $month): ?>
              <option><?php echo $month; ?></option>
            <?php endforeach; ?>
          </select>

          <select id="wsl-birthdate-year" name="wsl-birthdate-year">
            <option>Year</option>
            <?php for ($year = date('Y'); $year >= 1905; $year--): ?>
              <option><?php echo $year; ?></option>
            <?php endfor; ?>
          </select>
        </div>
      </div>
    <?php endif; ?>

    <?php if (get_option('wmf_values2')['show_cellphone'] || get_option('wmf_values2')['show_gender']): ?>
      <div class="wsl-group">

        <?php if (get_option('wmf_values2')['show_gender']): ?>
          <div>
            <label for="wsl-gender">Gender</label>
            <select id="wsl-gender" name="wsl-gender">
              <option>Select your gender</option>
              <option>Masculino</option>
              <option>Feminino</option>
            </select>
          </div>
        <?php endif; ?>

        <?php if (get_option('wmf_values2')['show_cellphone']): ?>
          <div>
            <label for="wsl-cellphone">Cellphone</label>
            <input type="number" name="wsl-cellphone" id="wsl-cellphone" placeholder="Cellphone" required="" />
          </div>
        <?php endif; ?>

      </div>
    <?php endif; ?>

    <div class="wsl-checkbox-container">
      <input id="wsl-checkbox-terms" type="checkbox" required="" />
      <label for="wsl-checkbox-terms">I accept the
        <a href="<?php echo esc_url(get_option('wmf_values2')['terms_conditions_url']); ?>" class="wsl-link">Terms and
          Conditions</a>
      </label>
    </div>
    <button type="submit">Create an account</button>
    <p class="already-have-account">Already have an account? <a href="#" id="wsl-login" class="wsl-link">Login here</a>
    </p>
  </form>
</div>