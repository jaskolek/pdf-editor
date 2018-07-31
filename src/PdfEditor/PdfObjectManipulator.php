<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 28.07.2018
 * Time: 14:08
 */

namespace PdfEditor;


use PdfEditor\PdfFilter\DummyPdfFilter;
use PdfEditor\PdfFilter\FlateDecodePdfFilter;
use PdfEditor\PdfFilter\PdfFilterInterface;
use PdfEditor\PdfObject\PdfObjectInterface;

/**
 * Class PdfObjectManipulator
 * @package PdfEditor
 */
class PdfObjectManipulator
{
    /**
     * @param PdfObjectInterface $pdfObject
     * @return string
     */
    public function getDecodedStream(PdfObjectInterface $pdfObject): string
    {
        $filter = $this->getFilter($pdfObject);
        return $filter->decode($pdfObject->getStream());
    }

    /**
     * @param PdfObjectInterface $pdfObject
     * @param $decodedStream
     * @return PdfObjectInterface
     */
    public function withDecodedStream(PdfObjectInterface $pdfObject, $decodedStream): PdfObjectInterface
    {
        $filter = $this->getFilter($pdfObject);
        $encodedStream = $filter->encode($decodedStream);
        return $pdfObject->withStream($encodedStream);
    }

    /**
     * @param PdfObjectInterface $pdfObject
     * @return PdfFilterInterface
     */
    protected function getFilter(PdfObjectInterface $pdfObject): PdfFilterInterface
    {
        if (!preg_match('@/Filter\s*@', $pdfObject->getHeader())) {
            return new DummyPdfFilter();
        }

        if (preg_match('@/Filter\s*/FlateDecode\s*@', $pdfObject->getHeader())) {
            return new FlateDecodePdfFilter();
        }

        throw new \InvalidArgumentException('Can not obtain filter for header ' . $pdfObject->getHeader());
    }
}