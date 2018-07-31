<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 28.07.2018
 * Time: 19:31
 */

namespace PdfEditor\TextDecoder;


class DummyTextDecoder implements TextDecoderInterface
{

    public function decode(string $string): string
    {
        return $string;
    }
}