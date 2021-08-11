<?php

class PriceTable extends Type
{
    public int $price_table_id;
    public string $pricing_table_title;
    public int $template_id;
    public string $custom_styles;

    public function __construct($price_table_id, $pricing_table_title, $template_id, $custom_styles)
    {
        $this->price_table_id = $price_table_id;
        $this->pricing_table_title = $pricing_table_title;
        $this->template_id = $template_id;
        $this->custom_styles = $custom_styles;
    }

    public static function createFromArray($price_table_data): PriceTable
    {
        if (empty($price_table_data['pricing_table_title']) || empty($price_table_data['template_id'])) {
            throw new Exception('missing mandatory fields price_table_title or template_id');
        }
        return new PriceTable($price_table_data['price_table_id'], $price_table_data['pricing_table_title'], $price_table_data['template_id'], $price_table_data['custom_styles']);
    }
}