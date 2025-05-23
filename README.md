## DEMO OF CREATE CONTACT SYNCHRONIZATION USING GROUNDHOGG AND FORTELLIS

In this demo, you'll be able to see how contacts are created

https://github.com/guevarawebgraphics/fortellis-groundhogg-api-integration/assets/42199746/b11dbae3-2512-4cd1-9940-b786ef452641




## Laravel Setup

Execute these commands

```
composer install
php artisan migrate
php artisan queue:work
```

This is the file path where api is located

![image](https://github.com/guevarawebgraphics/fortellis-groundhogg-api-integration/assets/42199746/363a58c3-075b-4651-87c3-f80a5f727ced)

## Cron Job

To setup cron job use this command below

```
php artisan send_groundhogg_request:cron

```
and configure it on this page

![image](https://github.com/guevarawebgraphics/fortellis-groundhogg-api-integration/assets/42199746/3f30ca6d-02f6-45bf-81bc-a0125a218cb8)


## API Keys, Secrets, Endpoints, Subscription ID

Use this credentials for testing purposes. Please inject this code on your `.env` file

```
# Ground Hogg Webhook Listener URL
GROUNDHOGG_WEBHOOK_URL="https://mycrmplayground.com/wp-json/gh/v4/webhooks/475-webhook-listener?token=FLgcPbp"

# FORTELLIS
FORTELLIS_API_KEY=""
FORTELLIS_API_SECRET=""
FORTELLIS_SUBSCRIPTION_ID=""
FORTELLIS_BEARER_URL="https://identity.fortellis.io/oauth2/aus1p1ixy7YL8cMq02p7/v1/token"
FORTELLIS_SEARCH_API="https://api.fortellis.io/cdk-test/sales/v1/elead/customers/search"

```



## Wordpress Shortcode Setup

Import This Code


```
add_filter( 'groundhogg/webhooks/listener/data', 'create_or_update_groundhogg_contact', 10, 2 );

function create_or_update_groundhogg_contact( $return, $data ) {
// Ensure the necessary data is available
if ( ! isset( $data['firstName'] ) || ! isset( $data['email'] ) ) {
return $return;
}

    $name = sanitize_text_field( $data['firstName'] );
    $email = sanitize_email( $data['email'] );

    // Check if the contact already exists
    $contact = \Groundhogg\get_contactdata( $email );

    if ( ! $contact ) {
        // If the contact does not exist, create a new one
        $args = array(
            'first_name' => $name,
            'email'      => $email,
        );
        $contact = new \Groundhogg\Contact( $args );
    } else {
        // If the contact exists, update the name
        $contact->update( array( 'first_name' => $name ) );
    }

    return $return; // Return the original value to not interfere with other processes

}
```

Into your Active Theme > Function.php

![image](https://github.com/guevarawebgraphics/fortellis-groundhogg-api-integration/assets/42199746/597c19c0-1623-4f05-a583-0b1f59f2f9c8)


## Webhook Listener

![image](https://github.com/guevarawebgraphics/fortellis-groundhogg-api-integration/assets/42199746/f915e135-7620-473b-8ede-1d92756d0b7f)

