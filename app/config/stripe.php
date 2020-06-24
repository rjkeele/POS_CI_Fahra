<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
|  Stripe API Configuration
| -------------------------------------------------------------------
|
| You will get the API keys from Developers panel of the Stripe account
| Login to Stripe account (https://dashboard.stripe.com/)
| and navigate to the Developers >> API keys page
|
|  stripe_api_key            string   Your Stripe API Secret key.
|  stripe_publishable_key    string   Your Stripe API Publishable key.
|  stripe_currency           string   Currency code.
*/
$config['stripe_api_key']         = 'sk_test_51GuLeAHcIt8yxuDt6uz2lkf1xiqWQeqlGrwJlXxYQX38nyDu7wdQH7EnaeIUkbz8mlONZ4O06ilGB8fw8ajwmt43003tSJM5N9';
$config['stripe_publishable_key'] = 'pk_test_51GuLeAHcIt8yxuDtOlKGwytlwinZOmVbaDddVNeRvyCt3aCGoJkv0Ksla3K6l1rSplUGri9ZzDxqjgmX1jtccVBv008LEqA7gx';
$config['stripe_currency']        = 'usd';