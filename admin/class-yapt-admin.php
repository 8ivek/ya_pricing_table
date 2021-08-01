<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://bivek.ca
 * @since      1.0.0
 *
 * @package    Yapt
 * @subpackage Yapt/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Yapt
 * @subpackage Yapt/admin
 * @author     bvk <bivek_j@yahoo.com>
 */
class Yapt_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * @var yapt_list
     */
    public yapt_list $price_table;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        // include WP_List_Table and yapt_list here coz we need to create its obj when loading add_menu_page()
        if (!class_exists('WP_List_Table')) {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }

        // Include EPT post list table
        require_once(plugin_dir_path(__FILE__) . '../includes/yapt_list.php');
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Yapt_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Yapt_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/yapt-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Yapt_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Yapt_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/yapt-admin.js', array('jquery'), $this->version, false);
    }


    /**
     * This function introduces the plugin options into the Main menu.
     */
    public function setupSettingsMenu(): void
    {
        //Add the menu item to the Main menu
        $hook = add_menu_page(
            'YA pricing tables',                      // Page title: The title to be displayed in the browser window for this page.
            'YA Pricing Table',                              // Menu title: The text to be used for the menu.
            'manage_options',                           // Capability: The capability required for this menu to be displayed to the user.
            'yapt_admin',                            // Menu slug: The slug name to refer to this menu by. Should be unique for this menu page.
            [$this, 'renderSettingsPageContent'],  // Callback: The name of the function to call when rendering this menu's page
            'dashicons-smiley',                         // Icon
            85                                          // Position: The position in the menu order this item should appear.
        );
        add_submenu_page('yapt_admin', 'Add new pricing table', 'Add New', 'manage_options', 'yapt_admin_add_page', [$this, 'renderAddPageContent']);

        add_action( "load-$hook", [ $this, 'screen_option' ] );
    }

    public function screen_option()
    {
        $option = 'per_page';
        $args = [
            'label' => 'Price Table',
            'default' => 10,
            'option' => 'tables_per_page'
        ];
        add_screen_option($option, $args);
        $this->price_table = new yapt_list();
    }

    /**
     * Save pricing table data
     * post submission of add/update pricing table
     * @throws Exception
     */
    public function addPricingTableData()
    {
        // print_r($_POST);die();
        // echo "Add pricing table data";
        global $wpdb;
        $posted_fields = $_POST['fields'] ?? [];
        if (empty($posted_fields)) {
            die('missing mandatory fields');
        }

        $template_id = $_POST['template'] ?? 0;

        $now = new DateTime('now', new DateTimeZone('UTC'));
        $created_at = $updated_at = $now->format('Y-m-d H:i:s');

        $pricing_table_title = $_POST['pricing_table_title'] ?? '';

        // insert into yapt_pricing_tables
        $wpdb->insert($wpdb->prefix . 'yapt_pricing_tables', ['pt_title' => $pricing_table_title, 'template_id' => $template_id, 'created_at' => $created_at, 'updated_at' => $updated_at]);

        $table_id = $wpdb->insert_id;

        foreach ($posted_fields as $column_data) {
            $column_title = $column_data['column_title'];
            $column_price = $column_data['column_price'];
            $col_button_face_text = $column_data['col_button_face_text'];
            $col_button_url = $column_data['col_button_url'];

            // insert into yapt_pricing_tables
            $wpdb->insert($wpdb->prefix . 'yapt_columns', ['column_title' => $column_title, 'table_id' => $table_id, 'price_text' => $column_price, 'ctoa_btn_text' => $col_button_face_text, 'ctoa_btn_link' => $col_button_url, 'created_at' => $created_at, 'updated_at' => $updated_at]);

            $column_id = $wpdb->insert_id;

            foreach ($column_data['feature_text'] as $key => $ft) {
                $isset = 0;
                if (isset($column_data['feature_checked'][$key])) {
                    $isset = 1;
                }
                $wpdb->insert($wpdb->prefix . 'yapt_features', ['column_id' => $column_id, 'feature_text' => $ft, 'is_set' => $isset, 'created_at' => $created_at, 'updated_at' => $updated_at]);
            }
        }
        echo wp_redirect(admin_url('admin.php?page=yapt_admin'));
    }

    public function renderSettingsPageContent(string $activeTab = ''): void
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/yapt-admin-display.php';
    }

    public function renderAddPageContent(string $activeTab = ''): void
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/yapt-admin-add-page.php';
    }
}