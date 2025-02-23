<?php
namespace App\Service;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeGenerator
{
    public function generate(string $content, int $id, string $prefix = 'therapie'): string
    {
        $qrCodePath = 'uploads/qrcode/' . $prefix . '-' . $id . '.png';
        if (file_exists($qrCodePath)) {
            return $qrCodePath;
        }

        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $content,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 150,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            logoPath: '',               
            logoResizeToWidth: null,      
            logoPunchoutBackground: false,
            labelText: '',
            labelFont: new OpenSans(20),
            labelAlignment: LabelAlignment::Center
        );

        $result = $builder->build();
        $result->saveToFile($qrCodePath);

        return $qrCodePath;
    }
}
