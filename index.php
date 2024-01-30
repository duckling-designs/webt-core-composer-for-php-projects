<?php

//einmal autoload requiren
require_once 'vendor/autoload.php';

//von https://packagist.org/packages/endroid/qr-code
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;


function generateQrCode($input): \Endroid\QrCode\Writer\Result\ResultInterface
{
    return
        Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($input)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(20)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->backgroundColor(new \Endroid\QrCode\Color\Color(94, 234, 200))
            ->labelText('')
            ->labelAlignment(LabelAlignment::Center)
            ->validateResult(false)
            ->build();
}


$htmlStart = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>QR-Code Generator</title>
</head>
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap')
</style>
<body>
<main>
<h1 class="title">Generate a QR-Code!</h1>
<form class="inputForm" method="post">
    <input type="tel" name="inputValue" id="inputField" placeholder="text to generate" pattern="[+]?[0-9 ]+">
    <button type="submit" id="submitButton">generate</button>
</form>
HTML;

echo $htmlStart;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["inputValue"])) {
    $inputValue = $_POST["inputValue"];
    $qrCodeDataUrl = 'data:image/png;base64,' . base64_encode(generateQrCode($inputValue)->getString());
    // Return HTML content with QR code inside the original <div>
    $qrcodeBlock = <<<HTML
    <div class="qrcode" id="qrcodeContainer">
        <img src=$qrCodeDataUrl alt="qrcode">
        <p>QR Code for: $inputValue</p>
    </div>
    HTML;

    echo $qrcodeBlock;
}

$htmlEnd = <<<HTML
</main>
</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    let button = document.getElementById("submitButton");

    button.addEventListener("click", function(event) {
        let inputValue = document.getElementById("inputField").value.trim();
        if (!inputValue) {
            event.preventDefault(); // Prevent default form submission if input field is empty
            alert("Please enter text to generate QR code.");
        }
    });
});
</script>
</html>
HTML;

echo $htmlEnd;
