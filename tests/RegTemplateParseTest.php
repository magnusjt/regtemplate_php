<?php

class TestRegTemplateParse extends PHPUnit_Framework_TestCase{
    /** @var  \RegTemplate\RegTemplate */
    public $regtemplate;


    public function setUp(){
        $this->regtemplate = new \RegTemplate\RegTemplate();
    }

    public function syntaxErrorTemplateProvider(){
        return [
            ['Empty variable',                            '{{ }}'],
            ['Unbalanced bracket right',                  '{{ number }'],
            ['Unknown rule',                              '{{ name|flargenblargen }}'],
            ['Regex missing end quote',                   '{{ name|reg="\d\d }}'],
            ['Missing variable name',                     '{{ |digits }}']
        ];
    }

    public function legalTemplateProvider(){
        return [
          ['Empty template',                            ''],
          ['Unbalanced bracket left (parsed as raw)',   '{ number }}'],
          ['Variable',                                  '{{ number }}'],
          ['Variable with rule digits',                 '{{ number|digits}}'],
          ['Variable with rule word',                   '{{number|word }}'],
          ['Variable with custom regex',                '{{ number|reg="\d\d" }}'],
          ['Variable with custom regex, escaped quote', '{{ number|reg="\"" }}'],
          ['Two variables',                             '{{ blargen1 }}{{ blargen2 }}']
        ];
    }

    /**
     * @dataProvider syntaxErrorTemplateProvider
     */
    public function test_IllegalTemplate_ParseTemplate_SyntaxError($description, $template){
        try{
            $this->regtemplate->parse_template($template);
        }catch(\RegTemplate\SyntaxError $e){
            return;
        }

        $this->fail('SyntaxError not thrown: ' . $description);
    }

    /**
     * @dataProvider legalTemplateProvider
     */
    public function test_LegalTemplate_ParseTemplate_NoException($description, $template){
        try{
            $this->regtemplate->parse_template($template);
        }catch(\RegTemplate\SyntaxError $e){
            $this->fail('Legal template resulted in syntax error during parse: ' . $description . ', Exception: ' . $e->getMessage());
        }catch(Exception $e){
            $this->fail('Legal template resulted in exception during parse: ' . $description . ', Exception: ' . $e->getMessage());
        }
    }

    public function test_TemplateWithCustomTags_Parse_NoException(){
        $this->regtemplate->set_variable_tokens('<<', '>>');

        $this->regtemplate->parse_template('<<myvariable|digits>>');
    }
}