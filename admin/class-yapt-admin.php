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
        add_menu_page(
            'YA pricing tables',                      // Page title: The title to be displayed in the browser window for this page.
            'YA Pricing Table',                              // Menu title: The text to be used for the menu.
            'manage_options',                           // Capability: The capability required for this menu to be displayed to the user.
            'yapt-admin',                            // Menu slug: The slug name to refer to this menu by. Should be unique for this menu page.
            [$this, 'renderSettingsPageContent'],  // Callback: The name of the function to call when rendering this menu's page
            'dashicons-smiley',                         // Icon
            85                                          // Position: The position in the menu order this item should appear.
        );
        add_submenu_page('yapt-admin', 'Add new pricing table', 'Add New', 'manage_options', 'yapta-admin-add-page', [$this, 'renderAddPageContent']);
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