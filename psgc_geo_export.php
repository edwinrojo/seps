<?php

// Ensure storage/app/geo directory exists
$geoDir = __DIR__ . '/storage/app/geo/';
if (!is_dir($geoDir)) {
    mkdir($geoDir, 0755, true);
}

// Helper function to fetch JSON from a URL
function fetchJson($url) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            'User-Agent: Mozilla/5.0 (compatible; SEPS/1.0)',
            'Accept: application/json',
        ],
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true);
}

// Fetch provinces
$provinces = fetchJson('https://psgc.gitlab.io/api/provinces');
file_put_contents($geoDir . 'provinces.json', json_encode($provinces, JSON_PRETTY_PRINT));

// Fetch municipalities for each province
if (!is_array($provinces)) {
    echo "Failed to fetch provinces.\n";
    exit(1);
}
$municipalities = [];
foreach ($provinces as $province) {
    $provinceCode = $province['code'];
    $munis = fetchJson("https://psgc.gitlab.io/api/provinces/{$provinceCode}/cities-municipalities");
    $municipalities[$provinceCode] = $munis;
}
file_put_contents($geoDir . 'municipalities.json', json_encode($municipalities, JSON_PRETTY_PRINT));

// Fetch barangays for each municipality
if (!is_array($municipalities)) {
    echo "Failed to fetch municipalities.\n";
    exit(1);
}
$barangays = [];
foreach ($municipalities as $provinceCode => $munis) {
    foreach ($munis as $muni) {
        $muniCode = $muni['code'];
        $brgys = fetchJson("https://psgc.gitlab.io/api/cities-municipalities/{$muniCode}/barangays/");
        $barangays[$muniCode] = $brgys;
    }
}
file_put_contents($geoDir . 'barangays.json', json_encode($barangays, JSON_PRETTY_PRINT));

echo "Export complete! JSON files saved in storage/app/geo/\n";
