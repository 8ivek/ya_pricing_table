<?php

/**
 * Provide a admin area view for adding new pricing table
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/8ivek/yapt
 * @since      1.0.0
 *
 * @package    Yapt
 * @subpackage Yapt/admin/partials
 */

global $wpdb;
$results_templates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}yapt_templates", ARRAY_A);
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Add pricing table</h1>
    <div id="poststuff">
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <div class="yapt_add_title">
                <input class="yapt_pricing_table_title" type="text" name="pricing_table_title" value=""
                       placeholder="Add pricing table title" required="required"/>
            </div>
            <div class="yapt_wrap">
                <!-- Tab links -->
                <div class="tab">
                    <button class="tablinks" onclick="yapt_admin_tab(event, 'Add_table')" id="defaultOpen">
                        <span class="dashicons dashicons-editor-table"></span>
                        Add Pricing Table
                    </button>
                    <button class="tablinks" onclick="yapt_admin_tab(event, 'Theme')">
                        <span class="dashicons dashicons-format-image"></span>
                        Select theme
                    </button>
                    <button class="tablinks" onclick="yapt_admin_tab(event, 'custom_styles')">
                        <span class="dashicons dashicons-admin-customizer"></span>
                        Styles
                    </button>
                </div>

                <!-- Tab content -->
                <div id="Add_table" class="tabcontent">
                    <table width="100%">
                        <tr>
                            <td>
                                <h3>Click on 'add column' to add a new price table</h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a class="yapt_add_column" href="javascript:;" onclick="add_column()">
                                    <span class="dashicons dashicons-plus"></span>
                                    add column
                                </a>
                                <input type="hidden" name="column_count" id="column_count" value="0"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div id="ypt_columns" class="ypt_columns_wrap">
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="Theme" class="tabcontent theme">
                    <h3>Select theme</h3>
                    <div class="yapt_template_list">
                        <?php
                        foreach ($results_templates as $template) {
                            ?>
                            <div class="yapt_template_item">
                                <label>
                                    <input type="radio" name="template_id" value="<?php echo esc_html($template['id']); ?>" checked="checked"/>
                                    <img src="<?php echo YAPT_PLUGIN_URL . 'templates/' . esc_html($template['template_name']) . '/' . esc_html($template['image']); ?>"/>
                                </label>
                            </div>
                            <?php
                        }
                        ?>
                    </div><!-- .yapt_template_list ends -->
                </div><!-- #Theme .tabcontent ends -->

                <div id="custom_styles" class="tabcontent">
                    <h3>Custom styles</h3>
                    <textarea name="custom_styles">/* styles here */</textarea>
                </div><!-- #Styles .tabcontent ends -->
            </div>
            <!--.yapt_wrap ends -->

            <div class="yapt_save_options">
                <input type="hidden" name="action" value="yapt_admin_save"/>
                <?php
                wp_nonce_field("yapt_nonce");
                submit_button();
                ?>
            </div>
        </form>
        <br class="clear">
    </div>
</div>

<script type="text/javascript">
    let computed_feature_id;

    function add_feature(column_id) {
        computed_feature_id = parseInt(jQuery("#column" + column_id + "_feature_count").val());
        //console.log(computed_feature_id);
        //console.log('add feature clicked for table '+ column_id);
        let new_feature_value = "<div id='column" + column_id + "_feature" + computed_feature_id +
            "'><label class='yapt_label_con'><input type='checkbox' name='fields[" + column_id + "][feature_checked][" +
            computed_feature_id +
            "]' value='1' /> <span class='checkmark'></span></label> <input type='text' required='required' name='fields[" + column_id +
            "][feature_text][" + computed_feature_id +
            "]' placeholder='Feature text content ...' value='' /> <a title='Delete feature' class='delete_feature' href='javascript:;' onclick='delete_feature(" +
            column_id + ", " + computed_feature_id + ")'><span class='dashicons dashicons-dismiss'></span></a></div>";
        jQuery("#column" + column_id + "_features").append(new_feature_value);
        computed_feature_id += 1;
        jQuery("#column" + column_id + "_feature_count").val(computed_feature_id);
    }

    function delete_feature(column_id, feature_id) {
        console.log('delete feature ' + feature_id + ' from table ' + column_id);
        jQuery("#column" + column_id + "_feature" + feature_id).remove();
    }

    function delete_column(column_id) {
        console.log('delete column ' + column_id);
        jQuery('#tbl_column' + column_id).remove();
    }

    function add_column() {
        //console.log('add column called');
        let computed_column_id = parseInt(jQuery("#column_count").val());
        //console.log('new column id: ' + computed_column_id);

        let new_column_value = "<div class='yapt_table_column' id='tbl_column" + computed_column_id +
            "'><div class='yapt_table_row'><label>Name</label><input type='text' required='required' name='fields[" + computed_column_id +
            "][column_title]'/></div><div class='yapt_table_row'><label>Short description</label><textarea class='short_description' name='fields[" + computed_column_id +
            "][description]'></textarea></div><div class='yapt_table_row'><label>Pricing</label><input type='text' name='fields[" +
            computed_column_id +
            "][column_price]'/></div><div class='yapt_table_row'><label>Button face text</label><input type='text' name='fields[" +
            computed_column_id +
            "][column_button_face_text]'/></div><div class='yapt_table_row'><label>Button url</label><input type='text' name='fields[" +
            computed_column_id +
            "][column_button_url]'/></div><div class='yapt_table_row yapt_table_row_features_head'><span class='features_title'>Features</span><a class='add_feature' href='javascript:;' onclick='add_feature(" +
            computed_column_id +
            ")'><span class='dashicons dashicons-plus-alt'></span>add feature</a></div><input type='hidden' name='column" +
            computed_column_id +
            "_feature_count' id='column" + computed_column_id +
            "_feature_count' value='0' /><div class='yapt_table_row yapt_table_row_features' id='column" +
            computed_column_id +
            "_features' class='feature_column_container'></div><div class='yapt_table_row clearfix'><div class='switch_featured'> <label class='switch'><input type='radio' name='highlighted' value='" + computed_column_id + "' /><span class='slider round'></span></label> Highlight</div><a title='Delete column' class='delete_column' href='javascript:;' onclick='delete_column(" +
            computed_column_id + ")'><span class='dashicons dashicons-trash'></span></a></div></div>";
        jQuery("#ypt_columns").append(new_column_value);

        add_feature(computed_column_id); // everytime we call add_column we will be adding 3 empty features to the column.
        computed_column_id += 1;
        jQuery("#column_count").val(computed_column_id);
    }

    // add first column
    add_column();
</script>