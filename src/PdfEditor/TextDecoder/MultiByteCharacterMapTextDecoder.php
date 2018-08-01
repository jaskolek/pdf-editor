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
class MultiByteCharacterMapTextDecoder implements TextDecoderInterface
{
    /**
     * @var
     */
    protected $decCharacterMap;

    protected $bytes;

    /**
     * IdentityHTextEncoder constructor.
     * @param $decCharacterMap
     * @param int $bytes
     */
    public function __construct($decCharacterMap, $bytes = 2)
    {
        $this->decCharacterMap = $decCharacterMap;
        $this->bytes = $bytes;
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

        //???? Why -2? It works this way but I don't know why
        $string = preg_replace_callback("@\\\\0(\d+)@", function($matches){
            return \chr(((int) $matches[1]) - 2);
        }, $string);
        $length = \strlen($string);
        $index = 0;

        while ($index < $length) {

            $mapIndex = 0;
            for ($i = 0; $i < $this->bytes; ++$i) {
                $mapIndex += \ord($string[$index + $i] ?? 0) * (256 ** ($this->bytes - 1 - $i));
            }

//            $mapIndex = \ord($string[$index] ?? 0) * 256 + \ord($string[$index + 1] ?? 0);
            if (isset($this->decCharacterMap[$mapIndex])) {
                $numValue = $this->decCharacterMap[$mapIndex];
                $output .= \chr($numValue);
                $index += $this->bytes;
            } else {
                $output .= $string[$index];
                $index++;
            }
        }

        return $output;
    }
}