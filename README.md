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
