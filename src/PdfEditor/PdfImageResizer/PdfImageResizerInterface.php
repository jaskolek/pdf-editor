<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 27.07.2018
 * Time: 09:47
 */

namespace PdfEditor\PdfImageResizer;


/**
 * Interface PdfImageResizerInterface
 * @package PdfEditor\PdfImageResizer
 */
interface PdfImageResizerInterface
{
    /**
     * @param $source
     * @param $width
     * @param $height
     * @param $newWidth
     * @param $newHeight
     * @return string
     */
    public function resize($source, $width, $height, $newWidth, $newHeight): string;
}