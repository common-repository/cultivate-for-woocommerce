<?php

defined( 'ABSPATH' ) || exit;

$authorize_url = 'admin.php?page=wc-cultivate-authorize';

$non_default_permalinks_enabled = ! ! get_option( 'permalink_structure' );

$authorized = get_user_meta( get_current_user_id(), WC_Cultivate::CULTIVATE_AUTHORIZED_META_KEY, true );

$current_userdata = get_userdata( get_current_user_id() );

?>

<style>
  .woocommerce-message, #mainform p.submit {
    /* hide WC notices and default settings chrome on our options page */
    display: none;
  }

  .expand-heading {
    display: inline-block;
    margin-bottom: 0;
    cursor: pointer;
  }

  .expand-heading::before {
    content: "\25B6";
    font-size: 82.5%;
    margin-right: 4px;
  }

  .expand-heading.active::before {
    content: "\25BC";
  }

  .card {
    max-width: unset;
  }

  .required-marker {
    vertical-align: top;
    margin-left: 6px;
  }

  .required-marker::before {
    content: '*';
    font-size: 16px;
    color: red;
  }

  p.cultivate-error {
    margin-top: 6px;
    color: red;
  }

  .file-upload input {
    position: absolute;
    top: 0;
    right: 0;
    margin: 0;
    height: 100%;
    opacity: 0;
    filter: alpha(opacity=0);
    font-size: 200px !important;
    direction: ltr;
    cursor: pointer;
  }

  .file-upload .button {
    position: relative;
    overflow: hidden;
    display: inline-block;
    margin-bottom: 6px;
  }

  .file-upload .progress {
    margin-bottom: 6px;
  }

  .file-upload .progress-bar {
    height: 18px;
    background: green;
  }
</style>

