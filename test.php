<?php

use PdfEditor\PdfDocumentCompressor\FordComWindowStickerPdfDocumentCompressor;
use PdfEditor\PdfImageCompressor;
use PdfEditor\PdfImageResizer\DeviceCMYK8PdfImageResizer;
use PdfEditor\PdfTextExtractor;

require_once __DIR__ . '/vendor/autoload.php';

ini_set('memory_limit', '2G');
$compressor = new FordComWindowStickerPdfDocumentCompressor(
    new PdfImageCompressor(0, 0.25, [
        DeviceCMYK8PdfImageResizer::class => [
            'channelColorReductionLevel' => 16
        ]
    ])
);

dump(memory_get_usage(), memory_get_usage(true), memory_get_peak_usage(), memory_get_peak_usage(true));
$compressor->compressFile(__DIR__ . '/1FM5K8DH6JGB32764.pdf', __DIR__ . '/out.pdf');
dump(memory_get_usage(), memory_get_usage(true), memory_get_peak_usage(), memory_get_peak_usage(true));

exit;

$extractor = new PdfTextExtractor();

$textObjectGroupList = $extractor->extractTextFromFile(__DIR__ . '/1FM5K8DH6JGB32764.pdf');

dump($textObjectGroupList);

//$textObjectList = $extractor->extractTextFromFile(__DIR__ . '/1FM5K8DH6JGB32764.pdf');

$image = imagecreatetruecolor(4000, 2000);
$bg = imagecolorallocate($image, 255,255,255);
imagefill($image, 0, 0, $bg);
$color = imagecolorallocate($image, 0, 0, 0);

$f = fopen(__DIR__ . '/out.csv', 'wb');
fputcsv($f, ['X', 'Y', 'ObjectId', 'InObjectId', 'FontSize', 'FontId', 'Text']);
foreach($textObjectGroupList as $objectId => $textObjectGroup){
    foreach($textObjectGroup as $inObjectId => $textObject) {
        $x = $textObject->options->x * 2;
        $y = 2000 - $textObject->options->y * 2;

        imagestring($image, 5, $x, $y, $textObject->decodedText, $color);

        fputcsv($f, [$x, $y, $objectId, $inObjectId, $textObject->options->fontSize, $textObject->options->fontId, $textObject->decodedText]);
    }
}

fclose($f);
imagepng($image, __DIR__ . '/out.png');