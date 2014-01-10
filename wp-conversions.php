<?php /**
 * Plugin Name: WP Conversions
 * Plugin URI: https://github.com/joeyd/WP-Conversions
 * Description: Allows for creating redirects with conversion code loading prior to the redirect.
 * Version: 1.0
 * Author: Joey Durham
 * Author URI: http://www.ultraweaver.com/
 * License: GPL2
 */

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

// add the custom post type
add_action('init', 'nolo_reg_conversion_cpt');
function nolo_reg_conversion_cpt() {
    register_post_type('wp-conversion', array(
        'label' => 'WP Conversions',
        'description' => '',
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'hierarchical' => false,
        'rewrite' => array('slug' => 'wp-conversion', 'with_front' => true),
        'query_var' => true,
        'supports' => array('title'),
        'labels' => array (
            'name' => 'WP Conversions',
            'singular_name' => 'Conversion',
            'menu_name' => 'WP Conversions',
            'add_new' => 'Add Conversion',
            'add_new_item' => 'Add New Conversion',
            'edit' => 'Edit',
            'edit_item' => 'Edit Conversion',
            'new_item' => 'New Conversion',
            'view' => 'View Conversion',
            'view_item' => 'View Conversion',
            'search_items' => 'Search WP Conversions',
            'not_found' => 'No WP Conversions Found',
            'not_found_in_trash' => 'No WP Conversions Found in Trash',
            'parent' => 'Parent Conversion',
        )
    ));
}

// add custom fields for meta box
$prefix = 'nolo_';
$meta_box = array(
    'id' => 'nolo-conversion-meta-box',
    'title' => 'Conversion Tracking Details',
    'page' => 'wp-conversion',
    'context' => 'normal',
    'priority' => 'high',
    'fields' => array(
         array(
            'name' => 'Conversion code for &lt;head&gt;',
            'desc' => 'Paste your code here to have it render inside the &lt;head&gt;&lt;/head&gt; tags.',
            'id' => $prefix . 'conversion_code_head',
            'type' => 'textarea',
            'std' => ''
        ),
        array(
            'name' => 'Conversion code for &lt;body&gt;',
            'desc' => 'Paste your code here to have it render inside the &lt;body&gt;&lt;/body&gt; tags.',
            'id' => $prefix . 'conversion_code',
            'type' => 'textarea',
            'std' => ''
        ),
        array(
            'name' => 'Redirect URL',
            'desc' => 'Enter the full URL to redirect to. (http://example.com/example-page)',
            'id' => $prefix . 'conversion_redirect_url',
            'type' => 'text',
            'std' => ''
        )
    )
);

// add conversion details meta box
add_action('admin_menu', 'nolo_add_metabox');
function nolo_add_metabox() {
    global $meta_box;
    add_meta_box($meta_box['id'], $meta_box['title'], 'nolo_show_box', $meta_box['page'], $meta_box['context'], $meta_box['priority']);
}

//display conversion details in meta box
function nolo_show_box() {
    global $meta_box, $post;

    echo '<input type="hidden" name="nolo_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
    echo '<table class="form-table">';

    foreach ($meta_box['fields'] as $field) {
        $meta = get_post_meta($post->ID, $field['id'], true);
        echo '<tr>',
                '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
                '<td>';
        switch ($field['type']) {
            case 'text':
                echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '<br />', $field['desc'];
                break;
            case 'textarea':
                echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="8" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', '<br />', $field['desc'];
                break;
            case 'select':
                echo '<select name="', $field['id'], '" id="', $field['id'], '">';
                foreach ($field['options'] as $option) {
                    echo '<option ', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                }
                echo '</select>';
                break;
            case 'radio':
                foreach ($field['options'] as $option) {
                    echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
                }
                break;
            case 'checkbox':
                echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
                break;
        }
        echo     '</td><td>',
            '</td></tr>';
    }
    echo '<tr><th>Copy Conversion URL</th><td>' .  get_post_permalink() . '</td><td></td></tr></table>';
}

// save conversion details
add_action('save_post', 'nolo_save_data');
function nolo_save_data($post_id) {
    global $meta_box;
    if (!wp_verify_nonce($_POST['nolo_meta_box_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    foreach ($meta_box['fields'] as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }
}

// add custom template path to point to the plugin folder
add_filter( 'single_template', 'get_wp_conversion_template' );
if( !function_exists('get_wp_conversion_template') ) {
    function get_wp_conversion_template($single_template) {
        global $wp_query, $post;
        if ($post->post_type == 'wp-conversion'){
            $single_template = plugin_dir_path(__FILE__) . 'wp-conversion-template.php';
        }
        return $single_template;
    }
}

// add conversion url (permalink) to admin list
add_filter('manage_wp-conversion_posts_columns' , 'add_conv_url_columns');
function add_conv_url_columns($columns) {
    $column_meta = array( 'conurl' => 'Conversion URL' );
    $columns = array_slice( $columns, 0, 2, true ) + $column_meta + array_slice( $columns, 2, NULL, true );
    return $columns;
}

//display the conversion url in admin list
add_action( 'manage_wp-conversion_posts_custom_column', 'display_converion_url' );
function display_converion_url(  ) {
echo get_post_permalink();
}

// to come later
//if(defined('WP_UNINSTALL_PLUGIN') ){ }
