<?php

return [
    'name' => getenv('APP_NAME') ?: 'LeadPulse CRM',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN),
    'url' => getenv('APP_URL') ?: 'http://localhost:8000',
    'timezone' => 'Asia/Tashkent',
    'locale' => 'uz',
    'session_lifetime' => (int)(getenv('SESSION_LIFETIME') ?: 120),
];
