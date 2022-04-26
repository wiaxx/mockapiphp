<?php
$input = (array) json_decode(file_get_contents('php://input'), true);
$method = $_SERVER['REQUEST_METHOD'];

$price_info = [];

if ('POST' == $method) {
    $skus = $input['sku'] ?? [];

    if (in_array('98765', $skus)) {
        sleep(5);
    }

    if (in_array('501', $skus)) {
        http_response_code(501);
        echo '501 - Internal Server Error';
        exit;
    }

    $earlier_request = file_exists('requested_skus.txt') ? file_get_contents('requested_skus.txt') : '';
    $same_request = $earlier_request == json_encode($skus);

    if ($same_request && file_exists('cache.txt')) {
        header('Content-Type: application/json; charset=utf-8;');
        echo file_get_contents('cache.txt');
    } else {
        foreach ($skus as $sku) {
            $price = round(rand(300, 1200));
            $price_ex_vat = round($price / 1.25);

            $price_info[] = [
                'sku' => $sku,
                'vat' => 25,
                'priceExcVat' => $price_ex_vat,
                'priceIncVat' => $price,
                'priceExcVatFormatted' => 'SEK ' . number_format($price_ex_vat, 2),
                'priceIncVatFormatted' => 'SEK ' . number_format($price, 2)
            ];
        }

        file_put_contents('requested_skus.txt', json_encode($skus));
        file_put_contents('cache.txt', json_encode($price_info));
        header('Content-Type: application/json; charset=utf-8;');
        echo json_encode($price_info);
    }
} else if ('GET' == $method) {
    echo 'Please send an array of SKUs.';
} else {
    echo 'Invalid method';
}
