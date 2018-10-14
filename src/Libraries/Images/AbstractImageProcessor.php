<?php

namespace Tmd\LaravelHelpers\Libraries\Images;

use Imagick;
use Intervention\Image\Image as InterventionImage;

abstract class AbstractImageProcessor
{
    /**
     * Do what you gotta do with the uploaded image.
     * May return a string filename, an Image model, an array, or something depending on the implementation.
     *
     * @param InterventionImage $image
     *
     * @return mixed
     */
    abstract public function process(InterventionImage $image);

    /**
     * @param InterventionImage $image
     * @param int               $width
     * @param int               $height
     */
    protected function toThumbnail(InterventionImage $image, $width, $height)
    {
        /** @var Imagick $imagickImage */
        $imagickImage = $image->getCore();

        $imagickImage->cropThumbnailImage($width, $height);
    }

    protected function toBackground(InterventionImage $image)
    {
        /** @var Imagick $imagickImage */
        $imagickImage = $image->getCore();

        $imagickImage->setGravity(Imagick::GRAVITY_CENTER);

        $imagickImage->resizeImage(1280, 0, Imagick::FILTER_CATROM, 1);

        $imagickImage->blurImage(10, 10);

        $imagickImage->evaluateImage(Imagick::EVALUATE_SUBTRACT, 0.5);
    }
}
