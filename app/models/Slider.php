<?php

use Phalcon\Mvc\Model;

class Slider extends Model
{
    public function initialize()
    {
        $this->belongsTo(
            "file_id",
            "File",
            "id"
        );
    }

}
