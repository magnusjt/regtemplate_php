<?php

class TestRegTemplateMatch extends PHPUnit_Framework_TestCase{
    /** @var  \RegTemplate\RegTemplate */
    public $regtemplate;


    public function setUp(){
        $this->regtemplate = new \RegTemplate\RegTemplate();
    }

    public function test_TemplateWithRegex_Match_ReturnsValue(){
        $this->regtemplate->parse_template('{{ number|reg="\d\d\d" }}');

        $matches = $this->regtemplate->match('234567');

        $this->assertEquals(true, isset($matches['number']));
        $this->assertEquals('234', $matches['number']);
    }

    public function test_TemplateWithQuotedRegex_Match_ReturnsValue(){
        $this->regtemplate->parse_template('{{ string|reg="\"Hello there\"" }}');

        $matches = $this->regtemplate->match('"Hello there"');

        $this->assertEquals(true, isset($matches['string']));
        $this->assertEquals('"Hello there"', $matches['string']);
    }

    public function test_TemplateWithCustomRule_Match_ReturnsValue(){
        $this->regtemplate->set_rule('on_or_off', '(?:on|off)');
        $this->regtemplate->parse_template('Hi, the status is {{ status|on_or_off }}');

        $matches = $this->regtemplate->match('Hi, the status is on');

        $this->assertEquals(true, isset($matches['status']));
        $this->assertEquals('on', $matches['status']);
    }

    public function test_TemplateWithIgnoreVar_Match_DoesntReturnValue(){
        $this->regtemplate->parse_template('{{ any|digits }}');

        $matches = $this->regtemplate->match('234556');

        $this->assertEquals(0, count($matches));
    }
}