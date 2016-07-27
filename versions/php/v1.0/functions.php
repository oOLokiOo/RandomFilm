<?php

function d($arr, $die = false) {
    echo '<pre>';
    print_r($arr);
    echo '</pre>';

    if ($die != false) die();
}

//https://developers.google.com/image-search/v1/jsondevguide
function get_from_images_google($search_words = '') {
    $manual_referer = 'http://google.com/';

    $args = array(
        'v' => '1.0',
        'q' => $search_words,
        'imgsz' => 'large',
        'rsz' => 8
    );

    $url = GOOGLE_IMAGES_URL;

    foreach ($args as $key => $val) {
        $url .= $key . '=' . rawurlencode($val) . '&';
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_REFERER, $manual_referer);
    
    $body = curl_exec($ch);
    $response = curl_getinfo($ch);

    $attempt = CURL_REQUEST_ATTEMPT;

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
        echo 'WARNING: CURL cant do request. Do something...';
        return false;
    }

    $json = json_decode($body, true);

    return $json['responseData']['results'];
}

function filter_from_blocked_resources($imges_arr = array(), $BLOCKED_RESOURCES = array()) {
    $good_url = '';

    for ($i = 0; $i < count($imges_arr); $i++) {
        if (!in_array($imges_arr[$i]['visibleUrl'], $BLOCKED_RESOURCES)) {
            $good_url = $imges_arr[$i]['url'];
            break;
        }
    }

    return (isset($imges_arr[0]['url']) && $good_url == '' ? $imges_arr[0]['url'] : $good_url); 
}