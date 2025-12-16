<?php
// Database configuration (if needed)

$host    = '127.0.0.1';    // or your hosting DB host, e.g. 127.0.0.1
$db      = 'company';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // show errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch as array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // better security
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "Connected successfully"; // you can test with this
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Site configuration
define('SITE_NAME', 'Golden Prosperous Group of Companies');
define('SITE_NAME_CHINESE', '鑫鴻集團');
define('SITE_EMAIL', 'gerrardlau@goldenprosperousrsb.com');
define('SITE_PHONE', ' 09-505 4433 ');
define('SITE_ADDRESS', 'B-46, Jalan Semambu Baru 2, 25300 Kuantan, Pahang Darul Makmur.');
define('SITE_WEBSITE', 'goldenprosperousrsb.com');
define('SITE_FACEBOOK', 'https://www.facebook.com/goldenprosperousgoc/');
define('SITE_GOOGLE_MAPS', 'https://maps.app.goo.gl/6Z5Wr25K8CATNgG18');

// Social Media Links (optional - leave empty if not used)
// define('SITE_TWITTER', '');
// define('SITE_LINKEDIN', '');
// define('SITE_INSTAGRAM', '');

// Timezone
date_default_timezone_set('Asia/Kuala_Lumpur');


?>

