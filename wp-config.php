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
define( 'DB_NAME', 'majstar_archilabswp' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'majstar' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost:2013' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',         'b!9;,`xu]5lN9U}mCf _<}ZibfI&iUd!T|!~W/s?Q;:@recBP7A~a$4tD.P#}QYb' );
define( 'SECURE_AUTH_KEY',  'N1Ch;Th[,-IUI#MRayT?;h&1t_{u=H7dis2.z ,|u;rghfSyN_C5;[Nsi?.|itD6' );
define( 'LOGGED_IN_KEY',    'Ih:2Xb:Uy1&<etAAt`dCD7XDkM*btll#f0dqdK73_<OH htZ^;//@8 ZK[(y$6AN' );
define( 'NONCE_KEY',        ':[fp68TgC}cO8y[V@*.m3#!E!Ls+zD%Eb+D6)v2(nHQ??~v*JuXzetMqTdg*yL6Y' );
define( 'AUTH_SALT',        'rWSo7y8#Sl%B .Ev)sl:  a!ZkgqF)=iKGKGdAiST/azP~ co]bosDwz ,9)` }d' );
define( 'SECURE_AUTH_SALT', ')/=wK%P8zUb/<Og-#hj-(Cvhf9O#Z4OCTUv-?& g?9L#I{FW{,jl!C7 Y=3C+A?=' );
define( 'LOGGED_IN_SALT',   '{,eD6Z,n?g%D[CBjBh_[C0pfRw.qm}-;agDSV1h!!^nY`0ID7uRg$T|F>4:Qn%?l' );
define( 'NONCE_SALT',       'BR|/$LVKBm0d6><XCk]6$b[*lU>Q_b!!8)9D0D`N]Q?Hh~6Li3mUO~Lc?I-?!=)-' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
