<?php

class Column extends Type
{
    public int $column_id;
    public string $column_title;
    public string $highlighted;
    public string $description;
    public string $column_price_currency;
    public string $column_price;
    public string $column_price_suffix;
    public string $column_button_url;
    public string $column_button_face_text;
    public array $features;

    /**
     * @param int $column_id
     * @param string $column_title
     * @param string $highlighted
     * @param string $description
     * @param string $column_price
     * @param string $column_button_url
     * @param string $column_button_face_text
     * @param array $features
     */
    public function __construct(int $column_id, string $column_title, string $highlighted, string $description, string $column_price_currency, string $column_price, string $column_price_suffix, string $column_button_url, string $column_button_face_text, array $features)
    {
        $this->column_id = $column_id;
        $this->column_title = $column_title;
        $this->highlighted = $highlighted;
        $this->description = $description;
        $this->column_price_currency = $column_price_currency;
        $this->column_price = $column_price;
        $this->column_price_suffix = $column_price_suffix;
        $this->column_button_url = $column_button_url;
        $this->column_button_face_text = $column_button_face_text;
        $this->features = $features;
    }

    /**
     * @param array $column_data_array
     * @return Column
     * @throws Exception
     */
    public static function createFormArray(array $column_data_array): Column
    {
        $column_title = sanitize_text_field($column_data_array['column_title']);
        $column_id = (int)sanitize_text_field($column_data_array['column_id'] ?? 0);
        $description = sanitize_text_field($column_data_array['description']);
        $column_price_currency = sanitize_text_field($column_data_array['column_price_currency']);
        $column_price = sanitize_text_field($column_data_array['column_price']);
        $column_price_suffix = sanitize_text_field($column_data_array['column_price_suffix']);
        $column_button_url = sanitize_text_field($column_data_array['column_button_url']);
        $column_button_face_text = sanitize_text_field($column_data_array['column_button_face_text']);
        $highlighted = $column_data_array['highlighted'];

        if (empty($column_title)) {
            throw new \Exception('missing mandatory field column title');
        }

        $feature_array = [];
        if (is_array($column_data_array['feature_data'])) {
            foreach($column_data_array['feature_data'] as $feature_data) {
                $feature_array[] = Feature::createFromArray($feature_data);
            }
        }

        return new Column($column_id, $column_title, $highlighted, $description, $column_price_currency, $column_price, $column_price_suffix, $column_button_url, $column_button_face_text, $feature_array);
    }
}