<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 28.07.2018
 * Time: 14:21
 */

namespace PdfEditor\TextDecoder;


/**
 * Class IdentityHTextDecoder
 * @package PdfEditor\TextDecoder
 */
class IdentityHTextDecoder implements TextDecoderInterface
{
    /**
     * @var
     */
    protected $decCharacterMap;

    /**
     * IdentityHTextEncoder constructor.
     * @param $decCharacterMap
     */
    public function __construct($decCharacterMap)
    {
        $this->decCharacterMap = $decCharacterMap;
    }


    /**
     * @param string $string
     * @return string
     */
    public function decode(string $string): string
    {
        $output = '';
        $specialCharacterList = [
            "\\\\" => "\\",
            '\\(' => '(',
            '\\)' => ')',
            '\\n' => "\n",
            '\\r' => "\r",
            '\\t' => "\t",
            '\\b' => "\b",
            '\\f' => "\f"
        ];
        $string = str_replace(array_keys($specialCharacterList), array_values($specialCharacterList), $string);

        $length = \strlen($string);
        $index = 0;

        while ($index < $length) {

            $mapIndex = \ord($string[$index] ?? 0) * 256 + \ord($string[$index + 1] ?? 0);
            if (isset($this->decCharacterMap[$mapIndex])) {
                $numValue = $this->decCharacterMap[$mapIndex];
                $output .= \chr($numValue);
                $index += 2;
            } else {
                $output .= $string[$index];
                $index++;
            }
        }

        return $output;
    }
}