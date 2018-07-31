<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 31.07.2018
 * Time: 12:24
 */

namespace PdfEditor\PdfDocumentCompressor;


use PdfEditor\PdfDocument;
use PdfEditor\PdfImageCompressor;
use PdfEditor\PdfImageResizer\DeviceCMYK8PdfImageResizer;

class FordComWindowStickerPdfDocumentCompressor implements PdfDocumentCompressorInterface
{
    /**
     * @var PdfImageCompressor
     */
    protected $pdfImageCompressor;

    /**
     * FordComWindowStickerPdfDocumentCompressor constructor.
     * @param PdfImageCompressor|null $pdfImageCompressor
     */
    public function __construct(PdfImageCompressor $pdfImageCompressor = null)
    {
        $this->pdfImageCompressor = $pdfImageCompressor ?? $this->getDefaultPdfImageCompressor();
    }

    /**
     * @return PdfImageCompressor
     */
    protected function getDefaultPdfImageCompressor(): PdfImageCompressor
    {
        return new PdfImageCompressor(0, 0.25, [
            DeviceCMYK8PdfImageResizer::class => [
                'channelColorReductionLevel' => 16
            ]
        ]);
    }

    /**
     * @param $inputPath
     * @param $outputPath
     */
    public function compressFile($inputPath, $outputPath): void
    {
        $pdfDocument = new PdfDocument(file_get_contents($inputPath));
        //delete objects by header
        $toDeleteIdList = [];
        foreach ($pdfDocument->getObjectList() as $objectId => $object) {
            $header = $object->getHeader();
            if (preg_match('@^<</N 4/Filter/FlateDecode/Length \d+>>$@', $header) || false !== strpos($header, 'FORD_WDWL_Blocker')) {
                $toDeleteIdList[] = $objectId;
            }
        }
        foreach ($toDeleteIdList as $toDeleteId) {
            $pdfDocument->deleteObject($toDeleteId);
        }

        $pdfDocument = $this->pdfImageCompressor->compressPdfDocument($pdfDocument);
        file_put_contents($outputPath, $pdfDocument->getSource());
    }
}