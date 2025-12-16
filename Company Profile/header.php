<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="container">
                <div class="nav-wrapper">
                    <div class="logo">
                        <a href="index.php">
                            <?php 
                            $logoPath = '';
                            if (file_exists('assets/images/Logo.jpg')) {
                                $logoPath = 'assets/images/Logo.jpg';
                            } elseif (file_exists('assets/images/logo.jpg')) {
                                $logoPath = 'assets/images/logo.jpg';
                            } elseif (file_exists('assets/images/logo.png')) {
                                $logoPath = 'assets/images/logo.png';
                            }
                            if ($logoPath): ?>
                                <img src="<?php echo $logoPath; ?>" alt="<?php echo SITE_NAME; ?>" class="logo-img">
                            <?php endif; ?>
                            <span class="logo-text">
                                <span class="logo-chinese"><?php echo defined('SITE_NAME_CHINESE') ? SITE_NAME_CHINESE : ''; ?></span>
                                <span class="logo-english"><?php echo SITE_NAME; ?></span>
                            </span>
                        </a>
                    </div>
                    <ul class="nav-menu" id="navMenu">
                        <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                        <li><a href="about.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">About Us</a></li>
                        <li><a href="services.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>">Our Business</a></li>
                        <li><a href="contact.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
                    </ul>
                    <div class="hamburger" id="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main>

