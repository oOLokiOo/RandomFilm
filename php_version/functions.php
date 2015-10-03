<?php

define('IMAGE_TYPE', 'jpg');
define('PARSER_RESOURCE_URL', 'http://ajax.googleapis.com/ajax/services/search/images?');
define('PARSER_CURL_REQUEST_ATTEMPT', 5);


function d($arr, $die = false) {
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
    if ($die != false) die();
}

function get_from_images_google($search_words, $start_point = 0) {
    $manual_referer = 'http://google.com/';

    $args = array(
        'v' => '1.0',
        'q' => $search_words,
        'as_filetype' => IMAGE_TYPE,
        'imgsz' => 'medium',
        'safe' => 'active',
        'as_filetype' => IMAGE_TYPE,
        'start' => $start_point,
    );

    $url = PARSER_RESOURCE_URL;
    foreach ($args as $key => $val) {
        $url .= $key . '=' . rawurlencode($val) . '&';
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_REFERER, $manual_referer);
    $body = curl_exec($ch);
    $response = curl_getinfo($ch);

    $attempt = PARSER_CURL_REQUEST_ATTEMPT;
    if ($response['http_code'] != 200) {
        while ($attempt > 0) {
            $body = curl_exec($ch);
            $response = curl_getinfo($ch);
            if ($response['http_code'] == 200) break;
            $attempt--;
        }
    }

    curl_close($ch);

    if ($attempt <= 0) {
        self::log('WARNING: CURL cant do request. Do something...');
        return false;
    }

    $json = json_decode($body, true);
    return $json['responseData']['results'];
}