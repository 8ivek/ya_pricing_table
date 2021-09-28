<?php

class Feature extends Type
{
    public string $feature_checked;
    public string $feature_text;
    public string $fid;
    public string $sort_value;

    /**
     * @param string $feature_text
     * @param string $feature_checked
     * @param int|null $fid
     * @param string|null $sort_value
     */
    public function __construct(string $feature_text, string $feature_checked = '0', int $fid = null, string $sort_value = null)
    {
        $this->feature_text = $feature_text;
        $this->feature_checked = $feature_checked;
        $this->fid = $fid;
        $this->sort_value = $sort_value;
    }

    /**
     * @param array $feature_data_array
     * @return Feature
     * @throws Exception
     */
    public static function createFromArray(array $feature_data_array): Feature
    {
        $feature_text = $feature_data_array['feature_text'];
        $feature_checked = $feature_data_array['feature_checked'];
        $fid = $feature_data_array['fid'];
        $sort_value = $feature_data_array['sort_value'];

        if (empty($feature_text)) {
            throw new Exception('missing mandatory field feature_text');
        }
        return new Feature($feature_text, $feature_checked, $fid, $sort_value);
    }
}