<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u0819189_wp383' );

/** MySQL database username */
define( 'DB_USER', 'u0819189_wp383' );

/** MySQL database password */
define( 'DB_PASSWORD', ']7S5p]1MWH' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'yyoxonqgmja6p7pvoy9ziscu1fkdcloupuao6etldlyj5bghfohksqnlb8w8dqzk' );
define( 'SECURE_AUTH_KEY',  'mvw5kwdsan2inn0z6kaczxyemey8szmfmc805j8hcwcbtn6ns1wevrodetvboi4q' );
define( 'LOGGED_IN_KEY',    'yps43bipkuxlzlhymqogzkmimoj7wbjztvbabqkh7850afqzyjr7kgww16nvlzlj' );
define( 'NONCE_KEY',        'oblgfviqc8yar8nln1dtsugmw03kpuiso52omf3lzpk1po9kjilxejuorpdopcga' );
define( 'AUTH_SALT',        'r9xenzyoipjjmwnabq6kgffvzecqdi8ubyx6sjufme2ehcu7woqbat5clyhlzuu5' );
define( 'SECURE_AUTH_SALT', '1qizz51ljgk40jdlanimkhi6a0y5qhbrxckobkwzk0tpabm77pmz5mwr9kcrrpgf' );
define( 'LOGGED_IN_SALT',   'dtyqj4lzhhxo49fqepj3nal6wibc2djkyu6dsf2px6qtgbkdvwdg5r3wv3c0ltgm' );
define( 'NONCE_SALT',       'o2o70z6irgjq9y6ea6hbnxtw9j0d0t0g0ulz4bylp4hra2yreu92kku3lrwof2lx' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpkd_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
