<pre>
<?php

print_r(get_loaded_extensions());
// apc_delete('_ah_app_identity_:https://www.googleapis.com/auth/devstorage.read_only');
// apc_delete('_ah_app_identity_:https://www.googleapis.com/auth/devstorage.read_write');
//print_r(file_get_contents('https://cloud.bloombees.com/api/bbchat/updates/374?nolog'));

$context = stream_context_create(
    array(
        'ssl' => array('verify_peer' => false, 'allow_self_signed' => true),
        'http' => array( 'method' => 'GET' )
    )
);
print_r(file_get_contents('https://cloud.bloombees.com/api/bbchat/updates/374?nolog', false, $context));


?>
</pre>