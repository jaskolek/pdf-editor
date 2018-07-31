<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 28.07.2018
 * Time: 14:20
 */

namespace PdfEditor\TextDecoder;


/**
 * Interface TextDecoderInterface
 * @package PdfEditor\TextDecoder
 */
interface TextDecoderInterface
{
    /**
     * @param string $string
     * @return string
     */
    public function decode(string $string): string;
}