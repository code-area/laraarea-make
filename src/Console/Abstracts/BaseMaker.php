<?php

namespace LaraAreaMake\Console\Abstracts;

use Illuminate\Support\Facades\App;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use LaraAreaMake\Parser\Parser;
use LaraAreaMake\Console\Traits\DatabaseTrait;
use LaraAreaMake\Console\Traits\MakeSignatureTrait;
use LaraAreaMake\Console\Traits\ProcessInputTrait;
use LaraAreaMake\Console\Traits\ReplaceTrait;
use LaraAreaSupport\Str;
use LaraAreaMake\Exceptions\LaraAreaCommandException;

abstract class BaseMaker extends Command
{
    use
        MakeSignatureTrait,
        ProcessInputTrait,
        DatabaseTrait,
        ReplaceTrait;

    /**
     * All commands automatically must be starts with this
     * {--confirm} {--confirm-back-slash} {--confirm-overwrite} {--choice-default} {--path=} {--root-path=}
     *
     * php artisan  area-make:command class1
     * php artisan  area-make:command class1,class2
     * php artisan  area-make:command class1,class2/sub-class
     * php artisan  area-make:command class1,class2/sub-class,class2/sub-class2
     * php artisan  area-make:command class1,class2/sub-class,class2/sub-class2 --confirm
     * php artisan  area-make:command class1,class2/sub-class,class2/sub-class2 --confirm --confirm-overwrite
     * php artisan  area-make:command class1,class2/sub-class,class2/sub-class2 --confirm-overwrite
     * php artisan  area-make:command class1,class2/sub-class,class2/sub-class2 --path=api
     * php artisan  area-make:command class1,class2/sub-class,class2/sub-class2 --root-path=api
     */
    const AREA_MAKE = 'area-make:';

    /**
     * The stub name.
     * It it must be inside of stubs folder
     * and stubs must be sibling Commands
     *
     * @var string
     */
    public $stub;

    /**
     * All keyword which stubs must be contain
     * All keywords starts wrap in keywordTemplate
     * Keys for dynamically change stub content
     *
     * Also for dynamically replace keywords define "replaceExampleKeyword($content, $keyword)"
     * modify keywords and return result {not null}
     *
     * for example if $keywords = ['_name'] make "replaceNameKeyword($content, $keyword)"
     * where as argument pass stubContent  and keyword must be _name
     *
     * @TODO maybe rename options, or arguments or other
     * Used for set signature with arguments, options like defaultOptions
     *
     *  [
     *      option => option, // used for options
     *      option            // used for options with value
     *  ]
     *
     * @var array
     */
    public $keywords = [];

    /**
     * Force overwrite existing file content
     *
     * @var
     */
    protected $__confirmOverwrite;

//
////    /**
////     * All paths which makes by lara-make it must be set in config path using this
////     */
////    const CONFIG_MAKES_PATH = 'laraarea_maker.makes';
//
    /**
     * @var
     */
    protected $__pattern;

    /**
     * For keep pattern argument
     *
     * @var
     */
    protected $_pattern;

    /**
     * Current file full path
     *
     * @var
     */
    public $currentPath;

    /**
     * The relative path project where must be insert generated files
     *
     * @var
     */
    protected $path = '';

    /**
     * user defined relative path
     *
     * @var
     */
    protected $__path;

    /**
     * @TODO
     * @var bool
     */
    public $dumpAutoload = false;

    /**
     * info parameter
     *
     * Define pattern instance which must be show for success generation pattern,
     * And add in parent name
     * examples-pattern:Class, Trait, Interface, html, ...etc
     *
     * @var
     */
    public $instance;

    /**
     * @TODO tmp must be make dynamically and validate
     *
     * @var
     */
    public $extension;
//
//    //    /**
////     * @var string
////     */
////    protected $_config = '_config';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var Parser
     */
    protected $parser;

////    /**  @TODO think need or not
////     * When force = true it must be create according user input any changes does not happen
////     *
////     * @var bool
////     */
////    protected $__force;
////    /**
////     * Force overwrite existing file content
////     *
////     * @var
////     */
////    protected $__forceOverwrite;

    /**
     * Also for dynamically replace keywords define "replaceExampleKey($content, $keyword)"
     * modify keywords and return result {not null}
     *
     * for example if $keywords = ['_name'] make "replaceNameKey($content, $keyword)"
     * for example if $keywords = ['_name'] make "trimNameKey($content, $keyword)"
     * where as argument pass stubContent  and keyword must be _name
     *
     * make trimFinalContent($content) to finally trim result with not needed result
     *
     *
     * BaseMaker constructor.
     * @param Filesystem $files
     * @param Parser $parser
     * @throws LaraAreaCommandException
     */
    public function __construct(Filesystem $files, Parser $parser)
    {
        $this->setSignature();
        $this->files = $files;
        $this->parser = $parser;
        parent::__construct();
    }

    /**
     * {rootNameSpace} . {?namespace} . {patternNamespace} . pattern TODO TODO
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \ReflectionException
     */
    public function handle()
    {
        try {
           return $this->_handle();
        } catch (LaraAreaCommandException $exception) {
            echo 'Error: ' . $exception->getMessage();
        }
    }

