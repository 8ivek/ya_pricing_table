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
        if (empty($price_table_data['pricing_table_title']) || empty($price_table_data['template_id'])) {
            throw new Exception('missing mandatory fields pricing_table_title or template');
        }
        $column_array = [];
        foreach ($price_table_data['fields'] as $cols) {
            $column_array[] = Column::createFormArray($cols);
        }
        return new PriceTable($price_table_data['price_table_id'], $price_table_data['pricing_table_title'], $price_table_data['template_id'], $price_table_data['custom_styles'], $column_array);
    }
}