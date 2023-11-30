<?php

namespace lightframe\Controllers;

class Api
{
    private $type;

    public function ade_ics() : void
    {
        $this->type = 'JSON';

        $inputJson = file_get_contents("php://input");

        if ($inputJson === false) {
            self::apiError($this->type, 404, 'Can\'t reach file requested url (ical_url).');
        }
    
        $data = json_decode($inputJson, true);
    
        if ($data === null) {
            self::apiError($this->type, 404, 'Can\'t decode JSON from ical_url.');
        }
    
        if (!array_key_exists('ical_url', $data)) {
            self::apiError($this->type, 404, 'You must specify a calendar URL.');
        }

        if (!array_key_exists('raw_data', $data) && !array_key_exists('parsed_data', $data)) {
            $data['parsed_data'] = true;
            $data['raw_data'] = false;
        }

        if (!array_key_exists('raw_data', $data)) {
            $data['raw_data'] = false;
        }

        if (!array_key_exists('parsed_data', $data)) {
            $data['parsed_data'] = false;
        }
    
        $icalUrl = $data["ical_url"];
        $rawData = $data["raw_data"];
        $parsedData = $data["parsed_data"];


        require('Controllers' . DIRECTORY_SEPARATOR . 'Api' . DIRECTORY_SEPARATOR . 'IcsParser.php');
        $adeIcsParser = new \lightframe\Controllers\Api\IcsParser();

        $adeIcsParser->fetchUrl($icalUrl);

        if (isset($data["from"])) {
            $adeIcsParser->setFrom($data["from"]);
        }
        
        if (isset($data["to"])) {
            $adeIcsParser->setTo($data["to"]);
        }

        if (isset($data["group"])) {
            $adeIcsParser->setGroup($data["group"]);
        }

        if (isset($data["location"])) {
            $adeIcsParser->setLocation($data["location"]);
        }

        $response = '';

        if ($rawData && $parsedData) {
            $response = $adeIcsParser->eventsReconciliation();
        } elseif ($rawData) {
            $response = $adeIcsParser->parseRawEvents();
        } elseif ($parsedData) {
            $response = $adeIcsParser->parseParsedEvents();
        } else {
            self::apiError($this->type, 404, 'At least one of the parameters must be True (raw_data or parsed_data).');
        }

        self::returnHeader($this->type, ['POST'], '*');
        echo json_encode($response);
    }

    public function ade_ics_manual() : void
    {
        require('html' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'ade-ics-manual.php');
    }

    public function ade_ics_options() : void
    {
        self::returnHeader('JSON', ['POST'], '*');
    }

    public static function returnHeader(string $type, array $methods = [], string $origin = null) : void
    {
        switch ($type) {
            case 'JSON':
                header('Content-Type: application/json');
                break;
            default:
                header('Content-Type: text/plain');
                break;
        }

        if ($origin !== null) {
            header('Access-Control-Allow-Origin: ' . ($origin === '*' ? '*' : $origin));
        }

        if (!empty($methods)) {
            header('Access-Control-Allow-Methods: ' . implode(', ', $methods));
        }

        header('Access-Control-Allow-Headers: Content-Type');
    }

    public static function apiError($responseType, $errorCode, $errorMessage) {
        http_response_code($errorCode);

        switch ($responseType) {
            case 'JSON':
                header('Content-Type: application/json');
                echo json_encode(['error' => ['code' => $errorCode, 'message' => $errorMessage]]);
                break;
            default:
                header('Content-Type: text/html');
                echo "<h1>Error $errorCode</h1><p>$errorMessage</p>";
        }

        exit();
    }
}