    /**
     * @return bool
     * @throws LaraAreaCommandException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \ReflectionException
     */
    protected function _handle()
    {
        $stubContent = $this->getStubContent();
        $stubContent = $this->validateStubContent($stubContent);
        // firstly set options then arguments
        //        if (\Illuminate\Support\Str::startsWith($pattern, $this->_config)) {
        ////            // @TODO make based config.file
        ////            if(method_exists($this, 'makeBasedConfig')) {
        ////                return $this->makeBasedConfig();
        ////            }
        ////            $message = sprintf('%s must be contain makeBasedConfig method', get_class($this));
        ////            throw new LaraAreaCommandException($message);
        //        }
        $this->setOptions();
        $this->setArguments();

        $pattern = parent::argument('pattern');
        if (is_string($pattern) && \Illuminate\Support\Str::startsWith($pattern, config('laraarea_make.by_database'))) {
            return $this->handleBasedDatabase($pattern, $stubContent);
        }

        $pattern = $this->argument('pattern');
        $patterns = is_array($pattern) ? $pattern : explode(',', $pattern);
        return $this->fillPatterns($patterns, $stubContent);
    }

    /**
     * @return string
     * @throws LaraAreaCommandException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \ReflectionException
     */
    public function getStubContent()
    {
        if (empty($this->stub)) {
            $message = $this->attentionSprintF('%s must be filled when used default getStubContent method, or you must be change method content', 'stub');
            throw new LaraAreaCommandException($message);
        }

        $path = $this->getStubPath();
        return $this->files->get($path);
    }

    /**
     * @return bool|string
     * @throws \ReflectionException
     */
    public function getStubPath()
    {
        $path = base_path($this->stub);
        if (file_exists($path)) {
            return $path;
        }

        $stub = $this->stub;
        $path = app_path('Console' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . $stub);

        if (! file_exists($path)) {
            $reflector = new \ReflectionClass(self::class);
            $fn = $reflector->getFileName();
            $callPath = dirname($fn);
            $path = Str::before($callPath, 'src', 1);
            $path .= 'stubs' . DIRECTORY_SEPARATOR . $stub;
        }

        return $path;
    }

    /**
     * @param $content
     * @return mixed
     * @throws LaraAreaCommandException
     */
    public function validateStubContent($content)
    {
        // @TODO make preg match for all structure
        // preg_match_all("/\{\{(.+)\}\}/", $stubContent, $matches);
        $content = $this->fixNewLinesOfStub($content);
        $keys = $this->getKeywords($content);
        $keywords = array_keys($this->keywords);
        $diffKey = array_diff($keys, $keywords);

        if ($diffKey) {
            // @TODO improve message with incorrect keyword
            $message = 'check your keywords ' . implode(',', $diffKey);
            throw new LaraAreaCommandException($message);
        }

        return $content;
    }

    /**
     * Get all keywords by content
     *
     * @param $content
     * @return array
     */
    public function getKeywords($content)
    {
        $keyEnds = Str::after($this->keywordTemplate, '%s');
        $keyStarts = Str::before($this->keywordTemplate, '%s');
        $keys = [];

        foreach (explode($keyStarts, $content) as $key) {
            if (\Illuminate\Support\Str::contains($content, $keyEnds)) {
                if (false != Str::before($key, $keyEnds)) {
                    $keys[] = Str::before($key, $keyEnds);
                }
            }
        }
//        $pregPattern = str_replace('%s', '(.*?)', $this->keyStructure);
//        $pregPattern  = sprintf('/%s/s', $pregPattern);
//        preg_match_all($pregPattern, $content, $keys);
//        $keys = $keys[1];

        return array_unique($keys);
    }

    /**
     * @param $content
     * @return mixed
     */
    public function fixNewLinesOfStub($content)
    {
        $content = $this->replaceContent(
            [PHP_EOL, "\r\n", "\n", '\r\n', '\n'],
            ['__tmp__', '__tmp__', '__tmp__', '__tmp__', '__tmp__'],
            $content
        );
        return $this->replaceContent('__tmp__', PHP_EOL, $content);
    }

    /**
     * Set all user command input options and validate it
     *
     * @throws LaraAreaCommandException
     */
    protected function setOptions()
    {
        foreach ($this->propertyWithOptions as $property => $option) {
            $option = $this->option($option);
            $this->{'__' . $property} = $option;
        }
    }

    /**
     * Set all user command input options and validate it
     *
     * @throws LaraAreaCommandException
     */
    protected function setArguments()
    {
        foreach ($this->propertyWithArguments as $property => $argument) {
            $argument = $this->argument($argument);
            $argument = $this->processInput($property, $argument);
            $this->{'_' . $property} = $argument;
        }
    }

    /**
     * Correct all user input pattern name, namespace, all options and make correspond files
     *
     * @param $patterns
     * @param $stubContent
     * @return bool
     * @throws LaraAreaCommandException
     */
    public function fillPatterns($patterns, $stubContent)
    {
        foreach ($patterns as $pattern) {
            if(false == $this->createFileBy($pattern, $stubContent)) {
                // @TODO show not saved infon
                return false;
            }
        }

        return true;
    }

