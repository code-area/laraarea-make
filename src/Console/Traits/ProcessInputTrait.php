<?php

namespace LaraAreaMake\Console\Traits;

use Illuminate\Support\Arr;

trait ProcessInputTrait
{
    /**
     * @var array
     */
    public $backslashKeywords = [];

    /**
     * @var array
     */
    public $defaultBackslashKeywords = [
        'path'
    ];

    /**
     * Automatically confirm all confirm box
     *
     * @var
     */
    protected $__confirm;

    /**
     * @var
     */
    protected $__confirmDefault = true;

    /**
     * @var
     */
    protected $__confirmBackslash;

    /**
     * Enable to choice default
     *
     * @var
     */
    protected $__choiceDefault;

    /**
     * Choice count with choice, after it work not continued
     *
     * @var int
     */
    public $choiceCount = 3;

    /**
     * For emphasis user input start input
     *
     * @var string
     */
    public $attentionStructure = '"%s"';

    /**
     * All parts of input must be process by methods
     *
     * keyword => method
     * keyword => [methods,...]
     *
     * @TODO, validate
     * @var array
     */
    public $processKeywordBackSlashParts = [

    ];
    /**
     * @var array
     */
    public $defaultKeywordBackSlashParts = [
        'pattern' => [
            '\Illuminate\Support\Str::camel',
            'ucfirst',
        ]
    ];

    /**
     * All input as whole expect suffix part must be process by methods
     *
     * keyword => method
     * keyword => [methods...]
     *
     * @TODO, validate
     * @var string
     */
    public $processKeyword = [
        'pattern' => [
            '\Illuminate\Support\Str::camel',
            'ucfirst',
        ]
    ];

    /**
     * All keyword must be end in this suffix if there partially match in suffix latest $checkSuffixLength count
     *
     * @var string
     */
    public $processKeywordSuffix = [];

    /**
     * It must be checked if partially suffix ends match the initial letters
     *
     * @var int
     */
    protected $checkSuffixLength = 3;

    /**
     * @var string
     */
    protected $processInputMethod = 'process%sInput';

    /**
     * @param $value
     * @return bool
     */
    protected function processConfirmDefaultInput($value)
    {
        if ($value === 0 || $value == 'false' || $value == 'no') {
            $this->__confirmDefault = false;
            return false;
        }

        $this->__confirmDefault = true;
        return true;
    }

    /**
     * @param $key
     * @param $value
     * @return array|string
     */
    public function processInput($key, $value)
    {
        $value = $this->_processInput($key, $value);
        return $this->finalProcessInput($key, $value);
    }

