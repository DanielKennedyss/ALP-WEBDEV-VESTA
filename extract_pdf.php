<?php
// Simple PDF text extractor
$file = 'C:\\Users\\nicho\\Documents\\Kuliah\\Semester 4\\Web Design & Development\\WebDev Week 10.1.pdf';
$content = file_get_contents($file);

// Extract text between stream/endstream markers and decode
preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $content, $matches);

$text = '';
foreach ($matches[1] as $stream) {
    // Try to decompress
    $decoded = @gzuncompress($stream);
    if ($decoded === false) {
        $decoded = @gzinflate($stream);
    }
    if ($decoded !== false) {
        // Extract text from PDF content stream
        // Match text between BT and ET markers
        preg_match_all('/\((.*?)\)/', $decoded, $textMatches);
        foreach ($textMatches[1] as $t) {
            $text .= $t;
        }
        // Also match hex strings
        preg_match_all('/<([0-9A-Fa-f]+)>/', $decoded, $hexMatches);
        foreach ($hexMatches[1] as $hex) {
            $text .= hex2bin($hex);
        }
        $text .= "\n";
    }
}

echo $text;
