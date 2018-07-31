<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 28.07.2018
 * Time: 16:28
 */

namespace PdfEditor\PdfTextBlockParser\PdfTextBlockOperation;


use PdfEditor\PdfTextBlockParser\EncodedTextObject;
use PdfEditor\PdfTextBlockParser\TextObjectOptions;

/**
 * Interface PdfTextBlockOperationInterface
 * @package PdfEditor\PdfTextBlockParser\PdfTextBlockOperation
 */
interface PdfTextBlockOperationInterface
{
    /**
     * @return string
     */
    public function getSearchPattern():string;

    /**
     * @param TextObjectOptions $options
     * @param string $parametersString
     * @return null|EncodedTextObject
     */
    public function performOperation(TextObjectOptions $options, string $parametersString): ?EncodedTextObject;
}