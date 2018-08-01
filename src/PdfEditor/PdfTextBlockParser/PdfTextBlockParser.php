<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 28.07.2018
 * Time: 19:39
 */

namespace PdfEditor\PdfTextBlockParser;


use PdfEditor\PdfTextBlockParser\PdfTextBlockOperation\DisplayTextArrayPdfTextBlockOperation;
use PdfEditor\PdfTextBlockParser\PdfTextBlockOperation\DisplayTextPdfTextBlockOperation;
use PdfEditor\PdfTextBlockParser\PdfTextBlockOperation\PdfTextBlockOperationInterface;
use PdfEditor\PdfTextBlockParser\PdfTextBlockOperation\UpdateFontPdfPdfTextBlockOperation;
use PdfEditor\PdfTextBlockParser\PdfTextBlockOperation\UpdatePositionPdfTextBlockOperation;

/**
 * Class PdfTextBlockParser
 * @package PdfEditor\PdfTextBlockParser
 */
class PdfTextBlockParser
{


    /**
     * @param $textBlock
     * @return EncodedTextObject[]
     */
    public function extractTextObjectList($textBlock): array
    {
        $textObjectOptions = new TextObjectOptions();
        /** @var PdfTextBlockOperationInterface[] $pdfTextBlockOperationList */
        $pdfTextBlockOperationList = [
            new UpdateFontPdfPdfTextBlockOperation(),
            new UpdatePositionPdfTextBlockOperation(),
            new DisplayTextPdfTextBlockOperation(),
            new DisplayTextArrayPdfTextBlockOperation()
        ];

        /** @var array $operationList */
        $operationList = [];

        foreach ($pdfTextBlockOperationList as $pdfTextBlockOperation) {
            $pattern = $pdfTextBlockOperation->getSearchPattern();
            preg_match_all($pattern, $textBlock, $matches, PREG_OFFSET_CAPTURE);

            foreach ($matches[0] as $match) {
                $operationList[$match[1]] = [$pdfTextBlockOperation, $match[0]];
            }
        }

        ksort($operationList);
        $operationList = array_values($operationList);

        $textObjectList = [];

        /**
         * @var int $key
         * @var PdfTextBlockOperationInterface $operation
         * @var string $parametersString
         */
        foreach ($operationList as $key => [$operation, $parametersString]) {
            $newTextObjectList = $operation->performOperation($textObjectOptions, $parametersString);
            foreach ($newTextObjectList as $newTextObject) {
                $textObjectList[] = $newTextObject;
            }
        }
        return $textObjectList;
    }


}