<?php

namespace Neok\LiipImagineAdditionalFiltersBundle\Imagine\Filter\Loader;

use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class PixelateLoader implements LoaderInterface
{
    /**
     * Driver - one of the three drivers: gd, imagick, gmagick.
     *
     * @var string
     */
    private $driver;

    /**
     * PixelateLoader constructor.
     *
     * @param string $driver
     */
    public function __construct($driver)
    {
        $this->driver = (string)$driver;
    }

    public function load(ImageInterface $image, array $options = [])
    {
        $x = $options['start'][0] ?? 0;
        $y = $options['start'][1] ?? 0;

        $width  = $options['size'][0] ?? 0;
        $height = $options['size'][1] ?? 0;

        $intensity = $options['intensity'] ?? 20;

        $type = $options['type'] ?? 'rectangle';

        $this->pixelate($image, $x + $width, $y + $height, $x, $y, $intensity, $type);

        return $image;
    }

    public function pixelate(
        ImageInterface $image,
        $width,
        $height,
        $startX,
        $startY,
        $intensity = 10,
        $type = 'ellipse'
    ) {
        $img = $image->getGdResource();

        $r = $width / 2;
        // start from the top-left pixel and keep looping until we have the desired effect
        for ($y = $startY; $y < $height; $y += $intensity + 1) {
            for ($x = $startX; $x < $width; $x += $intensity + 1) {
                $rgb   = imagecolorsforindex($img, imagecolorat($img, $x, $y));
                $color = imagecolorclosest($img, $rgb['red'], $rgb['green'], $rgb['blue']);

                imagefilledrectangle($img, $x, $y, $x + $intensity, $y + $intensity, $color);
            }
        }

        return $img;
    }
}
