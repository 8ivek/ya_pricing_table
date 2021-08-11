<?php

class Feature extends Type
{
    public bool $feature_checked;
    public string $feature_text;

    public function __construct($feature_text, $feature_checked = 0)
    {
        $this->feature_text = $feature_text;
        $this->feature_checked = $feature_checked;
    }

    public static function createFromArray($feature_data_array)
    {
        if (empty($feature_data_array['feature_text'])) {
            throw new Exception('missing mandatory field feature_text');
        }
        return new Feature($feature_data_array['feature_text'], $feature_data_array['feature_checked']);
    }
}