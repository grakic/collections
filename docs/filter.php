<?php

$source = file_get_contents($argv[1]);

// Translate @return to @retval
$source = str_replace('@return', '@retval', $source);

if(substr($argv[1], -4) == '.dox') {
    echo $source;
    exit;
}

// Translate "/** ... @var string */ public $x;" to "/** ... */ public string $x;" in class property docstring
$regexp = '#\@var\s+([^\s]+)([^/]+)/\s+(var|public|protected|private)\s+(\$[^\s;=]+)#';
$replac = '${2} */ ${3} ${1} ${4}';
$source = preg_replace($regexp, $replac, $source);

// Translate "@property string $x Comment" from class docstring to "/** Comment */ public string $x" class property declaration
$regexp_p = '#\@property\s+([^\s]+)\s+(\$[^\s*]+)\s+(.*)#';
$regexp_c = '#(/\*\*.*?\*/)\s*(class[^{]+\s*{)#s';
preg_match_all($regexp_c, $source, $classes, PREG_SET_ORDER);
foreach($classes as $class)
{
    preg_match_all($regexp_p, $class[1], $properties, PREG_SET_ORDER);
    foreach($properties as $property)
    {
        $comment = '';
        if($property[3]) {
            $comment = preg_replace("#<br/?>#", "\n     * ", $property[3]);
            $comment = "    /**\n     * ".$comment."\n     */\n";
        }
        $def = $comment."    public $property[1] $property[2];\n";
        $source = str_replace($property[0], "", $source);
        $source = str_replace($class[2], $class[2]."\n".$def, $source);
    }
}

echo $source;

