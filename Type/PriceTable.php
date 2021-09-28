<?php

class PriceTable extends Type
{
    public int $price_table_id;
    public string $pricing_table_title;
    public int $template_id;
    public string $custom_styles;
    public array $columns;

    /**
     * @param int $price_table_id
     * @param string $pricing_table_title
     * @param int $template_id
     * @param string $custom_styles
     * @param array $columns
     */
    public function __construct(int $price_table_id, string $pricing_table_title, int $template_id, string $custom_styles, array $columns)
    {
        $this->price_table_id = $price_table_id;
        $this->pricing_table_title = $pricing_table_title;
        $this->template_id = $template_id;
        $this->custom_styles = $custom_styles;
        $this->columns = $columns;
    }

    /**
     * @param array $price_table_data
     * @return PriceTable
     * @throws Exception
     */
    public static function createFromArray(array $price_table_data): PriceTable
    {
        $pricing_table_title = sanitize_text_field($price_table_data['pricing_table_title']);
        $template_id = (int)sanitize_text_field($price_table_data['template_id']);
        $price_table_id = (int)sanitize_text_field($price_table_data['price_table_id'] ?? 0);
        $custom_styles = str_replace("/* styles here */", '', sanitize_textarea_field($price_table_data['custom_styles']));
        $highlighted_key = sanitize_text_field($price_table_data['highlighted'] ?? '');

        if (empty($pricing_table_title) || empty($template_id)) {
            throw new Exception('missing mandatory fields pricing_table_title or template');
        }

        $column_array = [];
        if (is_array($price_table_data['fields'])) {
            foreach ($price_table_data['fields'] as $key => $cols) {

                $is_highlighted = '0';
                if ($key == $highlighted_key) {
                    $is_highlighted = '1';
                }

                $cols['highlighted'] = $is_highlighted;

                if (empty(sanitize_text_field($cols['column_title']))) {
                    continue;
                }


                $col_feature_text = [];
                $temp_arr = [];
                if (!empty(sanitize_text_field($cols['feature_order']))) {
                    $feature_orders = explode('&', sanitize_text_field($cols['feature_order']));
                    $sort_value = 0;
                    foreach ($feature_orders as $fo) {
                        [$x, $feature] = explode('=', $fo);
                        $key1 = (int)str_replace('feature', '', $feature);
                        $temp_arr['feature_text'] = sanitize_text_field($cols['feature_text'][$key1]);
                        $temp_arr['feature_checked'] = (isset($cols['feature_checked'][$key1]) && $cols['feature_checked'][$key1] == '1') ? '1' : '0';
                        $temp_arr['fid'] = (int)sanitize_text_field($cols['fid'][$key1] ?? 0);
                        $temp_arr['sort_value'] = $sort_value;
                        $col_feature_text[] = $temp_arr;
                        $sort_value++;
                    }
                } else {
                    foreach ($cols['feature_text'] as $key1 => $value) {
                        $temp_arr['feature_text'] = sanitize_text_field($cols['feature_text'][$key1]);
                        $temp_arr['feature_checked'] = (isset($cols['feature_checked'][$key1]) && $cols['feature_checked'][$key1] == '1') ? '1' : '0';
                        $temp_arr['fid'] = (int)sanitize_text_field($cols['fid'][$key1] ?? 0);
                        $temp_arr['sort_value'] = $key1;
                        $col_feature_text[] = $temp_arr;
                    }
                }
                $cols['feature_data'] = $col_feature_text;
                $column_array[] = Column::createFormArray($cols);
            }
        }
        return new PriceTable($price_table_id, $pricing_table_title, $template_id, $custom_styles, $column_array);
    }
}