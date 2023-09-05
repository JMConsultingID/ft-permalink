<?php
// Menambahkan menu di admin
add_action('admin_menu', 'ft_permalink_menu');

function ft_permalink_menu() {
    add_menu_page('FT Permalink Settings', 'FT Permalink', 'manage_options', 'ft-permalink', 'ft_permalink_settings_page', 'dashicons-admin-generic', 22);
}

// Fungsi untuk menampilkan halaman settings
function ft_permalink_settings_page() {
    ?>
    <div class="wrap">
        <h2>FT Permalink Settings</h2>
        <p>This is the settings page for the FT Permalink plugin. You can change the permalink structure for custom post types here.<br/> 
        After making changes in the FT Permalink Plugin, do the Following:
        <ol>
            <li>Make sure to reactivate your plugin so that the rewrite rules are updated.</li>
            <li>You may need to flush the rewrite rules in WordPress. You can do this by going back to <strong>"Settings" > "Permalinks"</strong> page in your WordPress dashboard and click <strong>"Save Changes"</strong> to ensure the rewrite rules are updated.</li>
            <li>Enjoy Using this Plugin.</li>
        </ol>
        </p> <!-- Deskripsi yang ditambahkan -->
        <form method="post" action="options.php">
            <?php
            settings_fields('ft_permalink_settings_group');
            do_settings_sections('ft-permalink');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Mendaftarkan setting dan field
add_action('admin_init', 'ft_permalink_settings_init');

function ft_permalink_settings_init() {
    register_setting('ft_permalink_settings_group', 'ft_permalink_settings');

    add_settings_section('ft_permalink_general_section', 'General Settings', null, 'ft-permalink');

    add_settings_field('ft_enable_plugin', 'Enable Plugin', 'ft_enable_plugin_callback', 'ft-permalink', 'ft_permalink_general_section');
    add_settings_field('ft_select_cpt', 'Select Custom Post Type', 'ft_select_cpt_callback', 'ft-permalink', 'ft_permalink_general_section');
}

function ft_enable_plugin_callback() {
    $options = get_option('ft_permalink_settings');
    $value = isset($options['enable_plugin']) ? $options['enable_plugin'] : 'disable';
    echo '<select name="ft_permalink_settings[enable_plugin]">
            <option value="enable" '.selected($value, 'enable', false).'>Enable</option>
            <option value="disable" '.selected($value, 'disable', false).'>Disable</option>
          </select>';
}

function ft_select_cpt_callback() {
    $options = get_option('ft_permalink_settings');
    $value = isset($options['select_cpt']) ? $options['select_cpt'] : '';
    $post_types = get_post_types(array('public' => true), 'objects');
    echo '<select name="ft_permalink_settings[select_cpt]">';
    foreach ($post_types as $post_type) {
        echo '<option value="'.$post_type->name.'" '.selected($value, $post_type->name, false).'>'.$post_type->label.'</option>';
    }
    echo '</select>';
}

function is_ft_permalink_enabled() {
    $options = get_option('ft_permalink_settings');
    return isset($options['enable_plugin']) && $options['enable_plugin'] == 'enable';
}

//enable plugin
if (is_ft_permalink_enabled()) {
// Mengubah link post
add_filter('post_type_link', 'ft_custom_permalink_structure', 10, 2);
function ft_custom_permalink_structure($post_link, $post) {
    // Pastikan ini adalah custom post type yang Anda inginkan
    $options = get_option('ft_permalink_settings');
    if (isset($options['select_cpt']) && $post->post_type == $options['select_cpt']) {
        $terms = get_the_terms($post->ID, 'category');
        if ($terms) {
            return home_url($post->post_type . '/' . $terms[0]->slug . '/' . $post->post_name . '/');
        }
    }
    return $post_link;
}
// Menambahkan rewrite rules
add_action('init', 'ft_add_rewrite_rules');
function ft_add_rewrite_rules() {
    $options = get_option('ft_permalink_settings');
    if (isset($options['select_cpt'])) {
        add_rewrite_rule('^' . $options['select_cpt'] . '/([^/]+)/([^/]+)/?$', 'index.php?post_type=' . $options['select_cpt'] . '&name=$matches[2]', 'top');
    }
}

add_action('init', 'ft_modify_cpt_args');
function ft_modify_cpt_args() {
    $options = get_option('ft_permalink_settings');
    if (isset($options['select_cpt'])) {
        $post_type_object = get_post_type_object($options['select_cpt']);
        if ($post_type_object) {
            $post_type_object->has_archive = true;
            register_post_type($options['select_cpt'], $post_type_object);
        }
    }
}


// Mengubah link kategori
add_filter('term_link', 'ft_custom_category_permalink', 10, 3);
function ft_custom_category_permalink($url, $term, $taxonomy) {
    $options = get_option('ft_permalink_settings');
    if ($taxonomy == 'category' && isset($options['select_cpt'])) {
        return home_url($options['select_cpt'] . '/' . $term->slug . '/');
    }
    return $url;
}


// Menambahkan rewrite rules untuk kategori
add_action('init', 'ft_add_category_rewrite_rules');
function ft_add_category_rewrite_rules() {
    $options = get_option('ft_permalink_settings');
    if (isset($options['select_cpt'])) {
        add_rewrite_rule('^' . $options['select_cpt'] . '/([^/]+)/?$', 'index.php?category_name=$matches[1]', 'top');
    }
}

add_action('pre_get_posts', 'ft_modify_category_query');
function ft_modify_category_query($query) {
    $options = get_option('ft_permalink_settings');
    if (!is_admin() && $query->is_category() && $query->is_main_query() && isset($options['select_cpt'])) {
        $query->set('post_type', $options['select_cpt']);
    }
}

}
// end enable plugin

// Flush rewrite rules saat plugin diaktifkan
register_activation_hook(__FILE__, 'ft_flush_rewrite_rules');
function ft_flush_rewrite_rules() {
    ft_add_rewrite_rules();
    flush_rewrite_rules();
}

// Flush rewrite rules saat plugin dinonaktifkan
register_deactivation_hook(__FILE__, 'ft_flush_rewrite_rules_deactivate');
function ft_flush_rewrite_rules_deactivate() {
    flush_rewrite_rules();
}