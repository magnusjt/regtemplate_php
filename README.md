# regtemplate_php
PHP class for parsing a template into a regex, used for matching into name-value pairs.

## Usage

See also examples in the examples folder.

### Basic usage example:
````
$template = \RegTemplate\RegTemplate('some/dir');

# Parse a template from a file inside the base directory
$template->parse_template_from_file('file_inside_some_dir.txt');

# Or parse from a string:
# $template->parse('Some number: {{ number|digits }}');

# Match an output string against the template. Name=>value array returned.
$name_value = $template->match('Some number: 64');

# Match all. If the regex matches several times, return a list of list of name?>value arrays:
$match_list = $template->match_all('Some number: 64, Some number: 65');
````

### Example of a template:
````
Displaying numbers from index {{ index_name|word }}
Number of interesting events: {{ num_events|digits }}
Number of pages: {{ num_pages|digits }}
Status: {{ on_or_off|reg="(?:on|off)" }}
````

### Adding custom rules:

A rule is a name and a regex to match it.
If the regex has capturing groups, ensure that they are non-capturing like this: (?:).

````
$template->set_rule('digits', '\d+');
$template->set_rule('on_or_off', '(?:on|off)');

# Default rule if no rule is given in the template
$template->set_default_rule('\S+');
````

### Inline regexes

You can use inline regexes instead of rules in your template.
Remember to escape any double quotes with backslash.

````
$template->parse('{{ number|reg="\d\d\d" }}
````

### Ignoring whitespace (on by default):

Ignore any excess whitespace by replacing all whitespace with a single space.
You can turn this feature on/off like this:

````
$template->set_ignore_whitespace(true);
````

### Setting custom variable tokens (defaults to '{{' and '}}'):

If the string you want to match contains the default variable tokens,
you can change the tokens like this:

````
$template->set_variable_tokens('{{', '}}');
````

### Ignoring a variable (default ignore variable name 'any'):

Any variable with the name 'any' is skipped. It is matched, but not returned.
You can change the name to ignore like this:

````
$template->set_ignore_var_name('any');
````