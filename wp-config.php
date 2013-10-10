<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'teamgoblin');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         ',SoC~[WC=%M:+%fq 8+tUc~UVS~0#||CRo1Tb2kjQv50jT$`:xHf-I(t-c<<t7]a');
define('SECURE_AUTH_KEY',  '1GS/y-5/9t)8QK#pI/*~)bY]m8dUxu5|ax%%DwI:<>r.eK:nj+|SPf^U+JQS5bpX');
define('LOGGED_IN_KEY',    '+=v6)=3^R0-(#`mf{4F!7f-L-mDrsrpK4Z0Q{Q.TrZml5I. #y:Peq`&A+|Y))jp');
define('NONCE_KEY',        'xcn$<St)fmL*L;x@o$1pRg-*%wJ5YANuZ~K:A*1+O5N|:${3 tY%nt(sE)nN!7):');
define('AUTH_SALT',        '|4bp#+j{OGN6G_ksP93?S-HRo}[,)KFxxrw;bf>_6^Qg}tQ]dyaP0--&a:V+{,Dn');
define('SECURE_AUTH_SALT', 'qtIKx-M6Utk>6Nbw)xKMVB-.Q# mY.+X/2L0>J<R,@-MrlPqdI=v=|z+-mch>5I4');
define('LOGGED_IN_SALT',   's lO+P#i(,h@F7]YF%&O*%r(DYA:`t(u:B|8+0 WDm?FDTSrI_Hy#pO5qzfjL<t.');
define('NONCE_SALT',       'B`XHi>WXV*z(-C8$iUW^*+kSF#K*OA(oFi8H)#|UZlDbUzt!HsDRAy>rI!3bnMs[');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'tg_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
define('CUSTOM', true); //we are in custom mode

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
