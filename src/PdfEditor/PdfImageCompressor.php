<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 21.07.2018
 * Time: 18:57
 */

namespace PdfEditor;


use PdfEditor\PdfImageResizer\DeviceCMYK8PdfImageResizer;
use PdfEditor\PdfImageResizer\PdfImageResizerInterface;
use PdfEditor\PdfObject\ImagePdfObject;

/**
 * Class PdfCompressor
 * @package PdfEditor
 */
class PdfImageCompressor
{

    protected $pdfObjectManipulator;
    protected $minObjectSize;
    protected $ratio;
    protected $resizerConfig;

    /**
     * PdfImageCompressor constructor.
     * @param int $minObjectSize
     * @param float $ratio
     * @param array $resizerConfig
     * @param PdfObjectManipulator $pdfObjectManipulator
     */
    public function __construct($minObjectSize = 0, $ratio = 0.5, array $resizerConfig = [], PdfObjectManipulator $pdfObjectManipulator = null)
    {
        $this->minObjectSize = $minObjectSize;
        $this->ratio = $ratio;
        $this->resizerConfig = $resizerConfig;
        if($pdfObjectManipulator === null){
            $pdfObjectManipulator = new PdfObjectManipulator();
        }
        $this->pdfObjectManipulator = $pdfObjectManipulator;
    }


    /**
     * @param $input
     * @param $output
     */
    public function compressFile($input, $output): void
    {
        $source = file_get_contents($input);
        $compressedSource = $this->compressSource($source);

        file_put_contents($output, $compressedSource);
    }


    /**
     * @param PdfDocument $pdfDocument
     * @return PdfDocument
     */
    public function compressPdfDocument(PdfDocument $pdfDocument): PdfDocument
    {
        foreach($pdfDocument->getObjectList() as $objectId => $object){
            if($object instanceof ImagePdfObject){
                try {
                    if($object->getLength() < $this->minObjectSize){
                        continue;
                    }
                    $width = $object->getWidth();
                    $height = $object->getHeight();
                    $newWidth = ceil($width * $this->ratio);
                    $newHeight = ceil($height * $this->ratio);

                    $resizer = $this->getResizer($object);

                    $decodedStream = $this->pdfObjectManipulator->getDecodedStream($object);
                    $resizedDecodedStream = $resizer->resize($decodedStream, $width, $height, $newWidth, $newHeight);
                    /** @var ImagePdfObject $newObject */
                    $newObject = $this->pdfObjectManipulator->withDecodedStream($object, $resizedDecodedStream);
                    $newObject = $newObject->withDimensions($newWidth, $newHeight, \strlen($newObject->getStream()));

                    $pdfDocument->replaceObject($objectId, $newObject);
                }catch (\Exception $exception){

                }
            }
        }
        return $pdfDocument;
    }

    /**
     * @param $source
     * @return string
     */
    public function compressSource($source): string
    {
        $pdfDocument = new PdfDocument($source);
        $pdfDocument = $this->compressPdfDocument($pdfDocument);
        return $pdfDocument->getSource();
    }


    /**
     * @param ImagePdfObject $imagePdfObject
     * @return PdfImageResizerInterface
     * @throws \RuntimeException
     */
    protected function getResizer(ImagePdfObject $imagePdfObject): PdfImageResizerInterface
    {
        $header = $imagePdfObject->getHeader();
        if(strpos($header, '/BitsPerComponent 8') !== false && strpos($header, '/ColorSpace/DeviceCMYK') !== false){
            return new DeviceCMYK8PdfImageResizer(
                $this->resizerConfig[DeviceCMYK8PdfImageResizer::class]['channelColorReductionLevel']
            );
        }

        throw new \RuntimeException('Can not get resizer for object. Header: ' . $header);
    }
}