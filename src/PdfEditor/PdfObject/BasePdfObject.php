<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 27.07.2018
 * Time: 09:42
 */

namespace PdfEditor\PdfObject;


use PdfEditor\StringUtils;

/**
 * Class BasePdfObject
 * @package PdfEditor\PdfObject
 */
abstract class BasePdfObject implements PdfObjectInterface
{
    /**
     * @var string
     */
    protected $innerBody;
    /**
     * @var string
     */
    protected $header;
    /**
     * @var string
     */
    protected $stream;

    /**
     * AbstractPdfObject constructor.
     * @param $innerBody
     */
    public function __construct($innerBody)
    {
        $this->innerBody = $innerBody;

        $this->extractHeaderAndStream();
    }

    /**
     *
     */
    protected function extractHeaderAndStream(): void
    {
        $cleanedObjectString = StringUtils::deleteStringBetween($this->innerBody, 'stream', 'endstream');
        $header = '';
        if (preg_match('@<<(.*)>>@s', $cleanedObjectString, $headerMatches)) {
            $header = $headerMatches[0];
        }
        $stream = StringUtils::getStringBetween($this->innerBody, 'stream', 'endstream', false) ?? '';
        $stream = ltrim($stream);

        $this->header = trim($header);
        $this->stream = $stream;
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @return string
     */
    public function getStream(): string
    {
        return $this->stream;
    }

    /**
     * @return string
     */
    public function getInnerBody(): string
    {
        return $this->innerBody;
    }

    /**
     * @param $newStream
     * @return PdfObjectInterface
     */
    public function withStream($newStream): PdfObjectInterface
    {
        $innerBody = $this->strReplaceFirst($this->stream, $newStream, $this->innerBody);

        return new static($innerBody);
    }

    /**
     * @param $newHeader
     * @return PdfObjectInterface
     */
    public function withHeader($newHeader): PdfObjectInterface
    {
        $innerBody = $this->strReplaceFirst($this->header, $newHeader, $this->innerBody);

        return new static($innerBody);
    }

    /**
     * @param $search
     * @param $replace
     * @param $subject
     * @return string
     */
    protected function strReplaceFirst($search, $replace, $subject): string
    {
        $pos = strpos($subject, $search);
        if ($pos !== false) {
            return substr_replace($subject, $replace, $pos, \strlen($search));
        }
        return $subject;
    }
}