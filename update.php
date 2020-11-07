	<?php
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
    require __DIR__ .'/../../../../wp/wp-load.php';

} elseif (file_exists(__DIR__ . '/../../../autoload.php')) {
    require __DIR__ . '/../../../autoload.php';
    require __DIR__ .'/../../../wp/wp-load.php';
} elseif (file_exists(__DIR__ . '/../autoload.php')) {
    require __DIR__ . '/../autoload.php';
    require __DIR__ .'/../wp/wp-load.php';
} else {
    throw new RuntimeException('Unable to locate autoload.php file.');
}

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}
//Reading data from spreadsheet.
$client = new \Google_Client();
$client->setApplicationName('Google Sheets and PHP');
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');
$client->setAuthConfig(__DIR__ . '/credentials.json');
$service = new Google_Service_Sheets($client);
$data   = unserialize(base64_decode($argv[1]));

$spreadsheetId = "sheetID"; //sheet ID - had to be removed due to privacy
$get_range = "Sheet1!A:D";//Range of values
//Request to get data from spreadsheet.
$response = $service->spreadsheets_values->get($spreadsheetId, $get_range);
$values = $response->getValues();

//Get the next empty row
$update_cell = end(array_keys($values))+2;
//update the range using empty cell above
$update_range = "Sheet1!A$update_cell";
$value1 = $data["orderID"];
$value2 = $data["error"];
$value3 = "";
$value4 = $data["date"];
//create array for values
$values = [[$value1, $value2, $value3, $value4]];

$body = new Google_Service_Sheets_ValueRange([
    'values' => $values
]);
$params = [
    'valueInputOption' => 'RAW'
];
//update sheet
$update_sheet = $service->spreadsheets_values->update($spreadsheetId, $update_range, $body, $params);
?> 
