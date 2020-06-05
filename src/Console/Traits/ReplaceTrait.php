<?php

namespace LaraAreaMake\Console\Traits;

use Illuminate\Support\Str;
use LaraAreaMake\Exceptions\LaraAreaCommandException;

trait ReplaceTrait
{
    /**
     * Must be not set as options in signature
     *
     * @var
     */
    public $guessKeywords = [];

    /**
     * Keyword template for stab
     *
     * @var string
     */
    public $keywordTemplate = '{{%s}}';

    /**
     *  Guess keyword correct value and make confirmation to use guessed value if it different in initial input
     *
     * @var string
     */
    protected $guessKeywordMethod = 'guess%sKeyword';

    /**
     * For change method nAme kayWords
     *
     * @var string
     */
    protected $replaceKeywordMethod = 'replace%sKeyword';

    /**
     * @var string
     */
//    protected replaceRepeatable = 'get%sKeywordTemplate';
    protected $repeatableKeywordMethod = 'replaceRepeatable%sKeyword';

    /**
     * @var string
     */
    protected $trimKeywordMethod = 'trim%sKeyword';

    /**
     * @var int
     */
    protected $reportKeywords = false;

    /**
     * @var int
     */
    protected $reportStage = false;

    /**
     * @var int
     */
    protected $reportStageAfter = 0;

    /**
     * @var int
     */
    protected $stage  = 0;

    /**
     * @param $content
     * @return mixed
     * @throws LaraAreaCommandException
     */
    protected function replaceStubContent($content)
    {
//        $makes = config(self::CONFIG_MAKES_PATH, []);
//        $makes[] = $pattern;
//        Config::set(self::CONFIG_MAKES_PATH, $makes);

        foreach ($this->keywords as $keyword => $inputType) {
            $this->keyword = $keyword;
            if ($this->reportKeywords) {
                $this->info($keyword);
            }
            $keyOption = '__' . $keyword;

            if (in_array($keyword, $this->guessKeywords)) {
                $this->guessKeyword($keyword, $keyOption, $inputType);
            }

            $_keyword = $this->getKeywordTemplate($keyword);
            if (! Str::contains($content, $_keyword)) {
                continue;
            }

            $replaceMethod = $this->getDynamicMethod($this->replaceKeywordMethod, $keyword);

            // @TODO think if input is array then use some general method
            // someKeywordRelatedProperty . PHP_EOL . $_keyword;
            // for Use keyword like someKeywordRelatedProperty = 'use %s;'
            $repeatableMethod = $this->getDynamicMethod($this->repeatableKeywordMethod, $keyword);
            if (method_exists($this, $repeatableMethod)) {
                $values = (array)$this->{$keyOption};
                foreach ($values as $value) {
                    // @TODO include replace method also
                    $template = $this->{$repeatableMethod}($_keyword);
                    $change = sprintf($template, $value);
                    $content = $this->replaceContent($_keyword, $change, $content);
                }

                continue;
            }

            if (method_exists($this, $replaceMethod)) {
                $content = $this->{$replaceMethod}($content, $_keyword, $this->{$keyOption});
                if (is_null($content)) {
                    $message = $replaceMethod . ' must be return corrected content not null';
                    throw new LaraAreaCommandException($message);
                }
                continue;
            }
        }

        $keywords = array_keys($this->keywords);
        // @TODO dry
        foreach ($keywords as $keyword) {
            $this->keyword = $keyword;
            $_keyword = $this->getKeywordTemplate($keyword);

            if (! Str::contains($content, $_keyword)) {
                continue;
            }

            $trimMethod = $this->getDynamicMethod($this->trimKeywordMethod, $keyword);

            if (! method_exists($this, $trimMethod) || ! \Illuminate\Support\Str::contains($content, $_keyword)) {
                continue;
            }

            $content = $this->{$trimMethod}($content, $_keyword);
            if (is_null($content)) {
                $message = $trimMethod . ' must be return corrected content not null';
                throw new LaraAreaCommandException($message);
            }
        }

        foreach ($keywords as $keyword) {
            $this->keyword = $keyword;
            if (Str::contains($content, $keyword)) {
                $content = $this->replaceContent($this->getKeywordTemplate($keyword), '', $content);
            }
        }

        return $this->trimFinalContent($content);
    }

    /**
     * @param $keyword
     * @param $keyOption
     * @param $inputType
     * @throws LaraAreaCommandException
     */
    protected function guessKeyword($keyword, $keyOption, $inputType)
    {
        $guessKeywordMethod = sprintf($this->guessKeywordMethod, $this->studlyCase($keyword));
        if (! method_exists($this, $guessKeywordMethod)) {
            throw new LaraAreaCommandException($this->attentionSprintF(
                'This %s class must be contain %s method when guessKeywords property contain %s',
                get_class($this),
                $guessKeywordMethod,
                $keyword
            ));
        }


        $guessValue = $this->{$guessKeywordMethod}($this->{$keyOption});
        if ($this->{$keyOption} && $this->{$keyOption} != $guessValue) {
            //@TODO show message by input type
            if ($this->confirm($this->attentionSprintF('Your initial [%s] input is %s, By our system guess that value must be %s. Are you want correct it', $keyOption, $this->{$keyOption}, $guessValue))) {
                $this->{$keyOption} = $guessValue;
            }
        } else {
            $this->{$keyOption} = $guessValue;
        }
    }

    /**
     * Overwrite ths methods for fix final changes
     *
     * @param $content
     * @return mixed
     */
    public function trimFinalContent($content)
    {
        return $content;
    }

    /**
     * @param $from
     * @param $to
     * @param $content
     * @return mixed
     */
    public function replaceContent($from, $to, $content)
    {
        if ($from == $to) {
            return $content;
        }

        $this->reportContentChanges($from, $to, $content);
        $content = str_replace($from, $to, $content);

        return $content;
    }

    /**
     * @param $from
     * @param $to
     * @param $content
     */
    protected function reportContentChanges($from, $to, $content)
    {
        $this->stage++;
        if (empty($this->reportStage)) {
            return;
        }

        if ($this->stage < $this->reportStageAfter) {
            return;
        }

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3)[2];
        if (is_array($from)) {
            foreach ($from as $key => $_from) {
                $_to = is_array($to) ? $to[$key] : $to;
                $content = str_replace($from, '<error>' . $_from . '</error><info>'. $_to . '</info>', $content);
            }
        } else {
            $content = str_replace($from, '<error>' . $from . '</error><info>'. $to . '</info>', $content);
        }
        $content .= ' test';
        $content = str_replace(' ', '<info>-</info>', $content);
        $this->line($content);
        $this->comment(sprintf('Replaced [%s] times by [%s] method', $this->stage, $backtrace['function']));

        parent::confirm('continue');
    }

    /**
     * @param $keyword
     * @return string
     */
    public function getKeywordTemplate($keyword)
    {
        return sprintf($this->keywordTemplate, $keyword);
    }

    /**
     * @param $value
     * @return string
     */
    public function studlyCase($value)
    {
        return Str::studly($value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function replaceBackSlashes($value)
    {
        $symbols = [
            '/*' => '__comment__start__',
            '*/' => '__comment__end__',
            '//' => '__comment__',
            '/' => '__back__slashes__',
            DIRECTORY_SEPARATOR => '__back__slashes__',
        ];
        $corrected = str_replace(array_keys($symbols), array_values($symbols), $value);
        $symbols = array_flip($symbols);
        $corrected = str_replace(array_keys($symbols), array_values($symbols), $corrected);
        return $corrected;
    }
}
