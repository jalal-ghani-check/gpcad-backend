<?php
return [
  'mongodb' => [
    'db_name' => env('MONGO_DB', 'police_system'),
    'db_connection_str' => env('MONGO_DB_CONNECTION_STR', 'mongodb://127.0.0.1:27017'),
    'db_host' => env('MONGO_DB_CONNECTION_HOST', '127.0.0.1'),
    'db_port' => env('MONGO_DB_CONNECTION_PORT', '27017'),
  ],
  'api_user_token_expiry_ts' => env('MOBILE_API_USER_TOKEN_EXPIRY_TS',1800),
  'api_secret_key_validation_ts' => env('MOBILE_API_SECRET_KEY_VALIDATION_TS', 30), // Value should be in Seconds

];
