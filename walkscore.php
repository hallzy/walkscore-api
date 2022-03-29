<?php

// The address of the place you want to get a walkscore for. This can be
// anything that you would normally search for in the search box on
// walkscore.com
$address = $_GET['addr'] ?? NULL;

if (!$address) {
    respond('Missing Address');
}

// Massage address so I can use it with the walkscore website
$address = str_replace('#', '', $address);
$address = str_replace(' ', '-', $address);

// Attempt to get a response from walkscore. Try up to 10 times before giving
// up, sometimes it fails a couple times.
$url = "https://www.walkscore.com/score/{$address}";
for ($i = 0; $i < 10; $i++) {
    $page = @file_get_contents($url);
    if ($page) {
        break;
    }
}

if (!$page) {
    respond(
        [
            err => 'Failed to retrieve Walk Score',
            url => $url,
            addr => $address,
        ]
        );
}

$matches = [];

// Find the walkscore, transit score, and bike score of the address.
preg_match("/(\d+) Walk Score of /", $page, $matches);
$walkScore = isset($matches[1]) ? intval($matches[1]) : NULL;

preg_match("/(\d+) Transit Score of /", $page, $matches);
$transitScore = isset($matches[1]) ? intval($matches[1]) : NULL;

preg_match("/(\d+) Bike Score of /", $page, $matches);
$bikeScore = isset($matches[1]) ? intval($matches[1]) : NULL;

// Respond with the scores and the walkscore URL that gives the results
respond(
    [
        'walkScore'    => $walkScore,
        'transitScore' => $transitScore,
        'bikeScore'    => $bikeScore,
        'url'          => $url,
    ]
);

function respond($data)
{
    // The optional variable name to store the results into.
    $variableName = $_GET['variable'] ?? NULL;

    $isJsonp = isset($variableName);
    $code = 200;

    // If a string, then assume it is an error
    if (is_string($data))
    {
        $data = [ 'err' => $data ];
        $code = 400;
    }
    else if (array_key_exists('err', $data))
    {
        $code = 400;
    }

    // Provide JSONP support
    if ($isJsonp)
    {
        header('Content-Type: application/javascript');
        echo "window.{$variableName} = " . json_encode($data) . ";";
    }
    else
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    http_response_code($code);
    die();
}
