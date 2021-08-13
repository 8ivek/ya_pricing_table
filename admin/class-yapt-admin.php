<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/8ivek/yapt
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
        require_once(YAPT_PLUGIN_DIR_PATH . 'includes/yapt_list.php');

        require_once(YAPT_PLUGIN_DIR_PATH . 'Type/Type.php');
        require_once(YAPT_PLUGIN_DIR_PATH . 'Type/PriceTable.php');
        require_once(YAPT_PLUGIN_DIR_PATH . 'Type/Column.php');
        require_once(YAPT_PLUGIN_DIR_PATH . 'Type/Feature.php');
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

        //wp_enqueue_script($this->plugin_name, "", ['jquery'], $this->version, false);

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

        add_action("load-$hook", [$this, 'screen_option']);
    }

    public function screen_option()
    {
        $this->price_table = new yapt_list();

        if (!empty($_GET['action']) && $_GET['action'] === 'edit') {
            // show edit form
            $price_table_id = 0;
            if (!empty($_GET['price_table'])) {
                $price_table_id = (int)sanitize_text_field($_GET['price_table']);
            }

            $this->price_table->prepare_item($price_table_id);
        } else {
            $option = 'per_page';
            $args = [
                'label' => 'Price Table',
                'default' => 10,
                'option' => 'tables_per_page'
            ];
            add_screen_option($option, $args);
            // show wp_list_table
            $this->price_table->prepare_items();
        }
    }

    /**
     * Save pricing table data
     * @throws Exception
     */
    public function savePricingTableData()
    {
        // print_r($_POST); die();
        // echo "Update pricing table data";

        try {
            $price_table_obj = PriceTable::createFromArray($_POST);
        } catch (Exception $e) {
            die($e->getMessage());
        }

        global $wpdb;
        if (!$price_table_obj instanceof PriceTable) {
            die('missing mandatory fields');
        }

        $date_obj = new DateTime('now', new DateTimeZone('UTC'));
        $now = $date_obj->format('Y-m-d H:i:s');

        // print_r($price_table_obj);die();

        if ($price_table_obj->price_table_id > 0) {
            // update into yapt_pricing_tables
            $wpdb->update($wpdb->prefix . 'yapt_pricing_tables', ['pt_title' => $price_table_obj->pricing_table_title, 'custom_styles' => $price_table_obj->custom_styles, 'template_id' => $price_table_obj->template_id, 'created_at' => $now, 'updated_at' => $now], ['id' => $price_table_obj->price_table_id]);
        } else {
            // insert into yapt_pricing_tables
            $wpdb->insert($wpdb->prefix . 'yapt_pricing_tables', ['pt_title' => $price_table_obj->pricing_table_title, 'custom_styles' => $price_table_obj->custom_styles, 'template_id' => $price_table_obj->template_id, 'created_at' => $now, 'updated_at' => $now]);
            $price_table_obj->price_table_id = $wpdb->insert_id;
        }

        $column_ids = [];
        foreach ($price_table_obj->columns as $column) {
            if (!$column instanceof Column) {
                die('$column must be of type Type/Column.');
            }

            if (empty($column->column_id)) {
                // insert into yapt_columns
                $wpdb->insert($wpdb->prefix . 'yapt_columns', ['column_title' => $column->column_title, 'description' => $column->description, 'highlighted' => $column->highlighted, 'table_id' => $price_table_obj->price_table_id, 'price_text' => $column->column_price, 'ctoa_btn_text' => $column->column_button_face_text, 'ctoa_btn_link' => $column->column_button_url, 'created_at' => $now, 'updated_at' => $now]);
                $column->column_id = $wpdb->insert_id;
            } else {
                // update yapt_columns
                $wpdb->update($wpdb->prefix . 'yapt_columns', ['column_title' => $column->column_title, 'description' => $column->description, 'highlighted' => $column->highlighted, 'table_id' => $price_table_obj->price_table_id, 'price_text' => $column->column_price, 'ctoa_btn_text' => $column->column_button_face_text, 'ctoa_btn_link' => $column->column_button_url, 'created_at' => $now, 'updated_at' => $now], ['id' => $column->column_id]);
            }

            $feature_ids = [];
            foreach ($column->features as $feature) {
                if (!$feature instanceof Feature) {
                    die('$feature must be of type Type/Feature.');
                }

                if (empty($feature->fid)) {
                    $wpdb->insert($wpdb->prefix . 'yapt_features', ['column_id' => $column->column_id, 'feature_text' => $feature->feature_text, 'is_set' => $feature->feature_checked, 'created_at' => $now, 'updated_at' => $now]);
                    $feature->fid = $wpdb->insert_id;
                } else {
                    $wpdb->update($wpdb->prefix . 'yapt_features', ['column_id' => $column->column_id, 'feature_text' => $feature->feature_text, 'is_set' => $feature->feature_checked, 'updated_at' => $now], ['id' => $feature->fid]);
                }
                $feature_ids[] = $feature->fid;
            }//foreach feature_text ends

            if (is_array($feature_ids) && count($feature_ids) > 0) {
                $sql_delete_features = "DELETE FROM `" . $wpdb->prefix . "yapt_features` WHERE `column_id` = '" . $column->column_id . "' AND `id` NOT IN (" . implode(', ', $feature_ids) . ")";
            } else if (count($feature_ids) === 0) {
                $sql_delete_features = "DELETE FROM `" . $wpdb->prefix . "yapt_features` WHERE `column_id` = '" . $column->column_id . "'";
            }
            $wpdb->query($sql_delete_features);

            $column_ids[] = $column->column_id;
        }//foreach column ends

        $sql_delete_columns = "DELETE FROM `" . $wpdb->prefix . "yapt_columns` WHERE `table_id` = '" . $price_table_obj->price_table_id . "' AND `id` NOT IN (" . implode(', ', $column_ids) . ")";
        $wpdb->query($sql_delete_columns);
        echo wp_redirect(admin_url('admin.php?page=yapt_admin'));
    }

    public function renderSettingsPageContent(string $activeTab = ''): void
    {
        if (!empty($_GET['action']) && $_GET['action'] === 'edit') {
            // show edit form
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/yapt-admin-edit.php';
        } else {
            // show wp_list_table
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/yapt-admin-display.php';
        }
    }

    public function renderAddPageContent(string $activeTab = ''): void
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/yapt-admin-add-page.php';
    }
}