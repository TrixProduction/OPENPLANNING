<?php
// Simple server-side fetcher for ICS to bypass CORS
// Usage: fetch_ics.php?url=ENCODED_ICS_URL

// Allow only GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  http_response_code(405);
  header('Content-Type: text/plain; charset=UTF-8');
  echo 'Method Not Allowed';
  exit;
}

$url = isset($_GET['url']) ? $_GET['url'] : '';
if (!$url) {
  http_response_code(400);
  header('Content-Type: text/plain; charset=UTF-8');
  echo 'Missing url parameter';
  exit;
}

// Basic URL validation
if (!filter_var($url, FILTER_VALIDATE_URL)) {
  http_response_code(400);
  header('Content-Type: text/plain; charset=UTF-8');
  echo 'Invalid URL';
  exit;
}

$scheme = parse_url($url, PHP_URL_SCHEME);
if ($scheme !== 'http' && $scheme !== 'https') {
  http_response_code(400);
  header('Content-Type: text/plain; charset=UTF-8');
  echo 'Unsupported URL scheme';
  exit;
}

function fetch_via_curl($url) {
  if (!function_exists('curl_init')) return false;
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_USERAGENT, 'OpenPlanning/1.0 (+https://prodtrix.fr)');
  $out = curl_exec($ch);
  $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err = curl_errno($ch) ? curl_error($ch) : '';
  curl_close($ch);
  if ($out === false || $status < 200 || $status >= 300) {
    return [false, $status ?: 502, $err ?: 'Upstream fetch failed'];
  }
  return [$out, 200, ''];
}

// Try cURL first
list($content, $code, $err) = fetch_via_curl($url);
if ($content === false) {
  // Fallback to file_get_contents
  $ctx = stream_context_create([
    'http' => [
      'method' => 'GET',
      'timeout' => 15,
      'header' => "User-Agent: OpenPlanning/1.0 (+https://prodtrix.fr)\r\n",
      'ignore_errors' => true,
    ],
    'https' => [
      'method' => 'GET',
      'timeout' => 15,
      'header' => "User-Agent: OpenPlanning/1.0 (+https://prodtrix.fr)\r\n",
      'ignore_errors' => true,
    ],
  ]);
  $out = @file_get_contents($url, false, $ctx);
  if ($out === false) {
    http_response_code(502);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Proxy fetch failed';
    exit;
  }
  $content = $out;
}

// Success
header('Content-Type: text/calendar; charset=UTF-8');
header('Cache-Control: no-store');
// Optional: allow CORS for clients other than this origin (not strictly needed if same-origin fetch)
header('Access-Control-Allow-Origin: *');
echo $content;
