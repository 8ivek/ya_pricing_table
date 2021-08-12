<?php

class Feature extends Type
{
    public bool $feature_checked;
    public string $feature_text;
    public string $fid;

    /**
     * @param string $feature_text
     * @param int $feature_checked
     * @param int|null $fid
     */
    public function __construct(string $feature_text, int $feature_checked = 0, $fid = null)
    {
        $this->feature_text = $feature_text;
        $this->feature_checked = $feature_checked;
        $this->fid = $fid;
    }

    /**
     * @param $feature_data_array
     * @return Feature
     * @throws Exception
     */
    public static function createFromArray($feature_data_array): Feature
    {
        if (empty($feature_data_array['feature_text'])) {
            throw new Exception('missing mandatory field feature_text');
        }
        return new Feature($feature_data_array['feature_text'], $feature_data_array['feature_checked'], $feature_data_array['fid']);
    }
}