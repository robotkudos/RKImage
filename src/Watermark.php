<?php

namespace RobotKudos\RKImage;

use RobotKudos\RKImage\Size;
use RobotKudos\RKImage\Position;
use RobotKudos\RKImage\Font;

class Watermark {
    private $size;
    private $pos;
    private $watermarkImagePath;
    private $retinaWatermarkImagePath;
    private $text;
    private $font;

    public function __construct(Size $size, Position $pos, $watermarkImagePath = null, $retinaWatermarkImagePath = null, $text = null, Font $font = null) {
        $this->size = $size;
        $this->pos = $pos;
        $this->watermarkImagePath = $watermarkImagePath;
        $this->text = $text;
        $this->font = $font;
    }
}