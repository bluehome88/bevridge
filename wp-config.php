<?php
@include __DIR__ . '/local-config.php';
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
@define('DB_NAME', 'bevridge');

/** MySQL database username */
@define('DB_USER', 'root');

/** MySQL database password */
@define('DB_PASSWORD', '');

/** MySQL hostname */
@define('DB_HOST', '127.0.0.1');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '5Syq|X?ZM2s>tiA}h<(@p7,-NuqXWA(d%8DQ/IjgxTJP/goz9OB*4v(oQi34X,uy');
define('SECURE_AUTH_KEY',  'mV[*@a-6&(d:q*2d(|kiH@/av~u?R?d<<(3y0$1u-kl&=gL+Yp}?L$qXqbJ&37GV');
define('LOGGED_IN_KEY',    'NQn<q(IR;X5{`wDqIt`FGUdfO*LQ1t4y:1ZK7Vh1vE}UzP-&=G,<BGbFRCsvZN.3');
define('NONCE_KEY',        '7;QX$6NGK)IdX|fO).mI#owWS@|]D;MqGZykh}K5ih</*=iD2*=xe-T*#N-9tz3_');
define('AUTH_SALT',        'ICFv1}!@#~#p6VV,y|1BH)d[C?Q~$wJ[&Ljh ?7EEQwvf&DwGeOPPw1AG*+(y9tN');
define('SECURE_AUTH_SALT', 'B[N?Y^%1v)%_sxw8qE0`B&`fG8Hm/&<iG7$:(~([UN|6x_AcMJ][0>Am_ yc<<@!');
define('LOGGED_IN_SALT',   'mlpDOwA$zwe@givGu:A%B,E8YI.&,O.H1BQ)y>yDkYJ8C,X|d[jafMbOJTK9 N8`');
define('NONCE_SALT',       '([`t<B`^B66P%p/TVNs.sdgvf `(psl7ql&ONl%&@_E`@/jJveuCeQ~CP-%-$s(2');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
@define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
