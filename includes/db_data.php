<?php

class db_data
{
    /**
     * @param int $price_table_id
     * @return array
     */
    public function getData(int $price_table_id): array
    {
        global $wpdb;
        $price_table_row = $wpdb->get_row("SELECT pt.*, t.template_name, t.style, t.html FROM {$wpdb->prefix}yapt_pricing_tables pt INNER JOIN {$wpdb->prefix}yapt_templates t WHERE pt.template_id = t.id AND pt.id={$price_table_id}", ARRAY_A);

        if (empty($price_table_row)) {
            return [];
        }
        $item = $price_table_row;

        // get columns
        $columns = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}yapt_columns WHERE `table_id` = '" . $price_table_row['id'] . "'", ARRAY_A);

        $formatted_column = [];
        foreach ($columns as $col) {
            $features = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}yapt_features WHERE `column_id` = '" . $col['id'] . "' ORDER BY `sort_value` ASC", ARRAY_A);

            $col_temp = $col;
            $col_temp['currency_symbol'] = '$';
            if (!empty($col['price_currency'])) {
                $currency = $wpdb->get_row("SELECT `symbol` FROM  {$wpdb->prefix}yapt_currency WHERE `country` = '" . $col['price_currency'] . "'", ARRAY_A);
                $col_temp['currency_symbol'] = $currency['symbol'];
            }
            $col_temp['features'] = $features;
            $formatted_column[] = $col_temp;
        }

        $item['columns'] = $formatted_column;
        return $item;
    }
}