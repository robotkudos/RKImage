# RobotKudos RKImage
Image resizer and watermarker for Laravel.

## Install

`composer require robotkudos/rkimage`

## Usage
```php
<?php
use RobotKudos\RKImage\ImageUploader;
use RobotKudos\RKImage\Size;
use RobotKudos\RKImage\Watermark;
use RobotKudos\RKImage\Position;

Route::post('/', function(Request $request) {
    $imageUploader = new ImageUploader();
    // With watermark image, watermark images should be in resources folder (below: resources/img/logo-watermark-light.png)
    $watermark = new Watermark(Position::BottomRight, 'img/logo-watermark-light.png', 'img/logo-watermark-light-x2.png');
    // save returns array of two files saved, if no retina requested, it'll be null
    return var_dump($imageUploader->save($request->myimage->path(), new Size(1500), $watermark));
    // ["image_url"] => "img/image_5f2aeaccc5a110.52811690.jpg" 
    // ["image_url_retina"]=> string(37) "img/image_5f2aeacd2086e3.79326949.jpg"
})
```

## Docs

### `ImageUploader Class`
`new ImageUploader($saveRetina = true, $pathToSave = 'img/')` 

Create a new `ImageUploader` class.

`bool $saveRetina` Another version of the image will be saved with twice as big for higher resolution screens.

`string $pathToSave` The path for the image to be saved in. Must end with `/`

### `save()`

`save($path, Size $size, Watermark $watermark = null, Size $thumb = null, $quality = 80)`

Save the image on the public folder, returns the full path of the files saved in an array. 

`string $path` Path to the image to be saved. Most of the times, it should be `$request->yourInputName->path()`

`Size $size` Size of image to be saved. This is for normal pixel size, for retina it will be doubled automatically. `new Size($width, $height)`

`Watermark $watermark` If you need to add watermark, send null if no watermark is required. `new Watermark($pos, $watermarkImagePath = null, $retinaWatermarkImagePath = null, $text = null, Font $font = null)`

`Size $thumb` Size for thumb, null if no thumb is required, will created retina version of thumb is specified on Class creation.

`$quality` Quality of the image.

