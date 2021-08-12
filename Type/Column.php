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
     * @param int $column_id
     * @param string $column_title
     * @param string $description
     * @param string $column_price
     * @param string $column_button_url
     * @param string $column_button_face_text
     * @param array $features
     */
    public function __construct(int $column_id, string $column_title, string $description, string $column_price, string $column_button_url, string $column_button_face_text, $features)
    {
        $this->column_id = $column_id;
        $this->column_title = $column_title;
        $this->description = $description;
        $this->column_price = $column_price;
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
        if (empty($column_data_array['column_title'])) {
            throw new \Exception('missing mandatory field column title');
        }
        $feature_array = [];
        foreach ($column_data_array['feature_text'] as $key => $feature_text) {
            $arr['feature_text'] = $feature_text;
            $arr['feature_checked'] = $column_data_array['feature_checked'][$key] ?? 0;
            $arr['fid'] = $column_data_array['fid'][$key] ?? null;
            $feature_array[] = Feature::createFromArray($arr);
        }

        return new Column($column_data_array['column_id'], $column_data_array['column_title'], $column_data_array['description'], $column_data_array['column_price'], $column_data_array['column_button_url'], $column_data_array['column_button_face_text'], $feature_array);
    }
}