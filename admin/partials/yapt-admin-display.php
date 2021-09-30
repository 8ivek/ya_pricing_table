<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/8ivek/yapt
 * @since      1.0.0
 *
 * @package    Yapt
 * @subpackage Yapt/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2>YA price tables <a href="admin.php?page=yapt_admin_add_page" class="page-title-action"><?php _e('Add New', 'ya-pricing-table'); ?></a></h2>
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