<?php 
private function getFirstImageFromPage($url)
{
    if (!$url) return '';

    // Use timeout to avoid slow response
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'Mozilla/5.0'
        ]
    ]);

    $html = @file_get_contents($url, false, $context);

    if (!$html) return '';

    // Try to get og:image first (BEST)
    preg_match('/<meta property="og:image" content="(.*?)"/i', $html, $og);
    if (!empty($og[1])) {
        return $og[1];
    }

    // Fallback: first <img>
    preg_match('/<img[^>]+src=["\'](.*?)["\']/i', $html, $img);

    return $img[1] ?? '';
}
?>