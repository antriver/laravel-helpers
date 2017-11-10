<?php

namespace Tmd\LaravelSite\Libraries\Images;

use Intervention\Image\Image as InterventionImage;
use Symfony\Component\HttpFoundation\File\File;

/**
 * And ImageOnDisk is used to group together an image object (from Intervention) and its corresponding
 * file object (from Symfony).
 */
class ImageOnDisk
{
    /**
     * @var InterventionImage
     */
    protected $image;

    /**
     * @var File
     */
    protected $file;

    /**
     * ImageOnDisk constructor.
     *
     * @param InterventionImage $image
     * @param File              $file
     */
    public function __construct(InterventionImage $image, File $file)
    {
        $this->setImage($image);
        $this->setFile($file);
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file)
    {
        $this->file = $file;
    }

    /**
     * @return InterventionImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param InterventionImage $image
     */
    public function setImage(InterventionImage $image)
    {
        $this->image = $image;
    }
}
