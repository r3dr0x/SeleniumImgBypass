<?php
require_once 'vendor/autoload.php';

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Chrome\ChromeOptions;
use thiagoalessio\TesseractOCR\TesseractOCR;

$host = "http://localhost:9515 /wd/hub";

$chromeOptions = new ChromeOptions();
$chromeOptions->addArguments(['--headless']);
$capabilities = DesiredCapabilities::chrome();
$capabilities->setCapability(ChromeOptions::CAPABILITY, $chromeOptions);

$driver = RemoteWebDriver::create($host, $capabilities);

$driver->get("XXXXXXXXXXX");

$imageBase64 = $driver->findElement(WebDriverBy::id("ctl00_ctl37_g_866b0f8a_3abe_4117_93d8_a540423922f8_ctl00_imgKod"))->getAttribute("src");

if (preg_match('/^data:image\/(\w+);base64,/', $imageBase64, $type)) {
    $imageBase64 = substr($imageBase64, strpos($imageBase64, ',') + 1);
    $fileType = strtolower($type[1]);
    $decodedImage = base64_decode($imageBase64);

    if ($decodedImage !== false) {
        file_put_contents("baypas.png", $decodedImage);
        echo "Resim başarıyla kaydedildi.";
    } else {
        echo "Base64 kodu dönüştürülemiyor.";
    }
} else {
    echo "Geçerli bir base64 kodu değil.";
}

$driver->quit();

$imagePath = realpath("baypas.png");

if (!$imagePath) {
    echo "Dosya bulunamadı: {$imagePath}";
    exit;
}

$customTesseractPath = 'C:\Users\_\AppData\Local\Programs\Tesseract-OCR\tesseract.exe';
$tessdataPath = 'C:\Users\_\AppData\Local\Programs\Tesseract-OCR\tessdata';

$ocr = new TesseractOCR($imagePath);
$ocr->executable($customTesseractPath);

putenv("TESSDATA_PREFIX={$tessdataPath}");

echo $ocr->run();
?>
