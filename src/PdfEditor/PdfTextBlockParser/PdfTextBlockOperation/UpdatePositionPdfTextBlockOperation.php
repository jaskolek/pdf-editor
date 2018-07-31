<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 28.07.2018
 * Time: 19:54
 */

namespace PdfEditor\PdfTextBlockParser\PdfTextBlockOperation;


use PdfEditor\PdfTextBlockParser\EncodedTextObject;
use PdfEditor\PdfTextBlockParser\TextObjectOptions;

/**
 * Class UpdatePositionPdfTextBlockOperation
 * @package PdfEditor\PdfTextBlockParser\PdfTextBlockOperation
 */
class UpdatePositionPdfTextBlockOperation implements PdfTextBlockOperationInterface
{

    /**
     * @return string
     */
    public function getSearchPattern(): string
    {
        return '@([0-9.+-]+)\s+([0-9.+-]+)\s+Td\s+@';
    }

    /**
     * @param TextObjectOptions $options
     * @param string $parametersString
     * @return null|EncodedTextObject
     */
    public function performOperation(TextObjectOptions $options, string $parametersString): ?EncodedTextObject
    {
        preg_match($this->getSearchPattern(), $parametersString, $matches);
        [, $x, $y] = $matches;

        $options->x += (float)$x;
        $options->y += (float)$y;

        return null;
    }
}