    /**
     * @param $pattern
     * @param $content
     * @return int|null
     * @throws LaraAreaCommandException
     */
    public function createFileBy($pattern, $content)
    {
        $path = $this->getCurrentPathBy($pattern, $this->extension);

        if ($this->files->exists($path)) {
            if (! $this->__confirmOverwrite) {
                $message = $this->attentionSprintF('This %s class already exists do you want to override it', $pattern);
                $confirm = $this->__confirm;
                $this->__confirm = false;

                if (! $this->confirm($message)) {
                    $this->__confirm = $confirm;
                    return null;
                }
                $this->__confirm = $confirm;
            }
        } else {
            $this->makeDirectory($path);
            if ($this->dumpAutoload) {
                // @TODO
                $this->composerDumpAutoload();
            }
        }

        $this->_pattern = $pattern;
        $this->__pattern = $pattern;
        $content = $this->replaceStubContent($content);
        $length = $this->files->put($path, $content);
        $message = $this->attentionSprintF('In path  %s %s %s created successfully. Filled %s symbol', $path, $pattern, $this->instance, $length);
        $this->info($message);
        // @TODO use
//        $this->line('line');
//        $this->info('info');
//        $this->comment('comment');
//        $this->question('question');
//        $this->error('error');

        return true;
    }

    /**
     * Get generation file full path
     *
     * @return mixed
     */
    protected function getCurrentPath()
    {
        return $this->currentPath;
    }

    /**
     * Get generation file full path
     *
     * @param $name
     * @param $extension
     * @return string
     */
    protected function getCurrentPathBy($name, $extension)
    {
        $pathStarts = $this->getRelativePath();
        if (empty($pathStarts)) {
            $name = $this->processRelativePath($name);
        }
        $path = $pathStarts . $name;
        $this->currentPath = base_path($path) . $extension;

        return $this->getCurrentPath();
    }

    /**
     * @param string $path
     * @return string
     */
    public function getRelativePath($path = '')
    {
        $this->path = $this->processPrePath($this->__path, $this->path);
        $path = $path
            ? $path . $this->path
            : $this->path = $this->processRelativePath($this->path);

        if ($path && ! \Illuminate\Support\Str::endsWith($path, DIRECTORY_SEPARATOR)) {
            $path = $path . DIRECTORY_SEPARATOR;
        }

        return $path;
    }

    /**
     * @param $value
     * @param $default
     * @return string
     */
    public function processPrePath($value, $default)
    {
        $value = $value ?? $default;
        return $value ? \Illuminate\Support\Str::finish($value, DIRECTORY_SEPARATOR) : $value;
    }

    /**
     * @param $path
     * @return string
     */
    protected function processRelativePath($path)
    {
        if (! strpos($path, DIRECTORY_SEPARATOR)) {
            return $path;
        }

        $paths = explode(DIRECTORY_SEPARATOR, $path);
        $first = array_shift($paths);
        $corrected = strtolower($first);
        if ($corrected != $first && ! $this->confirm($this->attentionSprintF('%s first folder correct name is %s, are you confirm', $first, $corrected))){
            $corrected = $first;
        }
        array_unshift($paths, $corrected);
        return implode(DIRECTORY_SEPARATOR, $paths);
    }

    /**
     * @param $path
     */
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     *
     */
    protected function composerDumpAutoload()
    {
        $composer = App::make(Composer::class);
        $composer->dumpAutoloads();
    }

    /**
     * Ger command folder full path
     *
     * @param string $dir
     * @return bool|string
     */
    public function getCommandPath($dir = __DIR__)
    {
        return Str::before($dir, 'Commands', 1);
    }

    /**
     * @param null $key
     * @return array|string|null
     * @throws LaraAreaCommandException
     */
    public function argument($key = null)
    {
        $argument =  parent::argument($key);
        $argument = $this->parser->parseInput($key, $argument);
        return $this->processInput($key, $argument);
    }

    /**
     * @param null $key
     * @return array|bool|string|null
     * @throws LaraAreaCommandException
     */
    public function option($key = null)
    {
        $option =  parent::option($key);
        $option = $this->parser->parseInput($key, $option);
        return $this->processInput($key, $option);
    }

    /**
     * @param null $key
     * @return array|string|null
     */
    public function initialArgument($key = null)
    {
        if (is_null($key)) {
            return $this->input->getArguments();
        }

        return $this->input->getArgument($key);
    }

    /**
     * @param null $key
     * @return array|bool|string|null
     */
    public function initialOption($key = null)
    {
        if (is_null($key)) {
            return $this->input->getOptions();
        }

        return $this->input->getOption($key);
    }

    /**
     * @param $template
     * @param $dynamic
     * @return string
     */
    public function getDynamicMethod($template, $dynamic)
    {
        return sprintf($template, $this->studlyCase($dynamic));
    }

    /**
     * @return array
     */
    public function getKeywordValues()
    {
        $result = [];
        foreach ($this->keywords as $keyword => $option) {
            $result['__' . $keyword] = $this->{'__' . $keyword };
        }

        return $result;
    }
}
