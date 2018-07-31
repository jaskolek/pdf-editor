<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 28.07.2018
 * Time: 20:15
 */

namespace PdfEditor\PdfTextBlockParser\PdfTextBlockOperation;


use PdfEditor\PdfTextBlockParser\EncodedTextObject;
use PdfEditor\PdfTextBlockParser\TextObjectOptions;

/**
 * Class DisplayTextPdfTextBlockOperation
 * @package PdfEditor\PdfTextBlockParser\PdfTextBlockOperation
 */
class DisplayTextPdfTextBlockOperation implements PdfTextBlockOperationInterface
{

    /**
     * @return string
     */
    public function getSearchPattern(): string
    {
        return '@\((.*?)\)Tj@';
    }

    /**
     * @param TextObjectOptions $options
     * @param string $parametersString
     * @return null|EncodedTextObject
     */
    public function performOperation(TextObjectOptions $options, string $parametersString): ?EncodedTextObject
    {
        preg_match($this->getSearchPattern(), $parametersString, $matches);

        $newObject = new EncodedTextObject();
        $newObject->encodedText = $matches[1];
        $newObject->options = clone $options;
        return $newObject;
    }
}