    /**
     * @param $key
     * @param $value
     * @return array|string
     */
    public function _processInput($key, $value)
    {
        if (is_array($value)) {
            $values = [];
            foreach ($value as $_key => $_value) {
                $values[$_key] = $this->_processInput($key, $_value);
            }

            return $values;
        }

        if (is_string($value)) {
            if (\Illuminate\Support\Str::contains($value, ',')) {
                $values = explode(',', $value);
                $values = $this->_processInput($key, $values);
                return implode(',', $values);
            }

            return $this->processStringInputs($key, $value);
        }

        return $value;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function finalProcessInput($key, $value)
    {
        if (is_null($key)) {
            return $value;
        }

        $fixKeywordMethod = $this->getDynamicMethod($this->processInputMethod, $key);
        if (method_exists($this, $fixKeywordMethod)) {
            return  $this->{$fixKeywordMethod}($value);
        }

        return $value;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function processStringInputs($key, $value)
    {
        $value = $this->processBackSlashes($key, $value);
        $value = $this->processString($key, $value);
        $value = $this->processBackSlushParts($key, $value);
        return $this->processSuffix($key, $value);
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function processBackSlashes($key, $value)
    {
        $corrected = $this->replaceBackSlashes($value);
        if (in_array($key, $this->defaultBackslashKeywords)) {
            return $corrected;
        }

        if (in_array($key, $this->backslashKeywords)) {
            return $corrected;
        }

        if ($corrected == $value) {
            return $value;
        }

        if ($this->__confirmBackslash) {
            return $corrected;
        }

//        if ($this->confirm($this->attentionSprintF('Do you wont correct %s input value from %s to %s', $key, $name, $corrected))) { @TODO
        if ($this->confirm($this->attentionSprintF('Do you wont correct %s to %s', $value, $corrected))) {
            return $corrected;
        }

        return $value;
    }

    /**
     * @param $key
     * @param string $value
     * @return string
     */
    public function processString($key, string $value)
    {
        $methods = $this->getProcessKeywordMethods();
        $inputMethods = $methods[$key] ?? null;
        $inputMethods = Arr::wrap($inputMethods);
        if (empty($inputMethods)) {
            return $value;
        }

        $corrected = $value;
        foreach ($inputMethods as $method) {
            // ucfirst or lcfirst or valid method name
            $corrected = $method($corrected);
            // @TODO confirm each part separately or not
        }

        if ($corrected != $value) {
//            $message = $this->attentionSprintF(sprintf('Do you wont correct %s input value from %s to %s', $key, $value, $corrected)); @TODO
            $message = $this->attentionSprintF(sprintf('Your %s input correction result is %s', $value, $corrected));
            return $this->confirm($message) ? $corrected : $value;
        }

        return $value;
    }

    /**
     * @param $input
     * @param string $value
     * @return string
     */
    public function processBackSlushParts($input, string $value)
    {
        if ( ! \Illuminate\Support\Str::contains($value, DIRECTORY_SEPARATOR)) {
            return $value;
        }

        $methods = $this->getProcessKeywordBackSlashPartsMethods();
        $inputMethods = $methods[$input] ?? null;
        $inputMethods = Arr::wrap($inputMethods);
        if (empty($inputMethods)) {
            return $value;
        }

        $words = explode(DIRECTORY_SEPARATOR, $value);
        foreach ($words as &$word) {
            foreach ($inputMethods as $method) {
                // ucfirst or lcfirst or valid method name
                $word = $method($word);
                // @TODO confirm each part separately or not
            }
        }

        $corrected = implode(DIRECTORY_SEPARATOR, $words);
        if ($corrected != $value) {
            $message = $this->attentionSprintF(sprintf('Your initial %s input parts of backslashes correction result is %s', $value, $corrected));
            return $this->confirm($message) ? $corrected : $value;
        }

        return $value;
    }

    /**
     * All parts of input must be process by methods
     *
     * keyword => method
     * keyword => [methods,...]
     *
     * method, methods must be any valid
     *          global functions,
     *          full classified methods like this Illuminate\Support\Str::pluralize
     *          callback
     * @return array|string
     */
    protected function getProcessKeywordBackSlashPartsMethods()
    {
        return array_merge($this->processKeywordBackSlashParts, $this->defaultKeywordBackSlashParts);
    }

    /**
     * All parts of input must be process by methods
     *
     * keyword => method
     * keyword => [methods,...]
     *
     * method, methods must be any valid
     *          global functions,
     *          full classified methods like this Illuminate\Support\Str::pluralize
     *          callback
     * @return array|string
     */
    protected function getProcessKeywordMethods()
    {
        return $this->processKeyword;
    }

    /**
     * @param $input
     * @param string $value
     * @return string
     */
    public function processSuffix($input, string $value)
    {
        // @TODO also prefix
        $corrected = $value;
        $suffix = $this->processKeywordSuffix[$input] ?? '';

        if ($suffix) {
            $corrected = $this->getWithoutSuffixPart($value, $suffix);
        }

        $corrected = $corrected . $suffix;
        if ($corrected == $value) {
            return $corrected;
        }

        return $this->choiceCorrectionMessage($value, $corrected, $suffix);
    }

    /**
     * @param $value
     * @param $tag
     * @param $correct
     * @return string
     */
    protected function processSpecialSymbols($value, $tag, $correct)
    {
        $parts = explode($tag, $value);
        return implode($correct, $parts);
    }

    /**
     * Check if pattern ends partially match with suffix it delete that part
     *
     * @param $pattern
     * @param $suffix
     * @return string
     */
    public function getWithoutSuffixPart($pattern, $suffix)
    {
        if (empty($suffix)) {
            return $pattern;
        }

        $suffixLen = strlen($suffix);
        if ($suffixLen < $this->checkSuffixLength) {
            // @TODO Dry
            $substring = substr($pattern, -strlen($suffix));
            if ($substring === $suffix) {
                return \Illuminate\Support\Str::replaceLast($substring, '', $pattern);
            }
        }

        for ($len = $suffixLen; $len >= $this->checkSuffixLength; $len--) {
            $partialSuffix = substr($suffix, 0, $len);
            // @TODO Dry
            $substring = substr($pattern, -strlen($partialSuffix));
            if ($substring === $partialSuffix) {
                return \Illuminate\Support\Str::replaceLast($substring, '', $pattern);
            }
        }

        return $pattern;
    }

    /**
     * attention sprintf wrap changeable words with  attention symbols
     *
     * @return string
     */
    public function attentionSprintF()
    {
        $arguments = func_get_args();
        $arguments[0] = str_replace('%s', $this->getAttentionString('%s'), $arguments[0]);
        return sprintf(...$arguments);
    }

    /**
     * return wrapped attention symbols
     *
     * @param $string
     * @return string
     */
    public function getAttentionString($string)
    {
        return sprintf($this->attentionStructure, $string);
    }

    /**
     * @param string $question
     * @param array $choices
     * @param null $default
     * @param null $attempts
     * @param null $multiple
     * @return null|string
     */
    public function choice($question, array $choices, $default = null, $attempts = null, $multiple = null)
    {
        if ($default && $this->__choiceDefault) {
            return $default;
        }

        return parent::choice($question, $choices, $default, $attempts, $multiple );
    }

    /**
     * @param string $question
     * @param bool $default
     * @return bool
     */
    public function confirm($question, $default = true)
    {
        if ($this->__confirm) {
            return $this->__confirmDefault;
        }

        return parent::confirm($question, $default);
    }

    /**
     * Choice message to correct user input or not
     *
     * @param $pattern
     * @param $corrected
     * @param $suffix
     * @return string
     */
    protected function choiceCorrectionMessage($pattern, $corrected, $suffix)
    {
        if ($this->__choiceDefault) {
            return $corrected;
        }
        //@TODO improve message
        $message = "%s class, which should be make by %s command suggested to end with %s ." . PHP_EOL;
        $message = $this->attentionSprintF($message, $pattern, get_class($this), $suffix);
        $message .= "Do you want to create corrected suffix?";


        $choiceOne = $this->attentionSprintF('Continue with your initial %s input', $pattern);
        $choiceTwo = $this->attentionSprintF('Change your initial %s input to corrected %s and continue', $pattern, $corrected);

        $choices = [
            1 => $choiceOne,
            2 => $choiceTwo,
        ];
        $choice = $this->choice($message, $choices, 2, $this->choiceCount);

        if ($choice == $choiceTwo) {
            return $corrected;
        }

        return $pattern;
    }

    /**
     * @param $input
     * @return array
     */
    protected function wrapArray($input)
    {
        return is_string($input) ? explode(',', $input) : Arr::wrap($input);
    }
}
