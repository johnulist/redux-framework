<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function redux_get_file_version ( $file ) {

    // Avoid notices if file does not exist
    if ( !file_exists ( $file ) ) {
        return '';
    }

    // We don't need to write to the file, so just open for reading.
    $fp = fopen ( $file, 'r' );

    // Pull only the first 8kiB of the file in.
    $file_data = fread ( $fp, 8192 );

    // PHP will close file handle, but we are good citizens.
    fclose ( $fp );

    // Make sure we catch CR-only line endings.
    $file_data = str_replace ( "\r", "\n", $file_data );
    $version = '';

    if ( preg_match ( '/^[ \t\/*#@]*' . preg_quote ( '@version', '/' ) . '(.*)$/mi', $file_data, $match ) && $match[ 1 ] )
        $version = _cleanup_header_comment ( $match[ 1 ] );

    return $version;
}
    
function redux_scan_template_files ( $template_path ) {
    $files = scandir ( $template_path );
    $result = array();

    if ( $files ) {
        foreach ( $files as $key => $value ) {
            if ( !in_array ( $value, array( ".", ".." ) ) ) {
                if ( is_dir ( $template_path . DIRECTORY_SEPARATOR . $value ) ) {
                    $sub_files = redux_scan_template_files ( $template_path . DIRECTORY_SEPARATOR . $value );
                    foreach ( $sub_files as $sub_file ) {
                        $result[] = $value . DIRECTORY_SEPARATOR . $sub_file;
                    }
                } else {
                    $result[] = $value;
                }
            }
        }
    }
    
    return $result;
}

function redux_clean( $var ) {
    return sanitize_text_field( $var );
}

function redux_let_to_num( $size ) {
    $l   = substr( $size, -1 );
    $ret = substr( $size, 0, -1 );
    
    switch ( strtoupper( $l ) ) {
        case 'P':
            $ret *= 1024;
        //break;
        case 'T':
            $ret *= 1024;
        //break;
        case 'G':
            $ret *= 1024;
        //break;
        case 'M':
            $ret *= 1024;
        //break;
        case 'K':
            $ret *= 1024;
        //break;
    }
    
    return $ret;
}

