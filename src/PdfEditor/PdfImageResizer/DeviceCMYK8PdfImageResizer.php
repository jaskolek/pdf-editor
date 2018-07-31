<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 27.07.2018
 * Time: 10:00
 */

namespace PdfEditor\PdfImageResizer;


/**
 * Class DeviceCMYK8PdfImageResizer
 * @package PdfEditor\PdfImageResizer
 */
class DeviceCMYK8PdfImageResizer implements PdfImageResizerInterface
{
    /**
     * @var int
     */
    protected $channelColorReductionLevel;

    /**
     * DeviceCMYK8PdfImageResizer constructor.
     * @param $channelColorReductionLevel
     */
    public function __construct($channelColorReductionLevel = 8)
    {
        $this->channelColorReductionLevel = $channelColorReductionLevel;
    }


    /**
     * @param $source
     * @param $width
     * @param $height
     * @param $newWidth
     * @param $newHeight
     * @return string
     */
    public function resize($source, $width, $height, $newWidth, $newHeight): string
    {
        $channelList = $this->getChannelList($source);

        $resizedChannelImageList = [];
        foreach ($channelList as $channelIndex => $channel) {
            $channelImage = imagecreatetruecolor($width, $height);
            foreach ($channel as $index => $pixel) {
                $color = imagecolorallocate($channelImage, $pixel, $pixel, $pixel);
                $x = $index % $width;
                $y = floor($index / $width);
                imagesetpixel($channelImage, $x, $y, $color);
            }

            $resizedChannelImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resizedChannelImage, $channelImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            $resizedChannelImageList[] = $resizedChannelImage;
        }


        $output = '';
        for ($y = 0; $y < $newHeight; ++$y) {
            for ($x = 0; $x < $newWidth; ++$x) {
                foreach ($resizedChannelImageList as $resizedChannelImage) {
                    $imageColor = imagecolorat($resizedChannelImage, $x, $y);
                    $value = imagecolorsforindex($resizedChannelImage, $imageColor);
                    $output .= \chr(floor($value['red'] / $this->channelColorReductionLevel) * $this->channelColorReductionLevel);
//                    $output .= \chr($value['red']);
                }
            }
        }
        return $output;
    }


    /**
     * @param $source
     * @return array[]
     */
    protected function getChannelList($source): array
    {
        $channelList = [[], [], [], []];
        $pixelStringList = str_split($source, 4);
        foreach ($pixelStringList as $pixelString) {
            $channelList[0][] = \ord($pixelString[0]);
            $channelList[1][] = \ord($pixelString[1]);
            $channelList[2][] = \ord($pixelString[2]);
            $channelList[3][] = \ord($pixelString[3]);
        }

        return $channelList;
    }

}