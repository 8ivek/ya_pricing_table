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
// 1st Method - Declaring $wpdb as global and using it to execute an SQL query statement that returns a PHP object
global $wpdb;
$results_pricing_table = $wpdb->get_results("SELECT pt.*, t.template_name FROM {$wpdb->prefix}yapt_pricing_tables pt INNER JOIN {$wpdb->prefix}yapt_templates t WHERE pt.template_id = t.id", ARRAY_A);
print_r($results_pricing_table);
?>
<div id="wrap">
    <form method="post" action="options.php">
        <?php
        settings_fields( 'yapt-save-settings' );
        do_settings_sections( 'yapt-save-settings' );
        submit_button();
        ?>
    </form>
</div>