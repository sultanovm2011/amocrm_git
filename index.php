<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
#define( 'WP_USE_THEMES', true );

/** Loads the WordPress Environment and Template */
//require( dirname( __FILE__ ) . '/wp-blog-header.php' );
// $webhookContent = "";
// $Response = fopen('php://input' , 'rb');
// while (!feof($webhook)) { $webhookContent .= fread($webhook, 4096);}
// fclose($webhook);
function SyncData()
{
    require($_SERVER['DOCUMENT_ROOT'] . '/auth.php');
    sleep(30);
    require($_SERVER['DOCUMENT_ROOT'] . '/contacts.php');
    sleep(300);
    require($_SERVER['DOCUMENT_ROOT'] . '/buyers.php');
    sleep(300);
    require($_SERVER['DOCUMENT_ROOT'] . '/CUSTOMFIELDS_BUYERS.php');
    sleep(300);
    require($_SERVER['DOCUMENT_ROOT'] . '/leads.php');
    sleep(300);
    require($_SERVER['DOCUMENT_ROOT'] . '/CUSTOMFIELDS_LEADS.php');
    sleep(300);
    require($_SERVER['DOCUMENT_ROOT'] . '/pipeline.php');
    sleep(300);
    require($_SERVER['DOCUMENT_ROOT'] . '/transactions.php');
}
SyncData();