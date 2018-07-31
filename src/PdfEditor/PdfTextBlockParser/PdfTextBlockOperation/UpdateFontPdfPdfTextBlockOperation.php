<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 28.07.2018
 * Time: 16:31
 */

namespace PdfEditor\PdfTextBlockParser\PdfTextBlockOperation;


use PdfEditor\PdfTextBlockParser\EncodedTextObject;
use PdfEditor\PdfTextBlockParser\TextObjectOptions;

/**
 * Class UpdateFontPdfPdfTextBlockOperation
 * @package PdfEditor\PdfTextBlockParser\PdfTextBlockOperation
 */
class UpdateFontPdfPdfTextBlockOperation implements PdfTextBlockOperationInterface
{

    /**
     * @param TextObjectOptions $options
     * @param string $parametersString
     * @return null|EncodedTextObject
     */
    public function performOperation(TextObjectOptions $options, string $parametersString):?EncodedTextObject
    {
        preg_match($this->getSearchPattern(), $parametersString, $matches);
        $options->fontId = $matches[1];
        $options->fontSize = $matches[2];

        return null;
    }

    /**
     * @return string
     */
    public function getSearchPattern(): string
    {
        return '@/([a-zA-Z0-9]+)\s+([0-9.]+)\s*Tf@';
    }
}