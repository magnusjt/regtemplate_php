<?php
include __DIR__ . '/../vendor/autoload.php';

$template = new \RegTemplate\RegTemplate();

$template->parse_template('
There {{ is_are|reg="(?:is|are)" }} {{ number|digits }} instance{{ plural|reg="s?" }} of this.
');

$matches = $template->match('
There is 1 instance of this.
');

var_dump($matches);

$matches = $template->match('
There are 10 instances of this.
');

var_dump($matches);

$matches = $template->match_all('
There is 1 instance of this.
There are 20 instances of this.
There are 30 instances of this.
');

var_dump($matches);