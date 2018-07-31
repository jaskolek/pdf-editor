<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 27.07.2018
 * Time: 12:10
 */

namespace PdfEditor;


/**
 * Class StringUtils
 * @package PdfEditor
 */
class StringUtils
{
    /**
     * @param $string
     * @param $newString
     * @param $startString
     * @param $endString
     * @param bool $outer
     * @return string
     */
    public static function replaceStringBetween($string, $newString, $startString, $endString, $outer = true): string
    {
        $start = strpos($string, $startString);
        $end = strpos($string, $endString, $start);

        if ($start === false || $end === false) {
            return $string;
        }

        if ($outer === true) {
            //replace entire outer text with $startString and $endString
            $length = $end - $start + \strlen($endString);
        } else {
            //just inner text
            $start += \strlen($startString);
            $length = $end - $start;
        }

        $string = substr_replace($string, $newString, $start, $length);
        return $string;
    }

    /**
     * @param $string
     * @param $startString
     * @param $endString
     * @param bool $outer
     * @return string
     */
    public static function deleteStringBetween($string, $startString, $endString, $outer = true): string
    {
        return self::replaceStringBetween($string, '', $startString, $endString, $outer);
    }

    /**
     * @param $string
     * @param $startString
     * @param $endString
     * @param bool $outer
     * @return null|string
     */
    public static function getStringBetween($string, $startString, $endString, $outer = true): ?string
    {

        $start = strpos($string, $startString);
        $end = strpos($string, $endString);

        if ($start === false || $end === false) {
            return null;
        }
        if ($outer === true) {
            //replace entire outer text with $startString and $endString
            $length = $end - $start + \strlen($endString);
        } else {
            //just inner text
            $start += \strlen($startString);
            $length = $end - $start;
        }

        return substr($string, $start, $length);
    }


}