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

global $wpdb;
$results_templates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}yapt_templates", ARRAY_A);
?>
<div class="wrap">
    <h2>Edit pricing table</h2>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <table>
                        <tr>
                            <td><strong>Select template</strong></td>
                            <td><input type="hidden" name="pt_id" value="<?php echo $this->price_table->item['id'];?>" /><select name="template" required="required" />
                                    <option value="0">select a template</option>
                                    <?php
                                    foreach ($results_templates as $template) {
                                        ?>
                                        <option value="<?php echo $template['id'] ?>"
                                            <?php
                                            if ($template['id'] === $this->price_table->item['template_id']) {
                                                echo "selected='selected'";
                                            }
                                            ?>><?php echo $template['template_name']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td><strong>Pricing table title</strong></td>
                            <td><input type="text" name="pricing_table_title"
                                       value="<?php echo $this->price_table->item['pt_title']; ?>"
                                       placeholder="first/main pricing table..." required="required"/></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <a href="javascript:;" onclick="add_column()">add column</a>
                                <input type="hidden" name="column_count" id="column_count" value="0"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table id="ypt_columns">

                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="hidden" name="action" value="yapt_admin_update" />
                                <?php
                                wp_nonce_field("yapt_nonce");
                                submit_button();
                                ?>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <br class="clear">
    </div>
</div>

<script type="text/javascript">
    let computed_feature_id;
    let computed_column_id;

    function add_feature(column_id) {
        computed_feature_id = parseInt(jQuery("#column" + column_id + "_feature_count").val());
        //console.log(computed_feature_id);
        //console.log('add feature clicked for table '+ column_id);
        let new_feature_value = "<div id='column" + column_id + "_feature" + computed_feature_id + "'> <input type='hidden' name='fields[" + column_id + "][fid][" + computed_feature_id + "]' /> <input type='checkbox' name='fields[" + column_id + "][feature_checked][" + computed_feature_id + "]' value='1' /> <input type='text' name='fields[" + column_id + "][feature_text][" + computed_feature_id + "]' placeholder='Feature text content ...' value='' /> <a href='javascript:;' onclick='delete_feature(" + column_id + ", " + computed_feature_id + ")'>delete</a></div>";
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
        computed_column_id = parseInt(jQuery("#column_count").val());
        //console.log('new column id: ' + computed_column_id);

        let new_column_value = "<tbody id='tbl_column" + computed_column_id + "'><tr><td>Name</td><td><input type='hidden' name='fields[" + computed_column_id + "][column_id]' /><input type='text' name='fields[" + computed_column_id + "][column_title]'/></td></tr><tr><td>Pricing</td><td><input type='text' name='fields[" + computed_column_id + "][column_price]'/></td></tr><tr><td>Button face text</td><td><input type='text' name='fields[" + computed_column_id + "][col_button_face_text]'/></td></tr><tr><td>Button url</td><td><input type='text' name='fields[" + computed_column_id + "][col_button_url]'/></td></tr><tr><td valign='top'>Features</td><td><a href='javascript:;' onclick='add_feature(" + computed_column_id + ")'>add feature</a><input type='hidden' name='column" + computed_column_id + "_feature_count' id='column" + computed_column_id + "_feature_count' value='0' /><div id='column" + computed_column_id + "_features' class='feature_column_container'></div></td></tr><tr><td colspan='2'><a href='javascript:;' onclick='delete_column(" + computed_column_id + ")'>delete column</a></td></tr></tbody>";
        jQuery("#ypt_columns").append(new_column_value);
        computed_column_id += 1;
        jQuery("#column_count").val(computed_column_id);
    }

    let column_value;
    <?php
    foreach($this->price_table->item['columns'] as $col) {
    ?>
        // add column
        add_column();
        // populate column fields
        column_id_value = computed_column_id - 1;
        jQuery("input[name='fields[" + column_id_value + "][column_id]']").val('<?php echo $col['id']?>');
        jQuery("input[name='fields[" + column_id_value + "][column_title]']").val('<?php echo $col['column_title']?>');
        jQuery("input[name='fields[" + column_id_value + "][column_price]']").val('<?php echo $col['price_text']?>');
        jQuery("input[name='fields[" + column_id_value + "][col_button_face_text]']").val('<?php echo $col['ctoa_btn_text']?>');
        jQuery("input[name='fields[" + column_id_value + "][col_button_url]']").val('<?php echo $col['ctoa_btn_link']?>');

        <?php
            foreach($col['features'] as $feature) {
                ?>
                // add features
                add_feature(column_id_value);
                // populate features
                feautre_id_value = computed_feature_id - 1;
                jQuery("input[name='fields[" + column_id_value + "][fid][" + feautre_id_value + "]']").val('<?php echo $feature['id']?>');
                jQuery("input[name='fields[" + column_id_value + "][feature_text][" + feautre_id_value + "]']").val('<?php echo $feature['feature_text']?>');
                jQuery("input[name='fields[" + column_id_value + "][feature_checked][" + feautre_id_value + "]']").prop('checked', <?php echo ($feature['is_set'] == '1') ? 'true' : 'false'; ?>);
        <?php
            }
        ?>
    <?php
    }
    ?>
</script>