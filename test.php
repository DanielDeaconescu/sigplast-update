<?php
// Minimal Turnstile test
$secret = "0x4AAAAAABavltz2SURcNVggAl_nybWCvAk";
$token = $_POST['cf-turnstile-response'] ?? '';

$data = [
    'secret' => $secret,
    'response' => $token,
    'remoteip' => $_SERVER['REMOTE_ADDR']
];

$url = "https://challenges.cloudflare.com/turnstile/v0/siteverify";
$options = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);
var_dump(json_decode($result, true));
?>