<div class="wrap">
  <h1 class="wp-heading-inline"><?php echo esc_html( __( 'Cultivate', 'cultivate-for-woocommerce' ) ); ?></h1>
  <?php if ( empty( $authorized ) && $non_default_permalinks_enabled ) { ?>
    <a href="<?php echo $authorize_url; ?>" class="page-title-action">Sign In</a>
  <?php } ?>

  <?php if ( empty( $authorized ) ) { ?>
    <div class="card">
      <h2 class="title">Get Started</h2>
      <?php if ( $non_default_permalinks_enabled ) { ?>
        <p>
          <?php
          /* translators: %1$s replaced with link start, %2$s replaced with link end HTML tags */
          printf( esc_html__( '%1$s Sign In to Cultivate %2$s to connect your WooCommerce store.',
            'cultivate-for-woocommerce' ), "<a href='$authorize_url' data-test-id='signInLink'>", '</a>' );
          ?>
        </p>
      <?php } else { ?>
        <p>
          <?php
          echo __( 'WooCommerce for Cultivate requires that you set up pretty permalinks. Default permalinks will not work, because they are not supported by the WooCommerce REST API.',
            'cultivate-for-woocommerce' );
          ?>
        </p>
      <?php } ?>
    </div>
  <?php } else { ?>
    <div
      data-behavior="cultivateDynamicContent"
      data-api-base-url="<?php echo WC_Cultivate::config()->get_client_api_base_url(); ?>"
      data-authorize-url="<?php echo $authorize_url; ?>"
      data-site-url="<?php echo get_site_url(); ?>"
      data-plugin-version="<?php echo WP_CULTIVATE_PLUGIN_VERSION; ?>"
    >
      <p data-pending>
        <?php echo esc_html__( 'Please wait...', 'cultivate-for-woocommerce' ); ?>
      </p>

      <div data-behavior="termsContainer" style="display: none; max-width: 800px;"
           data-site-name="<?php echo get_option( 'blogname' ); ?>"
           data-timezone="<?php echo wc_cultivate_get_timezone()->getName(); ?>"
           data-test-id="termsContent">
        <div data-terms-html data-test-id="terms"></div>
        <form data-form>
          <button type="submit" class="button button-primary" data-test-id="acceptTermsButton">
            <?php echo esc_html__( 'Accept Terms', 'cultivate-for-woocommerce' ); ?>
          </button>
        </form>
      </div>

      <div data-behavior="merchantTermsAcceptedContainer" style="display: none;">
        <div data-behavior="payItForwardContainer" class="card" style="display: none;"
             data-test-id="payItForwardContent">
          <h2 class="title"><?php echo esc_html__( 'Pay It Forward', 'cultivate-for-woocommerce' ); ?></h2>
          <div>
            <p data-status="ACTIVE" style="display: none;">
              <?php echo esc_html__( 'Pay It Forward is enabled for your store.', 'cultivate-for-woocommerce' ); ?>
            </p>

            <p data-status="AWAITING_APPROVAL" style="display: none;">
              <?php echo esc_html__( 'Pay It Forward for your store is awaiting approval by Cultivate.',
                'cultivate-for-woocommerce' ); ?>
            </p>

            <p data-status="INACTIVE" style="display: none;">
              <?php
              /* translators: %1$s replaced with link start, %2$s replaced with link end HTML tags */
              printf( esc_html__( 'You can enable %1$s Pay It Forward %2$s for your store to promote your store in Google search results for our extension users.',
                'cultivate-for-woocommerce' ),
                '<a target="_blank" rel="noreferrer" href="https://www.wecultivate.us/feature/pay-it-forward">',
                '</a>' );
              ?>
            </p>

            <div>
              <button data-enable class="button button-primary" disabled data-test-id="enablePayItForwardButton">
                <?php echo esc_html__( 'Enable', 'cultivate-for-woocommerce' ); ?>
              </button>
              <button data-disable class="button button-secondary" disabled>
                <?php echo esc_html__( 'Disable', 'cultivate-for-woocommerce' ); ?>
              </button>
            </div>
          </div>
        </div>

        <div
          data-behavior="settingsContainer"
          data-test-id="settingsContent"
        >
          <h2 class="expand-heading" data-behavior="expandHeading">
            <?php echo esc_html__( 'Site Settings', 'cultivate-for-woocommerce' ); ?>
          </h2>

          <form data-form style="display: none;">
            <div class="card">
              <h2 class="title">
                <?php echo esc_html__( 'Contact Details', 'cultivate-for-woocommerce' ); ?>
              </h2>
              <p>
                <?php echo esc_html__( 'Please enter the details of someone Cultivate can contact about your shop.',
                  'cultivate-for-woocommerce' ); ?>
              </p>

              <table class="form-table">
                <tbody>
                <tr>
                  <th>
                    <label for="contactName">
                      <?php echo esc_html__( 'Contact Name', 'cultivate-for-woocommerce' ); ?>
                    </label>
                  </th>
                  <td>
                    <input type="text" id="contactName" name="contactName" data-test-id="contactName"
                           class="regular-text ltr"
                           value="<?php echo $current_userdata->first_name . ' ' . $current_userdata->last_name; ?>"
                           disabled/>
                    <span class="required-marker"></span>
                  </td>
                </tr>
                <tr>
                  <th>
                    <label for="organizationName">
                      <?php echo esc_html__( 'Organization Name', 'cultivate-for-woocommerce' ); ?>
                    </label>
                  </th>
                  <td>
                    <input type="text" id="organizationName" name="organizationName" data-test-id="organizationName"
                           class="regular-text ltr"
                           value="<?php echo get_option( 'blogname' ); ?>" disabled/>
                    <span class="required-marker"></span>
                  </td>
                </tr>
                <tr>
                  <th>
                    <label for="telephone">
                      <?php echo esc_html__( 'Telephone', 'cultivate-for-woocommerce' ); ?>
                    </label>
                  </th>
                  <td>
                    <input type="text" id="telephone" name="telephone" data-test-id="telephone" class="regular-text ltr"
                           disabled/>
                    <span class="required-marker"></span>
                  </td>
                </tr>
                <tr>
                  <th>
                    <label for="email">
                      <?php echo esc_html__( 'Email', 'cultivate-for-woocommerce' ); ?>
                    </label>
                  </th>
                  <td>
                    <input type="text" id="email" name="email" data-test-id="email" class="regular-text ltr" disabled/>
                    <span class="required-marker"></span>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>

            <div class="card">
              <h2 class="title">
                <?php echo esc_html__( 'Site Details', 'cultivate-for-woocommerce' ); ?>
              </h2>
              <p>
                <?php echo esc_html__( 'Customize your site listing in Cultivate.', 'cultivate-for-woocommerce' ); ?>
                <br>
                <br>
              </p>

              <h4>
                <?php echo esc_html__( 'Is your business:', 'cultivate-for-woocommerce' ); ?>
              </h4>
              <table class="form-table">
                <tbody>
                <tr>
                  <th>
                    <label for="isWomanOwnedOrOperated">
                      <?php echo esc_html__( 'Woman-owned or operated?', 'cultivate-for-woocommerce' ); ?>
                    </label>
                  </th>
                  <td>
                    <input type="checkbox" id="isWomanOwnedOrOperated" name="isWomanOwnedOrOperated"
                           data-test-id="isWomanOwnedOrOperated" disabled/>
                    <p class="description">
                      <?php echo __( 'We use this to highlight your products in our results as being from a business wholly woman-owned or operated',
                        'cultivate-for-woocommerce' ); ?>
                    </p>
                  </td>
                </tr>
                <tr>
                  <th>
                    <label for="isBipocOwnedOrOperated">
                      <?php echo esc_html__( 'BIPOC-owned or operated?', 'cultivate-for-woocommerce' ); ?>
                    </label>
                  </th>
                  <td>
                    <input type="checkbox" id="isBipocOwnedOrOperated" name="isBipocOwnedOrOperated"
                           data-test-id="isBipocOwnedOrOperated" disabled/>
                    <p class="description">
                      <?php echo __( 'We use this to highlight your products in our results as being from a business wholly BIPOC-owned or operated',
                        'cultivate-for-woocommerce' ); ?>
                    </p>
                  </td>
                </tr>
                </tbody>
              </table>

              <hr/>

              <table class="form-table">
                <tbody>
                <tr>
                  <th>
                    <label for="hqState">
                      <?php echo esc_html__( 'Headquarters Location', 'cultivate-for-woocommerce' ); ?>
                    </label>
                  </th>
                  <td>
                    <select id="hqState" name="hqState" data-test-id="hqState" disabled>
                      <option value="">
                        <?php echo esc_html__( '-- Please select --', 'cultivate-for-woocommerce' ); ?>
                      </option>
                      <?php foreach ( wc_cultivate_get_state_codes() as $code ) {
                        echo "<option value='$code'>$code</option>";
                      } ?>
                    </select>
                    <span class="required-marker"></span>
                    <p class="description">
                      <?php echo __( 'We use this to filter our results when people search within a specific state',
                        'cultivate-for-woocommerce' ); ?>
                    </p>
                  </td>
                </tr>
                <tr>
                  <th>
                    <label for="shopLogo">
                      <?php echo esc_html__( 'Shop Logo', 'cultivate-for-woocommerce' ); ?>
                    </label>
                  </th>
                  <td>
                    <div class="file-upload">
                      <span class="button button-primary">
                        <span>
                          <?php echo esc_html__( 'Choose file', 'cultivate-for-woocommerce' ); ?>
                        </span>
                        <input id="shopLogo" type="file" name="file" title="" data-test-id="shopLogo" disabled>
                      </span>
                      <span class="required-marker"></span>
                      <div id="shopLogoProgress" class="progress">
                        <div class="progress-bar" style="width: 0%;"></div>
                      </div>
                      <div id="shopLogoThumbnail" data-test-id="shopLogoThumbnail"></div>
                      <p id="shopLogoError" class="cultivate-error"></p>
                    </div>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>

            <p data-behavior="generalError" class="cultivate-error"></p>

            <p class="submit">
              <button type="submit" class="button button-primary" data-test-id="settingsSaveChanges" disabled>
                <?php echo esc_html__( 'Save Changes', 'cultivate-for-woocommerce' ); ?>
              </button>
            </p>
          </form>
        </div>
      </div>
    </div>
  <?php } ?>
</div>
