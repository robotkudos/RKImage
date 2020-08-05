<?php

namespace RobotKudos\RKImage;

class Font {
    private $family;
    private $size;

    public function __construct($family, $size) {
        $this->family = $family;
        $this->size = $size;
    }
}