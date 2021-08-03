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
?>
<div class="wrap">
    <h2>YA price tables</h2>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post">
                        <?php
                        $this->price_table->display();
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>