<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 27.07.2018
 * Time: 09:49
 */

namespace PdfEditor\PdfFilter;


/**
 * Interface PdfFilterInterface
 * @package PdfEditor\PdfFilter
 */
interface PdfFilterInterface
{
    /**
     * @param $source
     * @return string
     */
    public function encode($source): string;

    /**
     * @param $source
     * @return string
     */
    public function decode($source): string;
}