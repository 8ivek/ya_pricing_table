<?php

class Column extends Type
{
    public int $column_id;
    public string $column_title;
    public string $description;
    public string $column_price;
    public string $column_button_url;
    public string $column_button_face_text;
    public array $features;

    /**
     * @param string $column_title
     * @param string $description
     * @param string $column_price
     * @param string $column_button_url
     * @param string $column_button_face_text
     * @param array $features
     */
    public function __construct(string $column_title, string $description, string $column_price, string $column_button_url, string $column_button_face_text, $features)
    {
        $this->column_title = $column_title;
        $this->description = $description;
        $this->column_price = $column_price;
        $this->column_button_url = $column_button_url;
        $this->column_button_face_text = $column_button_face_text;
        $this->features = $features;
    }

    /**
     * @throws \Exception
     */
    public static function createFormArray($column_data_array): Column
    {
        if (empty($column_data_array['column_title'])) {
            throw new \Exception('missing mandatory field column title');
        }
        $feature_array = [];
        foreach ($column_data_array['features'] as $feature) {
            $feature_array[] = Feature::createFromArray($feature);
        }

        return new Column($column_data_array['column_title'], $column_data_array['description'], $column_data_array['column_price'], $column_data_array['column_button_url'], $column_data_array['column_button_face_text'], $feature_array);
    }
}