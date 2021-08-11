<?php

class Type
{
    public function jsonSerialize(): Type
    {
        return $this;
    }
}