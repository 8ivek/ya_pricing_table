<?php

class Feature extends Type
{
    public string $feature_checked;
    public string $feature_text;
    public string $fid;

    /**
     * @param string $feature_text
     * @param bool $feature_checked
     * @param int|null $fid
     */
    public function __construct(string $feature_text, string $feature_checked = '0', int $fid = null)
    {
        $this->feature_text = $feature_text;
        $this->feature_checked = $feature_checked;
        $this->fid = $fid;
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

        if (empty($feature_text)) {
            throw new Exception('missing mandatory field feature_text');
        }
        return new Feature($feature_text, $feature_checked, $fid);
    }
}