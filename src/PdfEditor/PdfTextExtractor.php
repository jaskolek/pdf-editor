<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 31.07.2018
 * Time: 10:32
 */

namespace PdfEditor;


use PdfEditor\PdfObject\ImagePdfObject;
use PdfEditor\PdfObject\PdfObjectInterface;
use PdfEditor\PdfTextBlockParser\DecodedTextObject;
use PdfEditor\PdfTextBlockParser\PdfTextBlockParser;
use PdfEditor\TextDecoder\DummyTextDecoder;
use PdfEditor\TextDecoder\TextDecoderInterface;

/**
 * Class PdfTextExtractor
 * @package PdfEditor
 */
class PdfTextExtractor
{
    /**
     * @var PdfObjectManipulator
     */
    protected $pdfObjectManipulator;

    /**
     * @var TextEncoderFactory
     */
    protected $textEncoderFactory;

    /**
     * @var PdfTextBlockParser
     */
    protected $pdfTextBlockParser;

    /**
     * PdfTextExtractor constructor.
     * @param PdfObjectManipulator $pdfObjectManipulator
     * @param TextEncoderFactory $textEncoderFactory
     * @param PdfTextBlockParser|null $pdfTextBlockParser
     */
    public function __construct(PdfObjectManipulator $pdfObjectManipulator = null, TextEncoderFactory $textEncoderFactory = null, PdfTextBlockParser $pdfTextBlockParser = null)
    {
        $this->pdfObjectManipulator = $pdfObjectManipulator ?? $this->getDefaultPdfObjectManipulator();
        $this->textEncoderFactory = $textEncoderFactory ?? $this->getDefaultTextEncoderFactory();
        $this->pdfTextBlockParser = $pdfTextBlockParser ?? $this->getDefaultPdfTextBlockParser();
    }

    /**
     * @return PdfTextBlockParser
     */
    protected function getDefaultPdfTextBlockParser(): PdfTextBlockParser
    {
        return new PdfTextBlockParser();
    }

    /**
     * @return PdfObjectManipulator
     */
    protected function getDefaultPdfObjectManipulator(): PdfObjectManipulator
    {
        return new PdfObjectManipulator();
    }

    /**
     * @return TextEncoderFactory
     */
    protected function getDefaultTextEncoderFactory(): TextEncoderFactory
    {
        return new TextEncoderFactory();
    }

    /**
     * @param $path
     * @return DecodedTextObject[]
     */
    public function extractTextFromFile($path): array
    {
        $pdf = new PdfDocument(file_get_contents($path));
        $textObjectList = [];
        $objectList = $pdf->getObjectList();
        foreach ($objectList as $objectId => $pdfObject) {
            if (!($pdfObject instanceof ImagePdfObject)) {
                $newTextObjectList = $this->extractTextFromPdfObject($pdf, $pdfObject);
                foreach ($newTextObjectList as $newTextObject) {
                    $textObjectList[] = $newTextObject;
                }
            }
        }
        return $textObjectList;
    }

    /**
     * @param PdfDocument $pdfDocument
     * @param PdfObjectInterface $pdfObject
     * @return TextDecoderInterface[]
     */
    protected function getTextEncoderListFromPdfObject(PdfDocument $pdfDocument, PdfObjectInterface $pdfObject): array
    {
        //load fonts
        $textEncoderByNameList = [
            null => new DummyTextDecoder()
        ];
        if (preg_match('@/Font<<(.*?)>>@', $pdfObject->getHeader(), $matches)) {
            $fontStringList = explode('/', $matches[1]);
            array_shift($fontStringList);
            foreach ($fontStringList as $fontString) {
                [$fontName, $fontObjectId] = explode(' ', $fontString);
                $textEncoder = $this->textEncoderFactory->fromPdfFontObjectId($pdfDocument, $fontObjectId);
                $textEncoderByNameList[$fontName] = $textEncoder;
            }
        }

        return $textEncoderByNameList;
    }

    /**
     * @param PdfDocument $pdfDocument
     * @param PdfObjectInterface $pdfObject
     * @return DecodedTextObject[]
     */
    protected function extractTextFromPdfObject(PdfDocument $pdfDocument, PdfObjectInterface $pdfObject): array
    {
        $textEncoderByNameList = $this->getTextEncoderListFromPdfObject($pdfDocument, $pdfObject);
        $textObjectList = [];
        try {
            $stream = $this->getDefaultPdfObjectManipulator()->getDecodedStream($pdfObject);

            preg_match_all('@BT(.*?)ET@s', $stream, $matches, PREG_SET_ORDER);
        }catch (\Exception $exception){
            return [];
        }
        foreach ($matches as $key => $match) {
            $encodedTextObjectList = $this->pdfTextBlockParser->extractTextObjectList($match[1]);
            foreach ($encodedTextObjectList as $encodedTextObject) {
                $decodedTextObject = new DecodedTextObject();
                $decodedTextObject->options = $encodedTextObject->options;
                $fontId = $encodedTextObject->options->fontId;
                $textEncoder = $textEncoderByNameList[$fontId] ?? $textEncoderByNameList[null];

                $decodedTextObject->decodedText = $textEncoder->decode($encodedTextObject->encodedText);
                $textObjectList[] = $decodedTextObject;
            }
        }

        return $textObjectList;
    }
}