<?php
/*
Plugin Name:  WP-Short-URLs
Plugin URI:   http://www.jlsoft.de/
Description:  Shortens URLs by safely removing "http://example.com/" part from all urls.
Version:      0.1
License:      GPLv2 or later
Author:       Jan Loeffler
Author URI:   http://www.jlsoft.de/
Author Email: mail@jlsoft.de
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Online: http://www.gnu.org/licenses/gpl.txt
*/

require_once 'lib/class-html-analyzer.php';

$wp_shorten_urls_run = false;

function wp_shorten_urls_start() {
    global $wp_shorten_urls_run;

    if ( !$wp_shorten_urls_run ) {
        $wp_shorten_urls_run = true;

        if ( !is_feed() && !is_robots() ) {
            ob_start( 'shorten_urls_buffer' );
        }
    }
}

function shorten_urls_buffer( $html ) {
    return new HTML_Analyzer( $html );
}

// Prevents errors when this file is accessed directly
if ( function_exists( 'is_admin' ) ) {
    if ( !is_admin() ) {
        add_action( 'template_redirect', 'wp_shorten_urls_start', -1 );

        // In case above fails (it does sometimes.. ??)
        add_action( 'get_header', 'wp_shorten_urls_start' );
    }
}
?>