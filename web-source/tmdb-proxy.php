<?php
/**
 * Decan Movie - TMDB Proxy
 *
 * Keeps the TMDB access token server-side.
 * Compatible with PHP 7.4+ and PHP 8.x.
 */

declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

/*
|--------------------------------------------------------------------------
| Load configuration
|--------------------------------------------------------------------------
*/

$configFile = __DIR__ . '/config.php';

if (!file_exists($configFile)) {
    http_response_code(500);

    echo json_encode([
        'error' => true,
        'message' => 'config.php not found.'
    ]);

    exit;
}

require_once $configFile;

/*
|--------------------------------------------------------------------------
| Check TMDB token
|--------------------------------------------------------------------------
*/

if (
    !defined('TMDB_ACCESS_TOKEN') ||
    trim((string) TMDB_ACCESS_TOKEN) === '' ||
    TMDB_ACCESS_TOKEN === 'YOUR_NEW_TMDB_ACCESS_TOKEN' ||
    TMDB_ACCESS_TOKEN === 'YOUR_TMDB_ACCESS_TOKEN'
) {
    http_response_code(500);

    echo json_encode([
        'error' => true,
        'message' => 'TMDB_ACCESS_TOKEN is not configured in config.php.'
    ]);

    exit;
}

$token = trim((string) TMDB_ACCESS_TOKEN);

/*
|--------------------------------------------------------------------------
| Determine requested TMDB path
|--------------------------------------------------------------------------
|
| Supported:
|
| /tmdb-proxy.php/trending/movie/day
|
| /tmdb-proxy.php/movie/550
|
| /tmdb-proxy.php/search/movie?query=avatar
|
| /tmdb-proxy.php?path=/movie/550
|
|--------------------------------------------------------------------------
*/

$tmdbPath = '';

/*
 * First allow ?path=
 */
if (isset($_GET['path']) && $_GET['path'] !== '') {
    $tmdbPath = (string) $_GET['path'];
}

/*
 * Otherwise inspect REQUEST_URI.
 */
if ($tmdbPath === '') {

    $requestUri = isset($_SERVER['REQUEST_URI'])
        ? (string) $_SERVER['REQUEST_URI']
        : '';

    $path = parse_url($requestUri, PHP_URL_PATH);

    if (!is_string($path)) {
        $path = '';
    }

    /*
     * Find tmdb-proxy.php anywhere in the path.
     *
     * Example:
     * /tmdb-proxy.php/movie/550
     * /api/tmdb-proxy.php/movie/550
     */
    $marker = 'tmdb-proxy.php';

    $position = strpos($path, $marker);

    if ($position !== false) {

        $afterProxy = substr(
            $path,
            $position + strlen($marker)
        );

        if ($afterProxy !== false) {
            $tmdbPath = $afterProxy;
        }
    }
}

/*
|--------------------------------------------------------------------------
| Fallback to PATH_INFO
|--------------------------------------------------------------------------
*/

if ($tmdbPath === '') {

    if (
        isset($_SERVER['PATH_INFO']) &&
        $_SERVER['PATH_INFO'] !== ''
    ) {
        $tmdbPath = (string) $_SERVER['PATH_INFO'];
    }
}

/*
|--------------------------------------------------------------------------
| Clean path
|--------------------------------------------------------------------------
*/

$tmdbPath = '/' . ltrim($tmdbPath, '/');

/*
 * Remove accidental query string.
 */
$tmdbPath = explode('?', $tmdbPath, 2)[0];

/*
|--------------------------------------------------------------------------
| Security: don't allow arbitrary URLs
|--------------------------------------------------------------------------
*/

if (
    strpos($tmdbPath, 'http://') === 0 ||
    strpos($tmdbPath, 'https://') === 0 ||
    strpos($tmdbPath, '//') === 0
) {
    http_response_code(400);

    echo json_encode([
        'error' => true,
        'message' => 'Invalid TMDB path.'
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Only allow TMDB API paths
|--------------------------------------------------------------------------
*/

$allowedPrefixes = [
    '/movie',
    '/tv',
    '/search',
    '/discover',
    '/trending',
    '/person',
    '/genre',
    '/configuration',
    '/collection',
    '/keyword',
    '/company',
    '/network',
    '/credit'
];

$allowed = false;

foreach ($allowedPrefixes as $prefix) {

    if (
        $tmdbPath === $prefix ||
        strpos($tmdbPath, $prefix . '/') === 0
    ) {
        $allowed = true;
        break;
    }
}

if (!$allowed) {

    http_response_code(404);

    echo json_encode([
        'error' => true,
        'message' => 'TMDB endpoint not allowed.',
        'path' => $tmdbPath
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| TMDB URL
|--------------------------------------------------------------------------
*/

$tmdbUrl = 'https://api.themoviedb.org/3' . $tmdbPath;

/*
|--------------------------------------------------------------------------
| Preserve GET parameters
|--------------------------------------------------------------------------
*/

$query = $_GET;

unset($query['path']);

if (!empty($query)) {

    $queryString = http_build_query(
        $query,
        '',
        '&',
        PHP_QUERY_RFC3986
    );

    if ($queryString !== '') {
        $tmdbUrl .= '?' . $queryString;
    }
}

/*
|--------------------------------------------------------------------------
| Request TMDB
|--------------------------------------------------------------------------
*/

$headers = [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
];

/*
|--------------------------------------------------------------------------
| cURL
|--------------------------------------------------------------------------
*/

if (function_exists('curl_init')) {

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $tmdbUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_USERAGENT => 'Decan-Movie-TMDB-Proxy/1.0'
    ]);

    $response = curl_exec($ch);

    $curlError = curl_error($ch);

    $httpCode = (int) curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    curl_close($ch);

    if ($response === false) {

        http_response_code(502);

        echo json_encode([
            'error' => true,
            'message' => 'Unable to connect to TMDB.',
            'details' => $curlError
        ]);

        exit;
    }

} else {

    /*
     * Fallback for servers without cURL.
     */

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 30,
            'ignore_errors' => true,
            'header' =>
                "Authorization: Bearer " . $token . "\r\n" .
                "Accept: application/json\r\n" .
                "User-Agent: Decan-Movie-TMDB-Proxy/1.0\r\n"
        ]
    ]);

    $response = @file_get_contents(
        $tmdbUrl,
        false,
        $context
    );

    if ($response === false) {

        http_response_code(502);

        echo json_encode([
            'error' => true,
            'message' => 'Unable to connect to TMDB. Your hosting may have outbound HTTP disabled.'
        ]);

        exit;
    }

    $httpCode = 200;

    if (
        isset($http_response_header) &&
        is_array($http_response_header)
    ) {

        foreach ($http_response_header as $header) {

            if (preg_match(
                '/HTTP\/\S+\s+(\d+)/',
                $header,
                $matches
            )) {
                $httpCode = (int) $matches[1];
                break;
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| Return TMDB response
|--------------------------------------------------------------------------
*/

if ($httpCode < 200 || $httpCode >= 300) {

    http_response_code(
        $httpCode > 0 ? $httpCode : 502
    );

    /*
     * Pass TMDB's actual JSON response through.
     */
    echo $response;

    exit;
}

http_response_code(200);

echo $response;
