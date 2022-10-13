<?php
$ip = $_GET['ip'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

include __DIR__ . '/vendor/autoload.php';
$r = new \GeoIp2\Database\Reader(__DIR__ . '/GeoLite2-City.mmdb');


try {
    echo json_encode($r->city($ip)->jsonSerialize(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if (php_sapi_name() !== 'cli')
        header('Content-Type: application/json');
} catch (Exception $exception) {
    echo '查询失败';
}

