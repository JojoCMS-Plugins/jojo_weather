<?php

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

require_once(_PLUGINDIR . '/jojo_weather/jojo_weather.php');
$html = JOJO_Plugin_jojo_weather::_getContent();

echo json_encode($html['content']);
