<?php
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'ON' : 'OFF') . "<br>";

// Test fetching a public URL (e.g., Google)
$testUrl = "https://www.google.com";
$content = @file_get_contents($testUrl);

if ($content === false) {
    echo "Error: file_get_contents() failed to fetch $testUrl.<br>";
    echo "Possible reasons: allow_url_fopen is off, or server firewall blocks HTTP requests.";
} else {
    echo "Success: file_get_contents() works!";
}
?>