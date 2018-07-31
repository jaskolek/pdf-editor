<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 27.07.2018
 * Time: 09:50
 */

namespace PdfEditor\PdfFilter;


class FlateDecodePdfFilter implements PdfFilterInterface
{

    /**
     * @param $source
     * @return string
     */
    public function encode($source): string
    {
        return gzcompress($source);
    }

    /**
     * @param $source
     * @return string
     */
    public function decode($source): string
    {
        $result = gzuncompress($source);

        if($result === false){
            $result = gzuncompress(trim($source));
        }

        if($result === false){
            throw new \InvalidArgumentException('Can not decode string "' . $source . '"');
        }

        return $result;
    }
}