<?php

namespace RobotKudos\RKImage;

use RobotKudos\RKImage\Size;
use Imagick;

class ImageUploader {
    private $pathToSave;
    private $saveRetina;
    public function __construct($saveRetina = true, $pathToSave = 'img/') {
        $this->saveRetina = $saveRetina;
        $this->pathToSave = $pathToSave;
    }
    public function save($path, Size $size, Watermark $watermark = null, Size $thumb = null, $quality = 80) {
        $imagick = new Imagick(realpath($path));

        if (!$this->isImageLargeEnough($size, new Size($imagick->getImageWidth, $imagick->getImageHeight))) {
            return Error('Image is not large enough');
        }

        $imagick->resizeImage($size->width, $size->height, Imagick::FILTER_LANCZOSSHARP, 1);
        $imageName = uniqid('image_', true) . '.jpg';
        if (!file_exists($this->pathToSave)) {
            mkdir($this->pathToSave, 0755, true);
        }
        $imagick->setImageCompressionQuality($quality);
        $imagick->setImageFormat('jpg');
        $imagick->stripImage();
        if ($watermark) {
            if ($watermark->watermarkImagePath === null) {
                return Error('Watermark text is not currently supported');
            }
            $watermarkImage = new Imagick(realpath(\resource_path($watermark->watermarkImagePath)));
            $watermarkPos = $this->getWatermarkPos($watermark, $watermarkImage, $imagick);
            $imagick->compositeImage($watermarkImage, Imagick::COMPOSITE_OVER, $watermarkPos->width, $watermarkPos->height);
        }

        $imagick->writeImage(public_path($this->pathToSave) . $imageName);

        if ($this->saveRetina) {
            $imagick = new Imagick(realpath($path));

            $width = $size->width === 0 ? 0 : $size->width * 2;
            $height = $size->height === 0 ? 0 : $size->height * 2;
            $imagick->resizeImage($width, $height, Imagick::FILTER_LANCZOSSHARP, 1);
            $imageNameRetina = uniqid('image_', true) . '.jpg';
            if (!file_exists($this->pathToSave)) {
                mkdir($this->pathToSave, 0755, true);
            }
            $imagick->setImageCompressionQuality($quality);
            $imagick->setImageFormat('jpg');
            $imagick->stripImage();
            if ($watermark) {
                if ($watermark->retinaWatermarkImagePath === null) {
                    throw new \Error('Watermark text is not currently supported');
                }
                $watermarkImage = new Imagick(realpath(\resource_path($watermark->retinaWatermarkImagePath)));
                $watermarkPos = $this->getWatermarkPos($watermark, $watermarkImage, $imagick, true);
                $imagick->compositeImage($watermarkImage, Imagick::COMPOSITE_OVER, $watermarkPos->width, $watermarkPos->height);
            }
            $imagick->writeImage(public_path($this->pathToSave) . $imageNameRetina);
        }

        return [
            'image_url' => $this->pathToSave . $imageName,
            'image_url_retina' => $this->pathToSave . $imageNameRetina
        ];



    }

    private function isImageLargeEnough(Size $requestedSize, Size $givenImageSize, $retina = false) {
        // if saveRetian is true, min requested width should be times 2
        $retinaMultiple = $retina ? 2 : 1;
        $retinaMultiple = $this->saveRetina ? 2 : 1;
        if ($requestedSize->width * $retinaMultiple < $givenImageSize->width) {
            return false;
        } else {
            return true;
        }
    }

    private function getWatermarkPos($watermark, $watermarkImage, $imagick, $retina = false) {
        $retinaMultiple = $retina ? 2 : 1;
        if ($watermark->pos == Position::BottomRight) {
            $x = $imagick->getImageWidth() - $watermarkImage->getImageWidth() - (15 * $retinaMultiple);
            $y = $imagick->getImageHeight() - $watermarkImage->getImageHeight() - (10 * $retinaMultiple);
            return new Size($x, $y);
        }
    }
}