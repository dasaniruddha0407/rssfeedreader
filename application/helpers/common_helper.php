<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Get first image from webpage
 */
function get_first_image_from_page($url)
{
    if (!$url) return '';
   
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'Mozilla/5.0'
        ]
    ]);

    $html = @file_get_contents($url, false, $context);

    if (!$html) return '';

    // Try og:image first (best quality) if we want to match meta image 
     preg_match('/<meta property="og:image" content="(.*?)"/i', $html, $og);
        if (!empty($og[1])) {
            return $og[1];
        }
   

    //  Fallback: first <img>
   preg_match('/<img[^>]+(src|data-src)=["\']([^"\']+)["\']/i', $html, $img);

    return $img[2] ?? '';
}