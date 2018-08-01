<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 27.07.2018
 * Time: 11:12
 */

namespace PdfEditor;


use PdfEditor\PdfObject\FontPdfObject;
use PdfEditor\PdfObject\FormPdfObject;
use PdfEditor\PdfObject\ImagePdfObject;
use PdfEditor\PdfObject\PdfObjectInterface;
use PdfEditor\PdfObject\UnknownPdfObject;

/**
 * Class PdfDocument
 * @package PdfEditor
 */
class PdfDocument
{
    /**
     * @var
     */
    protected $source;

    /**
     * @var
     */
    protected $objectList = [];



    /**
     * PdfDocument constructor.
     * @param $source
     */
    public function __construct($source)
    {
        $this->source = $source;
        $this->reloadObjectList();
    }

    /**
     *
     */
    public function reloadObjectList():void
    {
        $objectListMatches = [];
        preg_match_all('@(\d+) (\d+) obj\s(.*?)\sendobj@s', $this->source, $objectListMatches, PREG_SET_ORDER);

        foreach ($objectListMatches as [, $objectId, , $objectString]) {
            $this->objectList[$objectId] = $this->createPdfObjectFromString($objectString);
        }
    }

    /**
     * @param $objectId
     */
    public function deleteObject($objectId): void
    {
        preg_match('@(' . $objectId . ') (\d+) obj\s+@', $this->source, $matches);
        $this->source = StringUtils::deleteStringBetween($this->source, $matches[0], 'endobj', true);

//        $this->source = preg_replace('@(' . $objectId . ') (\d+) obj\s(.*?)\sendobj@s', '', $this->source);
        unset($this->objectList[$objectId]);
    }

    /**
     * @param $objectId
     * @param PdfObjectInterface $pdfObject
     */
    public function replaceObject($objectId, PdfObjectInterface $pdfObject): void
    {
        preg_match('@(' . $objectId . ') (\d+) obj\s+@', $this->source, $matches);
        $innerBody = $pdfObject->getInnerBody();

        $this->source = StringUtils::replaceStringBetween($this->source, $innerBody, $matches[0], 'endobj', false);

        $this->reloadObjectList();
    }


    /**
     * @param $objectId
     * @return PdfObjectInterface
     */
    public function getObjectById($objectId): PdfObjectInterface
    {
        if(!isset($this->objectList[$objectId])){
            throw new \InvalidArgumentException('Can not find object with id ' . $objectId);
        }
        return $this->objectList[$objectId];
    }

    /**
     * @param $objectString
     * @return PdfObjectInterface
     */
    protected function createPdfObjectFromString($objectString): PdfObjectInterface
    {
        $cleanedObjectString = StringUtils::deleteStringBetween($objectString, 'stream', 'endstream');
        $header = '';
        if (preg_match('@<<(.*)>>@s', $cleanedObjectString, $headerMatches)) {
            $header = $headerMatches[0];
        }


        //depending on header, set different object
        if (strpos($header, '/Subtype/Image') !== false) {
            $object = new ImagePdfObject($objectString);
        } else if(strpos($header, '<</Type/Font') !== false){
            $object = new FontPdfObject($objectString);
        } else if(strpos($header, '<</Type/XObject/Subtype/Form') !== false){
            $object = new FormPdfObject($objectString);
        }else {
            $object = new UnknownPdfObject($objectString);
        }
        return $object;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return PdfObjectInterface[]
     */
    public function getObjectList(): array
    {
        return $this->objectList;
    }


}