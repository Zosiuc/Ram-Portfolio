<?php

return [
'paths' => ['/*', 'sanctum/csrf-cookie','login', 'logout'],
'allowed_methods' => ['*'],
'allowed_origins' => ['http://127.0.0.1:5173'], // dit laat alle frontends toe
'allowed_origins_patterns' => [],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => true
];
