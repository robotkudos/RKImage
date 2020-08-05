<?php

namespace RobotKudos\RKImage;

use RobotKudos\RKImage\Size;
use RobotKudos\RKImage\Position;
use RobotKudos\RKImage\Font;

class Watermark {
    public $pos;
    public $watermarkImagePath;
    public $retinaWatermarkImagePath;
    public $text;
    public $font;

    public function __construct($pos, $watermarkImagePath = null, $retinaWatermarkImagePath = null, $text = null, Font $font = null) {
        $this->pos = $pos;
        $this->watermarkImagePath = $watermarkImagePath;
        $this->retinaWatermarkImagePath = $retinaWatermarkImagePath;
        $this->text = $text;
        $this->font = $font;
    }
}