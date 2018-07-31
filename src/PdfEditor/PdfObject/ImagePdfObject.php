<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 27.07.2018
 * Time: 09:42
 */

namespace PdfEditor\PdfObject;


/**
 * Class ImagePdfObject
 * @package PdfEditor\PdfObject
 */
class ImagePdfObject extends BasePdfObject
{
    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        if (preg_match('@/Width (\d+)@', $this->getHeader(), $matches)) {
            return (int)$matches[1];
        }
        return null;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        if (preg_match('@/Height (\d+)@', $this->getHeader(), $matches)) {
            return (int)$matches[1];
        }
        return null;
    }

    /**
     * @return int|null
     */
    public function getLength(): ?int
    {
        if (preg_match('@/Length (\d+)@', $this->getHeader(), $matches)) {
            return (int)$matches[1];
        }
        return null;
    }

    /**
     * @param $width
     * @param $height
     * @param $length
     * @return ImagePdfObject
     */
    public function withDimensions($width, $height, $length): ImagePdfObject
    {
        $header = $this->getHeader();

        $header = str_replace([
            '/Width ' . $this->getWidth(),
            '/Height ' . $this->getHeight(),
            '/Length ' . $this->getLength()
        ], [
            '/Width ' . $width,
            '/Height ' . $height,
            '/Length ' . $length
        ], $header);

        return $this->withHeader($header);
    }
}