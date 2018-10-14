<?php

namespace Tmd\LaravelHelpers\Libraries\Images;

use Config;
use Exception;
use Intervention\Image\Image as InterventionImage;
use Intervention\Image\ImageManager as InterventionImageManager;
use Tmd\LaravelHelpers\Models\Image;
use Storage;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Process\Process;

class ImageFileRepository
{
    /**
     * @var InterventionImageManager
     */
    private $imageManager;

    /**
     * @var \Illuminate\Filesystem\FilesystemAdapter
     */
    private $storage;

    /**
     * @var string
     */
    private $tempPath;

    /**
     * @var \Illuminate\Filesystem\FilesystemAdapter
     */
    private $tempStorage;

    public function __construct()
    {
        $this->imageManager = new InterventionImageManager(
            [
                'driver' => 'imagick',
            ]
        );

        $this->storage = Storage::cloud();
        $this->tempPath = config('filesystems.disks.temp.root');
        $this->tempStorage = Storage::disk('temp');
    }

    /**
     * @param File $file
     *
     * @return ImageOnDisk
     * @throws Exception
     */
    public function makeImageFromFile(File $file)
    {
        $image = $this->imageManager->make($file->getPathname());
        if (!$image) {
            throw new Exception("Unable to create image from file.");
        }

        return new ImageOnDisk($image, $file);
    }

    /**
     * @param UploadedFile $file
     *
     * @return ImageOnDisk
     * @throws Exception
     */
    public function makeImageFromUploadedFile(UploadedFile $file)
    {
        $image = $this->imageManager->make($file->getPathname());
        if (!$image) {
            throw new Exception("Unable to create image from uploaded file.");
        }

        return new ImageOnDisk($image, $file);
    }

    /**
     * @param string $url
     *
     * @return ImageOnDisk
     * @throws Exception
     */
    public function makeImageFromUrl($url)
    {
        $image = $this->imageManager->make($url);
        if (!$image) {
            throw new \Exception("Unable to create image from url.");
        }

        // Save the image to a temporary file on disk.
        $file = $this->makeTempFile();
        $image->save($file->getPathname());

        return new ImageOnDisk($image, $file);
    }

    /**
     * @param string $string
     *
     * @return ImageOnDisk
     * @throws Exception
     */
    public function makeImageFromString($string)
    {
        if (empty($string)) {
            throw new \Exception("Image string must not be empty.");
        }
        $image = $this->imageManager->make($string);
        if (!$image) {
            throw new \Exception("Unable to create image from string.");
        }

        // Save the image to a temporary file on disk.
        $file = $this->makeTempFile();
        $image->save($file->getPathname());

        return new ImageOnDisk($image, $file);
    }

    /**
     * Save the ImageOnDisk to cloud storage and return an Image model pointing to it.
     *
     * @param ImageOnDisk $imageOnDisk
     * @param string      $directory
     * @param bool|false  $keepLocalFile
     * @param string|null $filename
     * @param string      $extension
     *
     * @return Image
     */
    public function persist(
        ImageOnDisk $imageOnDisk,
        $directory,
        $keepLocalFile = false,
        $filename = null,
        $extension = 'jpg'
    ) {
        $image = $imageOnDisk->getImage();
        $file = $imageOnDisk->getFile();

        $imageModel = $this->persistImage($image, $directory, $filename, $extension);

        if (!$keepLocalFile) {
            file_exists($file->getPathname()) && unlink($file->getPathname());
        }

        return $imageModel;
    }

    /**
     * @param InterventionImage $image
     * @param                   $directory
     * @param null              $filename
     * @param string            $extension Used when generating a new filename.
     *
     * @return Image
     * @throws Exception
     */
    public function persistImage(InterventionImage $image, $directory, $filename = null, $extension = 'jpg')
    {
        // Generate a new temporary file so we can force images to be the desired format
        $tempFile = $this->makeTempFile($extension);

        $image->save($tempFile->getPathname());

        list($size, $optimizedSize) = $this->optimize($tempFile);

        $result = $this->persistFile(
            $tempFile,
            $directory,
            $filename,
            $extension,
            $image,
            $size,
            $optimizedSize
        );

        if (file_exists($tempFile->getPathname())) {
            unlink($tempFile->getPathname());
        }

        return $result;
    }

    public function persistFile(
        File $file,
        $directory,
        $filename = null,
        $extension = 'jpg',
        $image = null,
        $size = null,
        $optimizedSize = null
    ) {
        $filename = $filename ?: $this->makeFilename($extension);

        if (!$this->copyToRemote($file, $directory.'/'.$filename)) {
            throw new Exception("Unable to copy image to cloud storage.");
        }

        return $this->makeImageModel(
            $filename,
            $directory,
            $image,
            $size,
            $optimizedSize
        );
    }

    /**
     * @param File $localFile
     * @param string $remotePath
     *
     * @return bool
     */
    private function copyToRemote(File $localFile, $remotePath)
    {
        return $this->storage->put(
            $remotePath,
            file_get_contents($localFile->getPathname()),
            'public'
        );
    }

    /**
     * Generate a directory and filename to save an image as.
     *
     * @param string $extension
     * @param string $suffix
     *
     * @return string 91/52/d8ca8232{$suffix}{.$extension}
     */
    public function makeFilename($extension = 'jpg', $suffix = '')
    {
        $filename = bin2hex(openssl_random_pseudo_bytes(8));
        $filename = str_split($filename, 2);
        $filename = implode('/', array_slice($filename, 0, 3)).implode('', array_slice($filename, 3));

        if ($suffix) {
            $filename .= $suffix;
        }

        if ($extension) {
            $filename .= '.'.$extension;
        }

        return $filename;
    }

    /**
     * @param string $extension
     *
     * @return File
     */
    private function makeTempFile($extension = 'jpg')
    {
        $filename = uniqid().'.'.$extension;
        $path = $this->tempPath.'/'.$filename;
        touch($path);
        $file = new File($path);

        return $file;
    }

    /**
     * @param InterventionImage $image
     * @param string            $filename
     * @param string            $directory
     * @param int               $size
     * @param int               $optimizedSize
     *
     * @return Image
     */
    private function makeImageModel(
        $filename,
        $directory,
        InterventionImage $image = null,
        $size = null,
        $optimizedSize = null
    ) {
        return new Image(
            [
                'directory' => $directory,
                'filename' => $filename,
                'width' => $image ? $image->width() : null,
                'height' => $image ? $image->height() : null,
                'size' => $size,
                'optimizedSize' => $optimizedSize,
            ]
        );
    }

    /**
     * TODO: Move to queue. Serve unoptimized version until it's ready.
     *
     * @param File $file
     *
     * @return int[]
     */
    private function optimize(File $file)
    {
        clearstatcache();
        $size = $file->getSize();

        if ($file->getMimeType() != 'image/jpeg') {
            return [$size, null];
        }

        $jpegtran = Config::get('app.jpegtran_bin');
        $inPath = escapeshellarg($file->getPathname());
        $outPath = $inPath;

        $cmd = "{$jpegtran} -optimize -progressive -copy none -outfile {$outPath} {$inPath}";

        $process = new Process($cmd);

        $process->run();

        clearstatcache();
        $optimizedSize = $file->getSize();

        return [$size, $optimizedSize];
    }
}
