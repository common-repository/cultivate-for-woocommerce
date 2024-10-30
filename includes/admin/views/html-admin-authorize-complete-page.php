<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="wrap">
  <h1 class="wp-heading-inline"><?php echo esc_html( __( 'Cultivate', 'cultivate-for-woocommerce' ) ); ?></h1>
  <div>
    <p><?php echo esc_html( __( 'Please wait...', 'cultivate-for-woocommerce' ) ); ?></p>
  </div>
</div>

<script type="text/plain"
        id="cultivateApiTokenParameterName"><?php echo WC_Cultivate::CULTIVATE_API_TOKEN_PARAMETER_NAME; ?></script>
<script type="text/plain"
        id="cultivateOptionsPageUrl"><?php echo admin_url( 'admin.php?page=wc-settings&tab=wc_cultivate' ); ?></script>

<script type="text/javascript">
  var apiTokenParameterName = document.getElementById('cultivateApiTokenParameterName').innerText
  var optionsPageUrl = document.getElementById('cultivateOptionsPageUrl').innerText

  var urlParts = document.location.href.split(/\?/g)
  var queryParts = urlParts.length > 1 ? urlParts[1].split(/&/g) : []
  var token
  for (var i = 0; i < (queryParts || []).length; i++) {
    var paramParts = queryParts[i].split(/=/g)
    if (paramParts[0] === apiTokenParameterName) {
      token = paramParts[1]
      break
    }
  }

  if (token && window.localStorage) {
    window.localStorage.setItem('cultivating', token)
    window.location = optionsPageUrl
  }
</script>
