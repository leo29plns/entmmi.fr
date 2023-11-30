<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADE-ICS API Documentation</title>

    <style>
        code {
            background-color: peachpuff;
        }
    </style>
</head>
<body>

    <h1>ADE-ICS API</h1>

    <h2>Overview</h2>

    <p>This API allows you to fetch and parse iCalendar (ICS) data from ADE (Amphi, TD, Exam) schedules provided by educational institutions using a simple HTTP request. The API supports various parameters to customize your request and retrieve specific data.</p>

    <h2>API Endpoint</h2>

    <p>The API endpoint for fetching ADE iCalendar data is:</p>

    <code>http://entmmi.fr/api/ade-ics</code>

    <h2>Parameters</h2>

    <p>The API supports the following parameters:</p>

    <ul>
        <li><code>ical_url</code> (<i>required</i>): The URL of the ADE iCalendar feed.</li>
        <li><code>raw_data</code> (<i>optional, default: <code>false</code></i>): Whether to include raw data in the response.</li>
        <li><code>parsed_data</code> (<i>optional, default: <code>true</code></i>): Whether to include parsed data in the response.</li>
        <li><code>from</code> (<i>optional</i>): Start timestamp to filter events from a specific date.</li>
        <li><code>to</code> (<i>optional</i>): End timestamp to filter events until a specific date.</li>
        <li><code>group</code> (<i>optional</i>): Filter events by group.</li>
        <li><code>location</code> (<i>optional</i>): Filter events by location.</li>
    </ul>

    <h2>Response Format</h2>

    <p>The API response is in JSON format. If an error occurs, the response will include an <code>error</code> key with details about the error.</p>

    <h2>Examples</h2>

    <h3>PHP Example</h3>

    <pre>
&lt;?php

$apiUrl = 'http://entmmi.fr/api/ade-ics';

$data = array(
    'ical_url' => 'https://edt.univ-eiffel.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?resources=4905&projectId=25&calType=ical&nbWeeks=50',
    'raw_data' => false,
    'parsed_data' => true,
    // 'from' => strtotime('2023-12-18'),
    // 'to' => strtotime('2023-12-19'),
    // 'group' => 'A',
    // 'location' => 'IUC 121'
);

$options = [
    'http' => [
        'header' => "Content-type: application/json",
        'method' => 'POST',
        'content' => json_encode($data),
    ],
];

$context = stream_context_create($options);
$result = file_get_contents($apiUrl, false, $context);

$response = json_decode($result, true);

if (isset($response['error'])) {
    echo "API Error: " . $response['error']['message'];
} else {
    echo $result;
}
</pre>

    <h3>JavaScript Example</h3>

    <pre>
// Vanilla JavaScript Example
var apiUrl = 'http://entmmi.fr/api/ade-ics';

var requestData = {
  ical_url: 'https://edt.univ-eiffel.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?resources=4905&projectId=25&calType=ical&nbWeeks=50',
  raw_data: false,
  parsed_data: true,
  // from: new Date('2023-12-18').getTime() / 1000,
  // to: new Date('2023-12-19').getTime() / 1000,
  // group: 'A',
  // location: 'IUC 121',
};

var xhr = new XMLHttpRequest();
xhr.open('POST', apiUrl, true);
xhr.setRequestHeader('Content-type', 'application/json');

xhr.onload = function () {
  if (xhr.status >= 200 && xhr.status < 300) {
    var response = JSON.parse(xhr.responseText);
    if (response.error) {
      console.error('API Error: ' + response.error.message);
    } else {
      console.log(response);
    }
  } else {
    console.error(xhr.statusText);
  }
};

xhr.onerror = function () {
  console.error('Request failed');
};

xhr.send(JSON.stringify(requestData));
</pre>

</body>
</html>