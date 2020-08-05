<?php

namespace RobotKudos\RKImage;

class Font {
    private $family;
    private $size;

    public function __construct($size, $family = 'Helvetica-Bold', $color = 'white') {
        $this->family = $family;
        $this->size = $size;
        $this->color = $color;
    }
}