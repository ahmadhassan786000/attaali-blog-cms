<?php

require __DIR__ . '/../includes/init.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array(
        'success' => false,
        'error' => 'Only POST requests are allowed.'
    ));
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$prompt = isset($input['prompt']) ? trim((string)$input['prompt']) : '';
$width = isset($input['width']) ? (int)$input['width'] : 1024;
$height = isset($input['height']) ? (int)$input['height'] : 1024;

if ($prompt === '') {
    http_response_code(400);
    echo json_encode(array(
        'success' => false,
        'error' => 'Please provide an image prompt.'
    ));
    exit;
}

$width = min(2048, max(256, $width));
$height = min(2048, max(256, $height));

/*
|--------------------------------------------------------------------------
| 1. TRY OLD POLLINATIONS
|--------------------------------------------------------------------------
*/

$oldUrl = 'https://image.pollinations.ai/prompt/'
    . rawurlencode($prompt)
    . '?width=' . $width
    . '&height=' . $height
    . '&nologo=true'
    . '&enhance=true'
    . '&seed=' . time();

$oldCh = curl_init($oldUrl);

curl_setopt($oldCh, CURLOPT_RETURNTRANSFER, true);
curl_setopt($oldCh, CURLOPT_TIMEOUT, 60);
curl_setopt($oldCh, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($oldCh, CURLOPT_USERAGENT, 'ATTAALI-Text-to-Image/1.0');

$oldImage = curl_exec($oldCh);
$oldStatus = curl_getinfo($oldCh, CURLINFO_HTTP_CODE);
$oldContentType = curl_getinfo($oldCh, CURLINFO_CONTENT_TYPE);
$oldCurlError = curl_error($oldCh);

curl_close($oldCh);

if (
    $oldImage !== false &&
    $oldStatus >= 200 &&
    $oldStatus < 300 &&
    strpos((string)$oldContentType, 'image/') === 0
) {
    echo json_encode(array(
        'success' => true,
        'image' => 'data:' . $oldContentType . ';base64,' . base64_encode($oldImage),
        'width' => $width,
        'height' => $height,
        'provider' => 'pollinations-old'
    ));
    exit;
}

/*
|--------------------------------------------------------------------------
| 2. LOAD NEW POLLINATIONS SECRET KEY
|--------------------------------------------------------------------------
*/

$secretFile = 'C:/xampp/attaali-secrets/pollinations.php';
$newApiKey = '';

if (file_exists($secretFile)) {
    $pollinationsConfig = require $secretFile;

    if (is_array($pollinationsConfig) && isset($pollinationsConfig['api_key'])) {
        $newApiKey = trim((string)$pollinationsConfig['api_key']);
    }
}

if ($newApiKey === '') {
    http_response_code(502);

    echo json_encode(array(
        'success' => false,
        'error' => 'Old Pollinations failed and the new Pollinations API key is not configured.',
        'old_provider_status' => $oldStatus,
        'old_provider_type' => $oldContentType,
        'old_provider_error' => $oldCurlError
    ));
    exit;
}

/*
|--------------------------------------------------------------------------
| 3. TRY NEW POLLINATIONS
|--------------------------------------------------------------------------
*/

$newUrl = 'https://gen.pollinations.ai/image/'
    . rawurlencode($prompt)
    . '?model=flux'
    . '&width=' . $width
    . '&height=' . $height
    . '&nologo=true'
    . '&enhance=true'
    . '&seed=' . time();

$newCh = curl_init($newUrl);

curl_setopt($newCh, CURLOPT_RETURNTRANSFER, true);
curl_setopt($newCh, CURLOPT_TIMEOUT, 60);
curl_setopt($newCh, CURLOPT_FOLLOWLOCATION, true);

curl_setopt($newCh, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $newApiKey,
    'Accept: image/*'
));

curl_setopt($newCh, CURLOPT_USERAGENT, 'ATTAALI-Text-to-Image/1.0');

$newImage = curl_exec($newCh);
$newStatus = curl_getinfo($newCh, CURLINFO_HTTP_CODE);
$newContentType = curl_getinfo($newCh, CURLINFO_CONTENT_TYPE);
$newCurlError = curl_error($newCh);

curl_close($newCh);

if (
    $newImage !== false &&
    $newStatus >= 200 &&
    $newStatus < 300 &&
    strpos((string)$newContentType, 'image/') === 0
) {
    echo json_encode(array(
        'success' => true,
        'image' => 'data:' . $newContentType . ';base64,' . base64_encode($newImage),
        'width' => $width,
        'height' => $height,
        'provider' => 'pollinations-new'
    ));
    exit;
}

/*
|--------------------------------------------------------------------------
| 4. BOTH FAILED
|--------------------------------------------------------------------------
*/

http_response_code(502);

echo json_encode(array(
    'success' => false,
    'error' => 'Both Pollinations image services failed.',
    'old_provider' => array(
        'status' => $oldStatus,
        'type' => $oldContentType,
        'error' => $oldCurlError
    ),
    'new_provider' => array(
        'status' => $newStatus,
        'type' => $newContentType,
        'error' => $newCurlError
    )
));

exit;
