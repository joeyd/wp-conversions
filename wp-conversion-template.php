<?php

/*  Copyright 2014 Joey Durham  (email : joey@ultraweaver.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// the custom post type template that handles the redirects.
global $post;
$conversion_code = get_post_meta($post->ID,'nolo_conversion_code', true);
$redirect_url = get_post_meta($post->ID,'nolo_conversion_redirect_url', true);

if ($conversion_code != '') {
    echo $conversion_code;
}

if ($redirect_url != '') {
    header("Location: $redirect_url");
}

?>