?>
<div class="wrap about-wrap redux-status">
    <h1><?php _e( 'Redux Framework - System Status', 'redux-framework' ); ?></h1>

    <div
        class="about-text"><?php _e( 'Our core mantra at Redux is backwards compatibility. With hundreds of thousands of instances worldwide, you can be assured that we will take care of you and your clients.', 'redux-framework' ); ?></div>
    <div
        class="redux-badge"><i
            class="el el-redux"></i><span><?php printf( __( 'Version %s', 'redux-framework' ), ReduxFramework::$_version ); ?></span>
    </div>

    <p class="redux-actions">
        <a href="http://docs.reduxframework.com/" class="docs button button-primary">Docs</a>
        <a href="https://wordpress.org/plugins/redux-framework/" class="review-us button button-primary" target="_blank">Review Us</a>
        <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MMFMHWUPKHKPW" class="review-us button button-primary" target="_blank">Donate</a>
        <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://reduxframework.com" data-text="Reduce your dev time! Redux is the most powerful option framework for WordPress on the web" data-via="ReduxFramework" data-size="large" data-hashtags="Redux">Tweet</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
    </p>

    <?php $this->tabs(); ?>
                
    <div class="updated redux-message">
            <p><?php _e( 'Please copy and paste this information in your ticket when contacting support:', 'redux-framework' ); ?> </p>
            <p class="submit"><a href="#" class="button-primary debug-report"><?php _e( 'Get System Report', 'redux-framework' ); ?></a>
            <a class="skip button-primary" href="http://docs.reduxframework.com/understanding-the-redux-framework-system-status-report/" target="_blank"><?php _e( 'Understanding the Status Report', 'redux-framework' ); ?></a></p>
            <div id="debug-report">
                    <textarea readonly="readonly"></textarea>
                    <p class="submit"><button id="copy-for-support" class="button-primary redux-hint-qtip" href="#" qtip-content="<?php _e( 'Copied!', 'redux-framework' ); ?>"><?php _e( 'Copy for Support', 'redux-framework' ); ?></button></p>
            </div>
    </div>
    <br/>
    <table class="redux_status_table widefat" cellspacing="0" id="status">
        <thead>
            <tr>
                <th colspan="3" data-export-label="WordPress Environment"><?php _e( 'WordPress Environment', 'redux-framework' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td data-export-label="Home URL"><?php _e( 'Home URL', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The URL of your site\'s homepage.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php echo home_url(); ?></td>
            </tr>
            <tr>
                <td data-export-label="Site URL"><?php _e( 'Site URL', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The root URL of your site.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php echo site_url(); ?></td>
            </tr>
            <tr>
                <td data-export-label="Redux Version"><?php _e( 'Redux Version', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The version of Redux Framework installed on your site.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php echo esc_html( ReduxFramework::$_version ); ?></td>
            </tr>
            <tr>
                <td data-export-label="Redux Data Directory Writable"><?php _e( 'Redux Data Directory Writable', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'Redux and its extensions write data to the <code>uploads</code> directory. This directory must be writable.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php
                    if ( @fopen( ReduxFramework::$_upload_dir . 'test-log.log', 'a' ) ) {
                        echo '<mark class="yes">' . '&#10004; <code>' . ReduxFramework::$_upload_dir . '</code></mark> ';
                    } else {
                        printf( '<mark class="error">' . '&#10005; ' . __( 'To allow logging, make <code>%s</code> writable.', 'redux-framework' ) . '</mark>', ReduxFramework::$_upload_dir );
                    }
                ?></td>
            </tr>
            <tr>
                <td data-export-label="WP Version"><?php _e( 'WP Version', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The version of WordPress installed on your site.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php bloginfo('version'); ?></td>
            </tr>
            <tr>
                <td data-export-label="WP Multisite"><?php _e( 'WP Multisite', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'Whether or not you have WordPress Multisite enabled.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php if ( is_multisite() ) echo '&#10004;'; else echo '&ndash;'; ?></td>
            </tr>
            <tr>
                <td data-export-label="WP Memory Limit"><?php _e( 'WP Memory Limit', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The maximum amount of memory (RAM) that your site can use at one time.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php
                    $memory = redux_let_to_num( WP_MEMORY_LIMIT );

                    if ( $memory < 67108864 ) {
                        echo '<mark class="error">' . sprintf( __( '%s - We recommend setting memory to at least 64MB. See: <a href="%s" target="_blank">Increasing memory allocated to PHP</a>', 'redux-framework' ), size_format( $memory ), 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP' ) . '</mark>';
                    } else {
                        echo '<mark class="yes">' . size_format( $memory ) . '</mark>';
                    }
                ?></td>
            </tr>
            <tr>
                <td data-export-label="WP Debug Mode"><?php _e( 'WP Debug Mode', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'Displays whether or not WordPress is in Debug Mode.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php if ( defined('WP_DEBUG') && WP_DEBUG ) echo '<mark class="yes">' . '&#10004;' . '</mark>'; else echo '<mark class="no">' . '&ndash;' . '</mark>'; ?></td>
            </tr>
            <tr>
                <td data-export-label="Language"><?php _e( 'Language', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The current language used by WordPress. Default = English', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php echo get_locale() ?></td>
            </tr>
        </tbody>
    </table>
    <table class="redux_status_table widefat" cellspacing="0" id="status">
        <thead>
            <tr>
                <th colspan="3" data-export-label="Server Environment"><?php _e( 'Server Environment', 'redux-framework' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td data-export-label="Server Info"><?php _e( 'Server Info', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'Information about the web server that is currently hosting your site.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ); ?></td>
            </tr>
            <tr>
                <td data-export-label="PHP Version"><?php _e( 'PHP Version', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The version of PHP installed on your hosting server.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php if ( function_exists( 'phpversion' ) ) echo esc_html( phpversion() ); ?></td>
            </tr>
            <?php if ( function_exists( 'ini_get' ) ) : ?>
                    <tr>
                        <td data-export-label="PHP Post Max Size"><?php _e( 'PHP Post Max Size', 'redux-framework' ); ?>:</td>
                        <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The largest filesize that can be contained in one post.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                        <td><?php echo size_format( redux_let_to_num( ini_get('post_max_size') ) ); ?></td>
                    </tr>
                    <tr>
                        <td data-export-label="PHP Time Limit"><?php _e( 'PHP Time Limit', 'redux-framework' ); ?>:</td>
                        <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups)', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                        <td><?php echo ini_get('max_execution_time'); ?></td>
                    </tr>
                    <tr>
                        <td data-export-label="PHP Max Input Vars"><?php _e( 'PHP Max Input Vars', 'redux-framework' ); ?>:</td>
                        <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The maximum number of variables your server can use for a single function to avoid overloads.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                        <td><?php echo ini_get('max_input_vars'); ?></td>
                    </tr>
                    <tr>
                        <td data-export-label="SUHOSIN Installed"><?php _e( 'SUHOSIN Installed', 'redux-framework' ); ?>:</td>
                        <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'Suhosin is an advanced protection system for PHP installations. It was designed to protect your servers on the one hand against a number of well known problems in PHP applications and on the other hand against potential unknown vulnerabilities within these applications or the PHP core itself.
If enabled on your server, Suhosin may need to be configured to increase its data submission limits.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                        <td><?php echo extension_loaded( 'suhosin' ) ? '&#10004;' : '&ndash;'; ?></td>
                    </tr>
            <?php endif; ?>
            <tr>
                <td data-export-label="MySQL Version"><?php _e( 'MySQL Version', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The version of MySQL installed on your hosting server.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td>
                    <?php
                    /** @global wpdb $wpdb */
                    global $wpdb;
                    echo $wpdb->db_version();
                    ?>
                </td>
            </tr>
            <tr>
                <td data-export-label="Max Upload Size"><?php _e( 'Max Upload Size', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The largest filesize that can be uploaded to your WordPress installation.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php echo size_format( wp_max_upload_size() ); ?></td>
            </tr>
            <tr>
                <td data-export-label="Default Timezone is UTC"><?php _e( 'Default Timezone is UTC', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The default timezone for your server.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php
                    $default_timezone = date_default_timezone_get();
                    if ( 'UTC' !== $default_timezone ) {
                        echo '<mark class="error">' . '&#10005; ' . sprintf( __( 'Default timezone is %s - it should be UTC', 'redux-framework' ), $default_timezone ) . '</mark>';
                    } else {
                        echo '<mark class="yes">' . '&#10004;' . '</mark>';
                    } ?>
                </td>
            </tr>
            <?php
            $posting = array();

            // fsockopen/cURL
            $posting['fsockopen_curl']['name'] = 'fsockopen/cURL';
            $posting['fsockopen_curl']['help'] = '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'Payment gateways can use cURL to communicate with remote servers to authorize payments, other plugins may also use it when communicating with remote services.', 'redux-framework'  ) . '">[?]</a>';

            if ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ) {
                $posting['fsockopen_curl']['success'] = true;
            } else {
                $posting['fsockopen_curl']['success'] = false;
                $posting['fsockopen_curl']['note']    = __( 'Your server does not have fsockopen or cURL enabled - PayPal IPN and other scripts which communicate with other servers will not work. Contact your hosting provider.', 'redux-framework' ). '</mark>';
            }

            // SOAP
            $posting['soap_client']['name'] = 'SoapClient';
            $posting['soap_client']['help'] = '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'Some webservices like shipping use SOAP to get information from remote servers, for example, live shipping quotes from FedEx require SOAP to be installed.', 'redux-framework'  ) . '">[?]</a>';

            if ( class_exists( 'SoapClient' ) ) {
                $posting['soap_client']['success'] = true;
            } else {
                $posting['soap_client']['success'] = false;
                $posting['soap_client']['note']    = sprintf( __( 'Your server does not have the <a href="%s">SOAP Client</a> class enabled - some gateway plugins which use SOAP may not work as expected.', 'redux-framework' ), 'http://php.net/manual/en/class.soapclient.php' ) . '</mark>';
            }

            // DOMDocument
            $posting['dom_document']['name'] = 'DOMDocument';
            $posting['dom_document']['help'] = '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'HTML/Multipart emails use DOMDocument to generate inline CSS in templates.', 'redux-framework'  ) . '">[?]</a>';

            if ( class_exists( 'DOMDocument' ) ) {
                $posting['dom_document']['success'] = true;
            } else {
                $posting['dom_document']['success'] = false;
                $posting['dom_document']['note']    = sprintf( __( 'Your server does not have the <a href="%s">DOMDocument</a> class enabled - HTML/Multipart emails, and also some extensions, will not work without DOMDocument.', 'redux-framework' ), 'http://php.net/manual/en/class.domdocument.php' ) . '</mark>';
            }

            // GZIP
            $posting['gzip']['name'] = 'GZip';
            $posting['gzip']['help'] = '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'GZip (gzopen) is used to open the GEOIP database from MaxMind.', 'redux-framework'  ) . '">[?]</a>';

            if ( is_callable( 'gzopen' ) ) {
                $posting['gzip']['success'] = true;
            } else {
                $posting['gzip']['success'] = false;
                $posting['gzip']['note']    = sprintf( __( 'Your server does not support the <a href="%s">gzopen</a> function - this is required to use the GeoIP database from MaxMind. The API fallback will be used instead for geolocation.', 'redux-framework' ), 'http://php.net/manual/en/zlib.installation.php' ) . '</mark>';
            }

            // WP Remote Post Check
            $posting['wp_remote_post']['name'] = __( 'Remote Post', 'redux-framework');
            $posting['wp_remote_post']['help'] = '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'PayPal uses this method of communicating when sending back transaction information.', 'redux-framework'  ) . '">[?]</a>';

            $response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', array(
                'sslverify'  => false,
                'timeout'    => 60,
                'user-agent' => 'ReduxFramework/' . ReduxFramework::$_version,
                'body'       => array(
                    'cmd'    => '_notify-validate'
                )
            ) );

            if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
                $posting['wp_remote_post']['success'] = true;
            } else {
                $posting['wp_remote_post']['note']    = __( 'wp_remote_post() failed. PayPal IPN won\'t work with your server. Contact your hosting provider.', 'redux-framework' );
                
                if ( $response->get_error_message() ) {
                    $posting['wp_remote_post']['note'] .= ' ' . sprintf( __( 'Error: %s', 'redux-framework' ), rexux_clean( $response->get_error_message() ) );
                }
                
                $posting['wp_remote_post']['success'] = false;
            }

            // WP Remote Get Check
            $posting['wp_remote_get']['name'] = __( 'Remote Get', 'redux-framework');
            $posting['wp_remote_get']['help'] = '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'Redux Framework plugins may use this method of communication when checking for plugin updates.', 'redux-framework'  ) . '">[?]</a>';

            $response = wp_remote_get( 'http://www.woothemes.com/wc-api/product-key-api?request=ping&network=' . ( is_multisite() ? '1' : '0' ) );

            if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
                $posting['wp_remote_get']['success'] = true;
            } else {
                $posting['wp_remote_get']['note']    = __( 'wp_remote_get() failed. The Redux Framework plugin updater won\'t work with your server. Contact your hosting provider.', 'redux-framework' );
                if ( $response->get_error_message() ) {
                        $posting['wp_remote_get']['note'] .= ' ' . sprintf( __( 'Error: %s', 'redux-framework' ), redux_clean( $response->get_error_message() ) );
                }
                
                $posting['wp_remote_get']['success'] = false;
            }

            $posting = apply_filters( 'redux_debug_posting', $posting );

            foreach ( $posting as $post ) {
                $mark = ! empty( $post['success'] ) ? 'yes' : 'error';
                ?>
                <tr>
                    <td data-export-label="<?php echo esc_html( $post['name'] ); ?>"><?php echo esc_html( $post['name'] ); ?>:</td>
                    <td><?php echo isset( $post['help'] ) ? $post['help'] : ''; ?></td>
                    <td class="help">
                        <mark class="<?php echo $mark; ?>">
                            <?php echo ! empty( $post['success'] ) ? '&#10004' : '&#10005'; ?>
                            <?php echo ! empty( $post['note'] ) ? wp_kses_data( $post['note'] ) : ''; ?>
                        </mark>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <table class="redux_status_table widefat" cellspacing="0" id="status">
        <thead>
            <tr>
                <th colspan="3" data-export-label="Server Locale"><?php _e( 'Server Locale', 'redux-framework' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $locale = localeconv();
                $locale_help = array(
                    'decimal_point'     => __( 'The character used for decimal points.', 'redux-framework' ),
                    'thousands_sep'     => __( 'The character used for a thousands separator.', 'redux-framework' ),
                    'mon_decimal_point' => __( 'The character used for decimal points in monetary values.', 'redux-framework' ),
                    'mon_thousands_sep' => __( 'The character used for a thousands separator in monetary values.', 'redux-framework' ),
                );

                foreach ( $locale as $key => $val ) {
                    if ( in_array( $key, array( 'decimal_point', 'mon_decimal_point', 'thousands_sep', 'mon_thousands_sep' ) ) ) {
                        echo '<tr><td data-export-label="' . $key . '">' . $key . ':</td><td class="help"><a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr( $locale_help[$key]  ) . '">[?]</a></td><td>' . ( $val ? $val : __( 'N/A', 'redux-framework' ) ) . '</td></tr>';
                    }
                }
            ?>
        </tbody>
    </table>
    <table class="redux_status_table widefat" cellspacing="0" id="status">
        <thead>
            <tr>
                <th colspan="3" data-export-label="Active Plugins (<?php echo count( (array) get_option( 'active_plugins' ) ); ?>)"><?php _e( 'Active Plugins', 'redux-framework' ); ?> (<?php echo count( (array) get_option( 'active_plugins' ) ); ?>)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $active_plugins = (array) get_option( 'active_plugins', array() );

            if ( is_multisite() ) {
                $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
            }

            foreach ( $active_plugins as $plugin ) {
                $plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
                $dirname        = dirname( $plugin );
                $version_string = '';
                $network_string = '';

                if ( ! empty( $plugin_data['Name'] ) ) {
                    // link the plugin name to the plugin url if available
                    $plugin_name = esc_html( $plugin_data['Name'] );

                    if ( ! empty( $plugin_data['PluginURI'] ) ) {
                        $plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . __( 'Visit plugin homepage' , 'redux-framework' ) . '">' . $plugin_name . '</a>';
                    }

//                    if ( strstr( $dirname, 'woocommerce-' ) ) {
//                        if ( false === ( $version_data = get_transient( md5( $plugin ) . '_version_data' ) ) ) {
//                            $changelog = wp_remote_get( 'http://dzv365zjfbd8v.cloudfront.net/changelogs/' . $dirname . '/changelog.txt' );
//                            $cl_lines  = explode( "\n", wp_remote_retrieve_body( $changelog ) );
//                            if ( ! empty( $cl_lines ) ) {
//                                foreach ( $cl_lines as $line_num => $cl_line ) {
//                                    if ( preg_match( '/^[0-9]/', $cl_line ) ) {
//                                        $date         = str_replace( '.' , '-' , trim( substr( $cl_line , 0 , strpos( $cl_line , '-' ) ) ) );
//                                        $version      = preg_replace( '~[^0-9,.]~' , '' ,stristr( $cl_line , "version" ) );
//                                        $update       = trim( str_replace( "*" , "" , $cl_lines[ $line_num + 1 ] ) );
//                                        $version_data = array( 'date' => $date , 'version' => $version , 'update' => $update , 'changelog' => $changelog );
//                                        set_transient( md5( $plugin ) . '_version_data', $version_data, DAY_IN_SECONDS );
//                                        break;
//                                    }
//                                }
//                            }
//                        }
//
//                        if ( ! empty( $version_data['version'] ) && version_compare( $version_data['version'], $plugin_data['Version'], '>' ) ) {
//                            $version_string = ' &ndash; <strong style="color:red;">' . esc_html( sprintf( _x( '%s is available', 'Version info', 'redux-framework' ), $version_data['version'] ) ) . '</strong>';
//                        }
//
//                        if ( $plugin_data['Network'] != false ) {
//                            $network_string = ' &ndash; <strong style="color:black;">' . __( 'Network enabled', 'redux-framework' ) . '</strong>';
//                        }
//                    }

                    ?>
                    <tr>
                        <td><?php echo $plugin_name; ?></td>
                        <td class="help">&nbsp;</td>
                        <td><?php echo sprintf( _x( 'by %s', 'by author', 'redux-framework' ), $plugin_data['Author'] ) . ' &ndash; ' . esc_html( $plugin_data['Version'] ) . $version_string . $network_string; ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
    <table class="redux_status_table widefat" cellspacing="0" id="status">
        <thead>
            <tr>
                <th colspan="3" data-export-label="Settings"><?php _e( 'Settings', 'redux-framework' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td data-export-label="API Enabled"><?php _e( 'API Enabled', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'Does your site have REST API enabled?', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php echo 'yes' === get_option( 'woocommerce_api_enabled' ) ? '<mark class="yes">'.'&#10004;'.'</mark>' : '<mark class="no">'.'&ndash;'.'</mark>'; ?></td>
            </tr>
            <tr>
                <td data-export-label="Taxes Enabled"><?php _e( 'Taxes Enabled', 'redux-framework' ) ?></td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'Does your site have taxes enabled?', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php echo wc_tax_enabled() ? '<mark class="yes">' . '&#10004;' . '</mark>' : '<mark class="no">' . '&ndash;' . '</mark>'; ?></td>
            </tr>
            <tr>
                <td data-export-label="Shipping Enabled"><?php _e( 'Shipping Enabled', 'redux-framework' ) ?></td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'Does your site have shipping enabled?', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php echo 'yes' === get_option( 'woocommerce_calc_shipping' ) ? '<mark class="yes">' . '&#10004;' . '</mark>' : '<mark class="no">' . '&ndash;' . '</mark>'; ?></td>
            </tr>
            <tr>
                <td data-export-label="Force SSL"><?php _e( 'Force SSL', 'redux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'Does your site force a SSL Certificate for transactions?', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php echo 'yes' === get_option( 'woocommerce_force_ssl_checkout' ) ? '<mark class="yes">'.'&#10004;'.'</mark>' : '<mark class="no">'.'&ndash;'.'</mark>'; ?></td>
            </tr>
            <tr>
                <td data-export-label="Currency"><?php _e( 'Currency', 'redux-framework' ) ?></td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'What currency prices are listed at in the catalog and which currency gateways will take payments in.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php echo get_woocommerce_currency(); ?> (<?php echo get_woocommerce_currency_symbol() ?>)</td>
            </tr>
            <tr>
                <td data-export-label="Currency Position"><?php _e( 'Currency Position', 'redux-framework' ) ?></td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The position of the currency symbol.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php echo get_option( 'woocommerce_currency_pos' ); ?></td>
            </tr>
            <tr>
                <td data-export-label="Thousand Separator"><?php _e( 'Thousand Separator', 'redux-framework' ) ?></td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The thousand separator of displayed prices.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php echo wc_get_price_thousand_separator(); ?></td>
            </tr>
            <tr>
                <td data-export-label="Decimal Separator"><?php _e( 'Decimal Separator', 'redux-framework' ) ?></td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The decimal separator of displayed prices.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php echo wc_get_price_decimal_separator(); ?></td>
            </tr>
            <tr>
                <td data-export-label="Number of Decimals"><?php _e( 'Number of Decimals', 'redux-framework' ) ?></td>
                <td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The number of decimal points shown in displayed prices.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
                <td><?php echo wc_get_price_decimals(); ?></td>
            </tr>
        </tbody>
    </table>
<!--    <table class="redux_status_table widefat" cellspacing="0" id="status">
            <thead>
                    <tr>
                            <th colspan="3" data-export-label="WC Pages"><?php _e( 'WC Pages', 'redux-framework' ); ?></th>
                    </tr>
            </thead>
            <tbody>
                    <?php /*
                            $check_pages = array(
                                    _x( 'Shop Base', 'Page setting', 'redux-framework' ) => array(
                                                    'option'    => 'woocommerce_shop_page_id',
                                                    'shortcode' => '',
                                                    'help'      => __( 'The URL of your WooCommerce shop\'s homepage (along with the Page ID).', 'redux-framework' ),
                                            ),
                                    _x( 'Cart', 'Page setting', 'redux-framework' ) => array(
                                                    'option'    => 'woocommerce_cart_page_id',
                                                    'shortcode' => '[' . apply_filters( 'woocommerce_cart_shortcode_tag', 'woocommerce_cart' ) . ']',
                                                    'help'      => __( 'The URL of your WooCommerce shop\'s cart (along with the page ID).', 'redux-framework' ),
                                            ),
                                    _x( 'Checkout', 'Page setting', 'redux-framework' ) => array(
                                                    'option'    => 'woocommerce_checkout_page_id',
                                                    'shortcode' => '[' . apply_filters( 'woocommerce_checkout_shortcode_tag', 'woocommerce_checkout' ) . ']',
                                                    'help'      => __( 'The URL of your WooCommerce shop\'s checkout (along with the page ID).', 'redux-framework' ),
                                            ),
                                    _x( 'My Account', 'Page setting', 'redux-framework' ) => array(
                                                    'option'    => 'woocommerce_myaccount_page_id',
                                                    'shortcode' => '[' . apply_filters( 'woocommerce_my_account_shortcode_tag', 'woocommerce_my_account' ) . ']',
                                                    'help'      => __( 'The URL of your WooCommerce shop\'s “My Account” Page (along with the page ID).', 'redux-framework' ),
                                            )
                            );

                            $alt = 1;

                            foreach ( $check_pages as $page_name => $values ) {
                                    $error   = false;
                                    $page_id = get_option( $values['option'] );

                                    if ( $page_id ) {
                                            $page_name = '<a href="' . get_edit_post_link( $page_id ) . '" title="' . sprintf( _x( 'Edit %s page', 'WC Pages links in the System Status', 'redux-framework' ), esc_html( $page_name ) ) . '">' . esc_html( $page_name ) . '</a>';
                                    } else {
                                            $page_name = esc_html( $page_name );
                                    }

                                    echo '<tr><td data-export-label="' . esc_attr( $page_name ) . '">' . $page_name . ':</td>';
                                    echo '<td class="help"><a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr( $values['help']  ) . '">[?]</a></td><td>';

                                    // Page ID check
                                    if ( ! $page_id ) {
                                            echo '<mark class="error">' . __( 'Page not set', 'redux-framework' ) . '</mark>';
                                            $error = true;
                                    } else {

                                            // Shortcode check
                                            if ( $values['shortcode'] ) {
                                                    $page = get_post( $page_id );

                                                    if ( empty( $page ) ) {

                                                            echo '<mark class="error">' . sprintf( __( 'Page does not exist', 'redux-framework' ) ) . '</mark>';
                                                            $error = true;

                                                    } else if ( ! strstr( $page->post_content, $values['shortcode'] ) ) {

                                                            echo '<mark class="error">' . sprintf( __( 'Page does not contain the shortcode: %s', 'redux-framework' ), $values['shortcode'] ) . '</mark>';
                                                            $error = true;

                                                    }
                                            }

                                    }

                                    if ( ! $error ) echo '<mark class="yes">#' . absint( $page_id ) . ' - ' . str_replace( home_url(), '', get_permalink( $page_id ) ) . '</mark>';

                                    echo '</td></tr>';
                            } */
                    ?>
            </tbody>
    </table>-->
<!--<table class="redux_status_table widefat" cellspacing="0" id="status">
	<thead>
		<tr>
			<th colspan="3" data-export-label="Taxonomies"><?php _e( 'Taxonomies', 'redux-framework' ); ?><?php echo ' <a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'A list of taxonomy terms that can be used in regard to order/product statuses.', 'redux-framework' ) . '">[?]</a>'; ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td data-export-label="Product Types"><?php _e( 'Product Types', 'redux-framework' ); ?>:</td>
			<td class="help">&nbsp;</td>
			<td><?php/* 
				$display_terms = array();
				$terms = get_terms( 'product_type', array( 'hide_empty' => 0 ) );
				foreach ( $terms as $term ) {
					$display_terms[] = strtolower( $term->name ) . ' (' . $term->slug . ')';
				}
				echo implode( ', ', array_map( 'esc_html', $display_terms ) ); */
			?></td>
		</tr>
	</tbody>
</table>-->
<table class="redux_status_table widefat" cellspacing="0" id="status">
	<thead>
		<tr>
			<th colspan="3" data-export-label="Theme"><?php _e( 'Theme', 'redux-framework' ); ?></th>
		</tr>
	</thead>
		<?php
		$active_theme = wp_get_theme();
//		if ( $active_theme->{'Author URI'} == 'http://www.woothemes.com' ) {
//
//			$theme_dir = substr( strtolower( str_replace( ' ','', $active_theme->Name ) ), 0, 45 );
//
//			if ( false === ( $theme_version_data = get_transient( $theme_dir . '_version_data' ) ) ) {
//
//				$theme_changelog = wp_remote_get( 'http://dzv365zjfbd8v.cloudfront.net/changelogs/' . $theme_dir . '/changelog.txt' );
//				$cl_lines  = explode( "\n", wp_remote_retrieve_body( $theme_changelog ) );
//				if ( ! empty( $cl_lines ) ) {
//
//					foreach ( $cl_lines as $line_num => $cl_line ) {
//						if ( preg_match( '/^[0-9]/', $cl_line ) ) {
//
//							$theme_date         = str_replace( '.' , '-' , trim( substr( $cl_line , 0 , strpos( $cl_line , '-' ) ) ) );
//							$theme_version      = preg_replace( '~[^0-9,.]~' , '' ,stristr( $cl_line , "version" ) );
//							$theme_update       = trim( str_replace( "*" , "" , $cl_lines[ $line_num + 1 ] ) );
//							$theme_version_data = array( 'date' => $theme_date , 'version' => $theme_version , 'update' => $theme_update , 'changelog' => $theme_changelog );
//							set_transient( $theme_dir . '_version_data', $theme_version_data , DAY_IN_SECONDS );
//							break;
//						}
//					}
//				}
//			}
//		}
		?>
	<tbody>
		<tr>
			<td data-export-label="Name"><?php _e( 'Name', 'redux-framework' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The name of the current active theme.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
			<td><?php echo $active_theme->Name; ?></td>
		</tr>
		<tr>
			<td data-export-label="Version"><?php _e( 'Version', 'redux-framework' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The installed version of the current active theme.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
			<td><?php
				echo $active_theme->Version;

				if ( ! empty( $theme_version_data['version'] ) && version_compare( $theme_version_data['version'], $active_theme->Version, '!=' ) ) {
					echo ' &ndash; <strong style="color:red;">' . $theme_version_data['version'] . ' ' . __( 'is available', 'redux-framework' ) . '</strong>';
				}
			?></td>
		</tr>
		<tr>
			<td data-export-label="Author URL"><?php _e( 'Author URL', 'redux-framework' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The theme developers URL.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
			<td><?php echo $active_theme->{'Author URI'}; ?></td>
		</tr>
		<tr>
			<td data-export-label="Child Theme"><?php _e( 'Child Theme', 'redux-framework' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'Displays whether or not the current theme is a child theme.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
			<td><?php
				echo is_child_theme() ? '<mark class="yes">' . '&#10004;' . '</mark>' : '&#10005; &ndash; ' . sprintf( __( 'If you\'re modifying WooCommerce or a parent theme you didn\'t build personally we recommend using a child theme. See: <a href="%s" target="_blank">How to create a child theme</a>', 'redux-framework' ), 'http://codex.wordpress.org/Child_Themes' );
			?></td>
		</tr>
		<?php
		if( is_child_theme() ) :
			$parent_theme = wp_get_theme( $active_theme->Template );
		?>
		<tr>
			<td data-export-label="Parent Theme Name"><?php _e( 'Parent Theme Name', 'redux-framework' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The name of the parent theme.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
			<td><?php echo $parent_theme->Name; ?></td>
		</tr>
		<tr>
			<td data-export-label="Parent Theme Version"><?php _e( 'Parent Theme Version', 'redux-framework' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The installed version of the parent theme.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
			<td><?php echo  $parent_theme->Version; ?></td>
		</tr>
		<tr>
			<td data-export-label="Parent Theme Author URL"><?php _e( 'Parent Theme Author URL', 'redux-framework' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'The parent theme developers URL.', 'redux-framework'  ) . '">[?]</a>'; ?></td>
			<td><?php echo $parent_theme->{'Author URI'}; ?></td>
		</tr>
		<?php endif ?>
	</tbody>
</table>
<table class="redux_status_table widefat" cellspacing="0" id="status">
	<thead>
		<tr>
			<th colspan="3" data-export-label="Templates"><?php _e( 'Templates', 'redux-framework' ); ?><?php echo ' <a href="#" class="redux-hint-qtip" qtip-content="' . esc_attr__( 'This section shows any files that are overriding the default WooCommerce template pages.', 'redux-framework'  ) . '">[?]</a>'; ?></th>
		</tr>
	</thead>
	<tbody>
		<?php

			$template_paths     = apply_filters( 'redux_template_overrides_scan_paths', array( 'ReduxFramework' => ReduxFramework::$_dir . 'templates/panel' ) );
			$scanned_files      = array();
			$found_files        = array();
			$outdated_templates = false;

			foreach ( $template_paths as $plugin_name => $template_path ) {
				$scanned_files[ $plugin_name ] = redux_scan_template_files( $template_path );
			}

			foreach ( $scanned_files as $plugin_name => $files ) {
				foreach ( $files as $file ) {
					if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
						$theme_file = get_stylesheet_directory() . '/' . $file;
					} elseif ( file_exists( get_stylesheet_directory() . '/woocommerce/' . $file ) ) {
						$theme_file = get_stylesheet_directory() . '/woocommerce/' . $file;
					} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
						$theme_file = get_template_directory() . '/' . $file;
					} elseif( file_exists( get_template_directory() . '/woocommerce/' . $file ) ) {
						$theme_file = get_template_directory() . '/woocommerce/' . $file;
					} else {
						$theme_file = false;
					}

					if ( $theme_file ) {
						$core_version  = redux_get_file_version( ReduxFramework::$_dir . 'templates/panel' . $file );
						$theme_version = redux_get_file_version( $theme_file );

						if ( $core_version && ( empty( $theme_version ) || version_compare( $theme_version, $core_version, '<' ) ) ) {
							if ( ! $outdated_templates ) {
								$outdated_templates = true;
							}
							$found_files[ $plugin_name ][] = sprintf( __( '<code>%s</code> version <strong style="color:red">%s</strong> is out of date. The core version is %s', 'redux-framework' ), str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ), $theme_version ? $theme_version : '-', $core_version );
						} else {
							$found_files[ $plugin_name ][] = sprintf( '<code>%s</code>', str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ) );
						}
					}
				}
			}

			if ( $found_files ) {
				foreach ( $found_files as $plugin_name => $found_plugin_files ) {
					?>
					<tr>
						<td data-export-label="Overrides"><?php _e( 'Overrides', 'redux-framework' ); ?> (<?php echo $plugin_name; ?>):</td>
						<td class="help">&nbsp;</td>
						<td><?php echo implode( ', <br/>', $found_plugin_files ); ?></td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td data-export-label="Overrides"><?php _e( 'Overrides', 'redux-framework' ); ?>:</td>
					<td class="help">&nbsp;</td>
					<td>&ndash;</td>
				</tr>
				<?php
			}

			if ( true === $outdated_templates ) {
				?>
				<tr>
					<td>&nbsp;</td>
					<td class="help">&nbsp;</td>
					<td><a href="http://speakinginbytes.com/2014/02/woocommerce-2-1-outdated-templates/" target="_blank"><?php _e( 'Learn how to update outdated templates', 'redux-framework' ) ?></a></td>
				</tr>
				<?php
			}
		?>
	</tbody>
</table>

<script type="text/javascript">

	jQuery( 'a.redux-hint-qtip' ).click( function() {
		return false;
	});

	jQuery( 'a.debug-report' ).click( function() {

		var report = '';

		jQuery( '#status thead, #status tbody' ).each(function(){

			if ( jQuery( this ).is('thead') ) {

				var label = jQuery( this ).find( 'th:eq(0)' ).data( 'export-label' ) || jQuery( this ).text();
				report = report + "\n### " + jQuery.trim( label ) + " ###\n\n";

			} else {

				jQuery('tr', jQuery( this ) ).each(function(){

					var label       = jQuery( this ).find( 'td:eq(0)' ).data( 'export-label' ) || jQuery( this ).find( 'td:eq(0)' ).text();
					var the_name    = jQuery.trim( label ).replace( /(<([^>]+)>)/ig, '' ); // Remove HTML
					var the_value   = jQuery.trim( jQuery( this ).find( 'td:eq(2)' ).text() );
					var value_array = the_value.split( ', ' );

					if ( value_array.length > 1 ) {

						// If value have a list of plugins ','
						// Split to add new line
						var output = '';
						var temp_line ='';
						jQuery.each( value_array, function( key, line ){
							temp_line = temp_line + line + '\n';
						});

						the_value = temp_line;
					}

					report = report + '' + the_name + ': ' + the_value + "\n";
				});

			}
		});

		try {
			jQuery( "#debug-report" ).slideDown();
			jQuery( "#debug-report textarea" ).val( report ).focus().select();
			jQuery( this ).fadeOut();
			return false;
		} catch( e ){
			console.log( e );
		}

		return false;
	});

	jQuery( document ).ready( function ( $ ) {
		$( '#copy-for-support' ).tipTip({
			'attribute':  'data-tip',
			'activation': 'click',
			'fadeIn':     50,
			'fadeOut':    50,
			'delay':      0
		});

		$( 'body' ).on( 'copy', '#copy-for-support', function ( e ) {
			e.clipboardData.clearData();
			e.clipboardData.setData( 'text/plain', $( '#debug-report textarea' ).val() );
			e.preventDefault();
		});

	});

</script>
