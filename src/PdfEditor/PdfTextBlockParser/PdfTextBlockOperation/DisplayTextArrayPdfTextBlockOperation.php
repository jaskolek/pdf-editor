<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 31.07.2018
 * Time: 18:13
 */

namespace PdfEditor\PdfTextBlockParser\PdfTextBlockOperation;


use PdfEditor\PdfTextBlockParser\EncodedTextObject;
use PdfEditor\PdfTextBlockParser\TextObjectOptions;

class DisplayTextArrayPdfTextBlockOperation implements PdfTextBlockOperationInterface
{

    /**
     * @return string
     */
    public function getSearchPattern(): string
    {
        return '@\[(.*?)\]TJ@';
    }

    /**
     * @param TextObjectOptions $options
     * @param string $parametersString
     * @return EncodedTextObject[]
     */
    public function performOperation(TextObjectOptions $options, string $parametersString): array
    {

        preg_match_all('@((\(.*?\))|([0-9.-]+))@', $parametersString, $matches);

        $encodedObjectList = [];
        foreach($matches[0] as $match){
            if($match[0] === '('){
                $newObject = new EncodedTextObject();
                $newObject->encodedText = substr($match, 1, -1);
                $newObject->options = clone $options;
                $encodedObjectList[] = $newObject;
            }else{
                $options->x += (float) $match;
            }
        }

        return $encodedObjectList;
    }
}