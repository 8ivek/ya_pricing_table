<?php

/**
 * Edit form
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
<div class="wrap">
    <h2>Edit pricing table</h2>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">

                </form>
            </div>
        </div>
        <br class="clear">
    </div>
</div>