<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 31.07.2018
 * Time: 12:23
 */

namespace PdfEditor\PdfDocumentCompressor;


/**
 * Interface PdfDocumentCompressorInterface
 * @package PdfEditor\PdfDocumentCompressor
 */
interface PdfDocumentCompressorInterface
{
    /**
     * @param $inputPath
     * @param $outputPath
     * @return void
     */
    public function compressFile($inputPath, $outputPath): void;
}