<?php
/**
 * Created by PhpStorm.
 * User: jaskolek
 * Date: 27.07.2018
 * Time: 11:16
 */

namespace PdfEditor\PdfObject;


/**
 * Interface PdfObjectInterface
 * @package PdfEditor\PdfObject
 */
interface PdfObjectInterface
{
    /**
     * @return string
     */
    public function getInnerBody(): string;

    /**
     * @param string $newStream
     * @return PdfObjectInterface
     */
    public function withStream(string $newStream): PdfObjectInterface;

    /**
     * @param string $newHeader
     * @return PdfObjectInterface
     */
    public function withHeader(string $newHeader): PdfObjectInterface;

    /**
     * @return string
     */
    public function getStream(): string;

    /**
     * @return string
     */
    public function getHeader(): string;
}