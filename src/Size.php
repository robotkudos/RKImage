<?php

namespace RobotKudos\RKImage;

class Size {
    public $height;
    public $width;
    function __construct($width = 0, $height = 0) {
        if ($width === 0 && $height === 0) {
            throw new \Error('Width and Height cannot both be null');
        }
        $this->width = $width;
        $this->height = $height;
    }
}