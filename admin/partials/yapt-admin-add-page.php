<?php

/**
 * Provide a admin area view for adding new pricing table
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
$results_templates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ya_templates", ARRAY_A);
?>
<div id="wrap">
    <h2 id="add_pricing_table">Add pricing table</h2>
    <form method="post" action="options.php">
        <table>
            <tr>
                <td>Select template</td>
                <td><select name="template" required="required">
                    <option value="0">select a template</option>
                        <?php
                        foreach ($results_templates as $template) {
                            ?>
                            <option value="<?php echo $template['id'] ?>"><?php echo $template['template_name']; ?></option>
                            <?php
                        }
                        ?>
                    </select></td>
            </tr>
            <tr>
                <td colspan="2">
                    <script>
                        var column_id = 1;
                        /**
                         * add feature
                         * @param column_id
                         */
                        let computed_feature_id;
                        function add_feature (column_id) {
                            computed_feature_id = parseInt(jQuery("#column"+column_id+"_feature_count").val()) +1;
                            //console.log(computed_feature_id);
                            //console.log('add feature clicked for table '+ column_id);
                            let new_feature_value = "<div id='column"+column_id+"_feature"+computed_feature_id+"'> <input type='checkbox' name='feature"+computed_feature_id+"_checked[]' value='1' /> <input type='text' name='feature"+computed_feature_id+"_text[]' placeholder='Feature text content ...' value='' /> <a href='javascript:;' onclick='delete_feature("+column_id+", "+computed_feature_id+")'>delete</a></div>";
                            jQuery("#column"+column_id+"_features").append(new_feature_value);
                            jQuery("#column"+column_id+"_feature_count").val(computed_feature_id);
                        }

                        /**
                         *
                         * @param column_id
                         * @param feature_id
                         */
                        function delete_feature(column_id, feature_id) {
                            console.log('delete feature '+ feature_id + ' from table '+ column_id);
                            jQuery("#column"+column_id+"_feature"+feature_id).remove();
                        }
                    </script>
                    <table>
                        <tbody id="tbl_column1">
                        <tr>
                            <td>Name</td>
                            <td><input type="text" name="tbl_name"/></td>
                        </tr>
                        <tr>
                            <td>Pricing</td>
                            <td><input type="text" name="tbl_pricing"/></td>
                        </tr>
                        <tr>
                            <td>Button face text</td>
                            <td><input type="text" name="tbl_button_face_text"/></td>
                        </tr>
                        <tr>
                            <td>Button url</td>
                            <td><input type="text" name="tbl_button_url"/></td>
                        </tr>
                        <tr>
                            <td valign="top">Features</td>
                            <td>
                                <a href="javascript:;" onclick="add_feature(1)">add feature</a>
                                <input type="hidden" name="column1_feature_count" id="column1_feature_count" value="1" />
                                <div id='column1_features' class='feature_column_container'>
                                    <div id='column1_feature1'>
                                        <input type='checkbox' name='feature1_checked[]' value='1' />
                                        <input type='text' name='feature1_text[]' placeholder='Feature text content ...' value='' />
                                        <a href='javascript:;' onclick='delete_feature(1, 1)'>delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php
                    settings_fields('yapt-save-settings');
                    do_settings_sections('yapt-save-settings');
                    submit_button();
                    ?>
                </td>
            </tr>
        </table>
    </form>
</div>