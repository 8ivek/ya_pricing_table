<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://bivek.ca
 * @since      1.0.0
 *
 * @package    Yapt
 * @subpackage Yapt/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Yapt
 * @subpackage Yapt/public
 * @author     bvk <bivek_j@yahoo.com>
 */
class Yapt_Public
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
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_shortcode('yapt', [$this, 'yapt_shortcode_callback']);
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/yapt-public.css', array(), $this->version, 'all');

        if (!wp_style_is('fontawesome', 'enqueued')) {
            wp_register_style('fontawesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', false, '5.15.4');
            wp_enqueue_style('fontawesome');
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/yapt-public.js', array('jquery'), $this->version, false);
    }

    /**
     * show html table data
     * @param array $atts
     * @return string
     */
    public function yapt_shortcode_callback(array $atts = [])
    {
        // set up default parameters
        extract(shortcode_atts([
            'ptid' => '0'
        ], $atts));

        // echo "yeta pugyo" . $ptid;
        $db_data_obj = new db_data();
        $item_detail = $db_data_obj->getData($ptid);
        // print_r($item_detail);

        if (!is_array($item_detail) || count($item_detail) < 1) {
            return "<p>not available</p>";
        }

        $pt_column_content = $this->readHtmlFile($item_detail['template_name'] ?? '', $item_detail['html'] ?? '');

        $pt_html = "<link rel='stylesheet' href='" . YAPT_PLUGIN_URL . "templates/" . $item_detail['template_name'] . "/" . $item_detail['style'] . "' />
        <div class='yapt_pricing_table'>";

        $col_html = '';

        foreach ($item_detail['columns'] as $col) {

            $highlighted = '';
            if ($col['highlighted'] == 1) {
                $highlighted = 'highlighted';
            }

            //feature task
            $feature_list = '';
            foreach ($col['features'] as $feats) {
                $feature_class = 'unchecked';
                if ($feats['is_set'] == '1') {
                    $feature_class = 'checked';
                }
                $feature_list .= "<li class='" . $feature_class . "'>" . $feats['feature_text'] . "</li>";
            }

            $temp_col = str_replace('##is_highlighted##', $highlighted, $pt_column_content);
            $temp_col = str_replace('##col_title##', $col['column_title'], $temp_col);
            $temp_col = str_replace('##col_price##', $col['price_text'], $temp_col);
            $temp_col = str_replace('##col_cta_btn_lnk##', $col['ctoa_btn_link'], $temp_col);
            $temp_col = str_replace('##col_cta_btn_text##', $col['ctoa_btn_text'], $temp_col);
            $temp_col = str_replace('##col_feature_list##', $feature_list, $temp_col);

            $col_html .= $temp_col;
        }

        $pt_html .= $col_html . "</div>";

        return $pt_html;
    }

    /**
     * Read html file
     * @param $folder_name
     * @param $html_file
     * @return false|string
     */
    public function readHtmlFile($folder_name, $html_file)
    {
        if (empty($folder_name)) {
            $folder_name = 'default';
        }
        if (empty($html_file)) {
            $html_file = 'default';
        }
        return file_get_contents(YAPT_PLUGIN_DIR_PATH . '/templates/' . $folder_name . '/' . $html_file, true);
    }
}