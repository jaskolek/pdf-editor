<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 28.07.2018
 * Time: 14:09
 */

namespace PdfEditor\PdfFilter;


class DummyPdfFilter implements PdfFilterInterface
{

    /**
     * @param $source
     * @return string
     */
    public function encode($source): string
    {
        return $source;
    }

    /**
     * @param $source
     * @return string
     */
    public function decode($source): string
    {
        return $source;
    }
}