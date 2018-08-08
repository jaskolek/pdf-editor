<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 31.07.2018
 * Time: 12:29
 */

namespace PdfEditor\PdfDocumentCompressor;


use PdfEditor\PdfDocument;
use PdfEditor\PdfImageCompressor;
use PdfEditor\PdfImageResizer\DeviceCMYK8PdfImageResizer;

class DefaultPdfDocumentCompressor implements PdfDocumentCompressorInterface
{

    /**
     * @var PdfImageCompressor
     */
    protected $pdfImageCompressor;

    /**
     * DefaultPdfDocumentCompressor constructor.
     * @param PdfImageCompressor|null $pdfImageCompressor
     */
    public function __construct(PdfImageCompressor $pdfImageCompressor = null)
    {
        $this->pdfImageCompressor = $pdfImageCompressor ?? $this->getDefaultPdfImageCompressor();
    }

    /**
     * @return PdfImageCompressor
     */
    public function getDefaultPdfImageCompressor(): PdfImageCompressor
    {
        return new PdfImageCompressor(0, 0.5, [
            DeviceCMYK8PdfImageResizer::class => [
                'channelColorReductionLevel' => 8
            ]
        ]);
    }

    /**
     * @param $inputPath
     * @param $outputPath
     */
    public function compressFile($inputPath, $outputPath): void
    {
        $content = file_get_contents($inputPath);
        $compressedContent = $this->compressContent($content);
        file_put_contents($outputPath, $compressedContent);
    }
    /**
     * @param string $content
     * @return string
     */
    public function compressContent($content): string
    {
        $pdfDocument = new PdfDocument($content);
        $pdfDocument = $this->pdfImageCompressor->compressPdfDocument($pdfDocument);
        return $pdfDocument->getSource();
    }
}