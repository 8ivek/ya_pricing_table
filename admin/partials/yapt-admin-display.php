<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://bivek.ca
 * @since      1.0.0
 *
 * @package    Yapt
 * @subpackage Yapt/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
// global $wpdb;
// $results_pricing_table = $wpdb->get_results("SELECT pt.*, t.template_name FROM {$wpdb->prefix}yapt_pricing_tables pt INNER JOIN {$wpdb->prefix}yapt_templates t WHERE pt.template_id = t.id", ARRAY_A);
// print_r($results_pricing_table);
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
// Include EPT post list table
include(plugin_dir_path(__FILE__) . '../../includes/yapt_list.php');

$list_table = new yapt_list();

?>
<div class="wrap">
    <h2>YA Price Tables</h2>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post">
                        <?php
                        $list_table->prepare_items();
                        $list_table->display();
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>