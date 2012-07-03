<?php
require_once LIB_PATH.'recaptchalib.php';

// Get a key from https://www.google.com/recaptcha/admin/create
$publickey = "6LfQlNMSAAAAAOHrxJwHy7ucizE1SMHGbzSozvTh";

# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;

echo recaptcha_get_html($publickey, $error);
?>