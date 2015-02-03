<?php
include __DIR__ . '/../vendor/autoload.php';

$template = new \RegTemplate\RegTemplate();

$template->parse_template('
Displaying numbers from index {{ index_name|word }}
Number of interesting events: {{ num_events|digits }}
Number of pages: {{ num_pages|digits }} blarg
');

$matches = $template->match('
Displaying numbers from index SuperIndex
Number of interesting events: 45678
Number of pages: 9876 blarg
');

var_dump($matches);