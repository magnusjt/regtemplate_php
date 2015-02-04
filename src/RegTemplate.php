<?php
namespace RegTemplate;

class RegTemplate{
    /** @var  string */
    protected $base_dir;

    /** @var  string */
    protected $var_start = '{{';

    /** @var string  */
    protected $var_end = '}}';

    /** @var bool  */
    protected $ignore_whitespace = true;

    /** @var string  */
    protected $rule_dflt = '\S+';

    /** @var string  */
    protected $ignore_var_name = 'any';

    /** @var  array */
    protected $rules = array(
        'digits'        => '\d+',
        'word'          => '\w+',
        'float'         => '\d+\.\d+',
        'notwhitespace' => '\S+',
        'whitespace'    => '\s+',
        'anything'      => '.*',
    );

    /** @var int  */
    protected $pos = 0;

    /** @var string  */
    protected $template = '';

    /** @var string  */
    protected $reg = '';

    /** @var string[]  */
    protected $names = array();

    public function __construct($base_dir = '/'){
        $this->base_dir = $base_dir;
    }

    public function set_variable_tokens($var_start = '{{', $var_end = '}}'){
        $this->var_start = $var_start;
        $this->var_end = $var_end;
    }

    /**
     * @param $rule_name  string
     * @param $rule_regex string
     */
    public function set_rule($rule_name, $rule_regex){
        $this->rules[$rule_name] = $rule_regex;
    }

    /**
     * @param $ignore bool
     *
     * Ignore excess whitespace
     * (essentially replace any type of whitespace with a single space)
     */
    public function set_ignore_whitespace($ignore = true){
        $this->ignore_whitespace = $ignore;
    }

    /**
     * @param $name string
     *
     * Any variable with the name set here
     * will not be returned in matches.
     *
     * Default: any
     */
    public function set_ignore_var_name($name){
        $this->ignore_var_name = $name;
    }

    /**
     * @param $default_rule string
     */
    public function set_default_rule($default_rule = '\S+'){
        $this->rule_dflt = $default_rule;
    }

    /**
     * @param $filename string
     *
     * @return $this
     */
    public function parse_template_from_file($filename){
        $this->parse_template(file_get_contents($this->base_dir . '/' . $filename));
        return $this;
    }

    /**
     * @param $template string
     *
     * @return $this
     * @throws \Exception
     */
    public function parse_template($template){
        $this->template = $template;
        $this->reg = '';
        $this->names = array();
        $this->pos = 0;
        $var_start_length = strlen($this->var_start);

        if($this->ignore_whitespace){
            $this->template = trim($this->template);
        }

        preg_match_all('/' . preg_quote($this->var_start, '/') . '/', $this->template, $var_matches, PREG_OFFSET_CAPTURE);
        foreach($var_matches[0] as $var_match){
            $var_pos = $var_match[1];
            $this->add_raw(substr($this->template, $this->pos, $var_pos - $this->pos));
            $this->pos = $var_pos + $var_start_length;
            $this->parse_var();
        }

        $template_length = strlen($template);
        if($this->pos < $template_length){
            $this->add_raw(substr($this->template, $this->pos, $template_length - $this->pos));
        }

        return $this;
    }

    /**
     * @param $str string
     *
     * @return array|bool
     */
    public function match($str){
        if($this->ignore_whitespace){
            $str = trim($str);
            $str = preg_replace('/\s+/', ' ', $str);
        }

        if(preg_match('/' . $this->reg . '/', $str, $matches)){
            $result = array();
            for($i = 1; $i < count($matches); $i++){
                $result[$this->names[$i-1]] = $matches[$i];
            }

            return $result;
        }

        return false;
    }

    /**
     * @param $str string
     *
     * @return array
     */
    public function match_all($str){
        if($this->ignore_whitespace){
            $str = trim($str);
            $str = preg_replace('/\s+/', ' ', $str);
        }

        preg_match_all('/' . $this->reg . '/', $str, $matches, PREG_SET_ORDER);

        $result = array();
        foreach($matches as $match_list){
            $d = array();
            for($i = 1; $i < count($match_list); $i++){
                $d[$this->names[$i-1]] = $match_list[$i];
            }

            $result[] = $d;
        }

        return $result;
    }

    protected function add_raw($raw){
        if($this->ignore_whitespace){
            $raw = preg_replace('/\s+/', ' ', $raw);
        }

        $this->reg .= preg_quote($raw, '/');
    }

    protected function parse_var(){
        $ignore_var = true;
        if(preg_match('/\s*(\w+)\s*/A', $this->template, $matches, null, $this->pos)){
            if($matches[1] != $this->ignore_var_name){
                $this->names[] = $matches[1];
                $ignore_var = false;
            }
            $this->pos += strlen($matches[0]);
        }else{
            throw new \Exception('Expected variable name, but got something else around: ' . $this->around());
        }

        if(preg_match('/\|\s*(\w+)\s*/A', $this->template, $matches, null, $this->pos)){
            $rule = $matches[1];
            if(!isset($this->rules[$rule])){
                throw new \Exception('Unknown rule: ' . $rule . ', around: ' . $this->around());
            }

            $rule = $this->rules[$rule];
            $this->pos += strlen($matches[0]);
        }else{
            $rule = $this->rule_dflt;
        }

        if($ignore_var){
            $this->reg .= $rule;
        }else{
            $this->reg .= '(' . $rule . ')';
        }

        if(preg_match('/' . preg_quote($this->var_end, '/') . '/A', $this->template, $matches, null, $this->pos)){
            $this->pos += strlen($matches[0]);
        }else{
            throw new \Exception('Expected end-of-variable token, but got something else. Around: ' . $this->around());
        }
    }

    protected function around(){
        return substr($this->template, $this->pos, 10);
    }
}