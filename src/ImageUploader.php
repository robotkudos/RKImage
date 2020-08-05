<?php

namespace RobotKudos\RKImage;

use RobotKudos\RKImage\Size;
use Imagick;
use ImagickDraw;

class ImageUploader {
    private $pathToSave;
    private $saveRetina;
    public function __construct($saveRetina = true, $pathToSave = 'img/') {
        $this->saveRetina = $saveRetina;
        $this->pathToSave = $pathToSave;
    }
    
    public function save($path, Size $size, Watermark $watermark = null, Size $thumb = null, $quality = 80) {

        // create folder if not exists
        if (!file_exists($this->pathToSave)) {
            mkdir($this->pathToSave, 0755, true);
        }

        $imageFullpath = $this->modifyAndSave($path, $size, $watermark, $thumb, $quality, false);

        if ($this->saveRetina) {
            $imageFullpathRetina = $this->modifyAndSave($path, $size, $watermark, $thumb, $quality, true);
        }

        return [
            'image_url' => $imageFullpath,
            'image_url_retina' => $imageFullpathRetina
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

    private function getWatermarkPos($watermark, $watermarkImage, $imagick, $retina = false, $text = null) {
        $retinaMultiple = $retina ? 2 : 1;
        if ($text) {
            $pos = $imagick->queryFontMetrics($watermarkImage, $text);
            $x = $imagick->getImageWidth() - $pos["textWidth"] - (25 * $retinaMultiple);
            $y = $imagick->getImageHeight() - $pos["textHeight"] - (10 * $retinaMultiple);
        } else {
            $x = $imagick->getImageWidth() - $watermarkImage->getImageWidth() - (15 * $retinaMultiple);
            $y = $imagick->getImageHeight() - $watermarkImage->getImageHeight() - (10 * $retinaMultiple);
        }
        return new Size($x, $y);
    }


    private function modifyAndSave($path, Size $size, Watermark $watermark = null, Size $thumb = null, $quality = null, $retina = false) {

        $imagick = new Imagick(realpath($path));

        // image must not be smaller than requested size, twice if retian requested too
        if (!$this->isImageLargeEnough($size, new Size($imagick->getImageWidth, $imagick->getImageHeight))) {
            return Error('Image is not large enough');
        }
        $requestedWidth = $size->width; 
        $requestedHeight = $size->height; 
        if ($retina) {
            $requestedWidth = $size->width === 0 ? 0 : $size->width * 2;
            $requestedHeight = $size->height === 0 ? 0 : $size->height * 2;
        }

        // create image to do edit on it
        $imagick->resizeImage($requestedWidth, $requestedHeight, Imagick::FILTER_LANCZOSSHARP, 1);
        $imageName = uniqid('image_', true) . '.jpg';

        $imagick->setImageCompressionQuality($quality);
        $imagick->setImageFormat('jpg');
        // reduces the image size
        $imagick->stripImage();

        if ($watermark) {
            $watermarkImagePath = $retina ? $watermark->retinaWatermarkImagePath : $watermark->watermarkImagePath;
            // Watermark image
            if ($watermarkImagePath !== null) {
                if (!file_exists(realpath(\resource_path($watermarkImagePath)))) {
                    throw new \Error("Watermark image '$watermarkImagePath' not found in resource folder");
                }
                $watermarkImage = new Imagick(realpath(\resource_path($watermarkImagePath)));
                $watermarkPos = $this->getWatermarkPos($watermark, $watermarkImage, $imagick, $retina);
                $imagick->compositeImage($watermarkImage, Imagick::COMPOSITE_OVER, $watermarkPos->width, $watermarkPos->height);
            // not an image nor text provided
            } else if ($watermark->text === null) {
                throw new Error('Neight text nor image for watermark has been set');
            // text has been provided, textual watermark
            } else {
                if ($watermark->font) {
                    $fontSize = $retina ? $watermark->font->size * 2 : $watermark->font->size;
                    $fontFamily = $watermark->font->family;
                    $fontColor = $watermark->font->color;
                } else {
                    $fontSize = $retina ? 30 : 15;
                    $fontFamily = 'Helvetica-Bold';
                    $fontColor = 'white';
                }

                $textWatermark = new ImagickDraw();
                $textWatermark->setFillColor($fontColor);
                $textWatermark->setFontSize($fontSize);
                $textWatermark->setFontFamily($fontFamily);
                $watermarkPos = $this->getWatermarkPos($watermark, $textWatermark, $imagick, $retina, $watermark->text);
                $imagick->annotateImage($textWatermark, $watermarkPos->width, $watermarkPos->height, 0, $watermark->text);
            }
        }
        $imagick->writeImage(public_path($this->pathToSave) . $imageName);
        return $this->pathToSave . $imageName;

    }
}