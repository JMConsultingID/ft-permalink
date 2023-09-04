<?php
// Menambahkan menu di admin
add_action('admin_menu', 'ft_permalink_menu');

function ft_permalink_menu() {
    add_menu_page('FT Permalink Settings', 'FT Permalink', 'manage_options', 'ft-permalink', 'ft_permalink_settings_page', 'dashicons-admin-generic', 6);
}

// Fungsi untuk menampilkan halaman settings
function ft_permalink_settings_page() {
    ?>
    <div class="wrap">
        <h2>FT Permalink Settings</h2>
        <p>This is the settings page for the FT Permalink plugin. You can change the permalink structure for custom post types here.<br/> 
        You may need to flush the rewrite rules in WordPress. You can do this by going back to Settings > Permalinks and then clicking Save Changes without changing anything.</p> <!-- Deskripsi yang ditambahkan -->
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
    add_settings_field('ft_enable_category', 'Enable Category', 'ft_enable_category_callback', 'ft-permalink', 'ft_permalink_general_section');
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
    $post_types = get_post_types(['_builtin' => false], 'objects');
    echo '<select name="ft_permalink_settings[select_cpt]">';
    foreach ($post_types as $post_type) {
        echo '<option value="'.$post_type->name.'" '.selected($value, $post_type->name, false).'>'.$post_type->label.'</option>';
    }
    echo '</select>';
}

function ft_enable_category_callback() {
    $options = get_option('ft_permalink_settings');
    $value = isset($options['enable_category']) ? $options['enable_category'] : 'disable';
    echo '<select name="ft_permalink_settings[enable_category]">
            <option value="enable" '.selected($value, 'enable', false).'>Enable</option>
            <option value="disable" '.selected($value, 'disable', false).'>Disable</option>
          </select>';
}
?>
