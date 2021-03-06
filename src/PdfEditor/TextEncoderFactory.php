<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 28.07.2018
 * Time: 14:23
 */

namespace PdfEditor;


use PdfEditor\PdfObject\FontPdfObject;
use PdfEditor\PdfObject\PdfObjectInterface;
use PdfEditor\TextDecoder\MultiByteCharacterMapTextDecoder;
use PdfEditor\TextDecoder\DummyTextDecoder;

/**
 * Class TextEncoderFactory
 * @package PdfEditor
 */
class TextEncoderFactory
{
    /**
     * @var PdfObjectManipulator
     */
    protected $pdfObjectManipulator;

    /**
     * IdentityHTextEncoderFactory constructor.
     * @param PdfObjectManipulator|null $pdfObjectManipulator
     */
    public function __construct(PdfObjectManipulator $pdfObjectManipulator = null)
    {
        $this->pdfObjectManipulator = $pdfObjectManipulator ?? $this->getDefaultPdfObjectManipulator();
    }

    /**
     * @return PdfObjectManipulator
     */
    public function getDefaultPdfObjectManipulator(): PdfObjectManipulator
    {
        return new PdfObjectManipulator();
    }

    /**
     * @param PdfDocument $pdf
     * @param $fontObjectId
     * @return DummyTextDecoder|MultiByteCharacterMapTextDecoder
     */
    public function fromPdfFontObjectId(PdfDocument $pdf, $fontObjectId)
    {
        /** @var FontPdfObject $object */
        $object = $pdf->getObjectById($fontObjectId);
        return $this->fromPdfFontObject($pdf, $object);
    }

    /**
     * @param PdfDocument $pdf
     * @param FontPdfObject $pdfFontObject
     * @return DummyTextDecoder|MultiByteCharacterMapTextDecoder
     */
    public function fromPdfFontObject(PdfDocument $pdf, FontPdfObject $pdfFontObject)
    {
        $header = $pdfFontObject->getHeader();
        if (preg_match('@/ToUnicode\s+(\d+)\s+(\d+)\s+R@', $header, $matches)) {
            return $this->identityHFromToUnicodeMapObject($pdf->getObjectById($matches[1]));
        }

        if (preg_match('@/Encoding\s+(\d+)\s+(\d+)\s+R@', $header, $matches)) {
            return $this->fromDifferencesObject($pdf->getObjectById($matches[1]));
        }

        return new DummyTextDecoder();
    }

    /**
     * @param PdfObjectInterface $pdfObject
     * @return MultiByteCharacterMapTextDecoder
     */
    public function fromDifferencesObject(PdfObjectInterface $pdfObject): MultiByteCharacterMapTextDecoder
    {
        preg_match('@\[(.*?)\]@s', $pdfObject->getHeader(), $match);

        $groupList = explode(' ', $match[1]);
        $decCharacterMap = [];
        foreach ($groupList as $group) {
            $characterList = explode('/', $group);
            $start = (int)array_shift($characterList);
            foreach ($characterList as $key => $character) {
                $decCharacterMap[$key + $start] = (int)$character;
            }
        }

        return new MultiByteCharacterMapTextDecoder($decCharacterMap, 1);
    }

    /**
     * @param PdfObjectInterface $pdfObject
     * @return MultiByteCharacterMapTextDecoder
     */
    public function identityHFromToUnicodeMapObject(PdfObjectInterface $pdfObject): MultiByteCharacterMapTextDecoder
    {
        $text = $this->pdfObjectManipulator->getDecodedStream($pdfObject);

        $decCharacterMap = [];
        preg_match_all('@<([0-9A-F]+)>\s+<([0-9A-F]+)>\s+\[(.*?)\]@is', $text, $matches, PREG_SET_ORDER);

        foreach ($matches as [, $start, $end, $valueListString]) {
            $valueListString = trim(preg_replace('@\s+@', ' ', $valueListString));
            $valueList = explode(' ', $valueListString);
            $decValueList = array_map(function ($value) {
                return hexdec(substr($value, 1, -1));
            }, $valueList);

            $decStart = hexdec($start);

            foreach ($decValueList as $key => $decValue) {
                $decCharacterMap[$decStart + $key] = $decValue;
            }
        }

        return new MultiByteCharacterMapTextDecoder($decCharacterMap);
    }
}