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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Redirecting...</title>
<?php if (isset($conversion_code)) {
    echo $conversion_code;
}
 ?>
 <?php if (isset($redirect_url)) { ?>
    <META http-equiv="refresh" content="3;URL=<?php echo $redirect_url; ?>">
 <?php } ?>
 <meta name="robots" content="noindex">
</head>
<body>
Redirecting...
<script>
<!--
<?php if (isset($redirect_url)) { ?>
    window.location = "<?php echo $redirect_url ?>";
<?php } ?>
//-->
</script>

</body>
</html>
