<?php

namespace LaraAreaMake\Console\Traits;

use LaraAreaMake\Exceptions\LaraAreaCommandException;
use LaraAreaSupport\Str;

trait MakeSignatureTrait
{
    /**
     * The console command name
     * automatically mast be start area_make: {self::AREA_MAKE}
     * fore change it command prefix you must be change const AREA_MAKE
     *
     * @var
     */
    public $commandName;

    /**
     * Add command arguments
     * All arguments must be corrected and automatically must be insert user input to _argument property
     * All property must be camelCase
     *
     * @var
     */
    public $commandArguments = [];

    /**
     * Add command options
     * All options must be corrected and automatically must be insert user input to __option property
     * All property must be camelCase
     *
     * @var array
     */
    public $commandOptions = [];

    /**
     * Used for set signature with arguments, options
     *  [
     *      option => option, // used for options
     *      option            // used for options with value
     *  ]
     * @var array
     */
    protected $defaultOptions = [
        'confirm' => 'confirm : Confirm all system correction inputs',
        'confirm-default' => 'confirm-default= : Set [false, no or 0] for not permit any changes make by system',
        'confirm-backslash' => 'confirm-backslash : Confirm correct backslash. Must be replace \ to /',
        'confirm-overwrite' => 'confirm-overwrite : Confirm overwrite existing files',
        'choice-default' => 'choice-default : Set default choice',
        'path' => 'path= : Path where must be generate file',
    ];

    /**
     * Default arguments
     *
     * @var array
     */
    protected $defaultArguments = [];

    /**
     * Used for get class properties and options
     *
     * @var array
     */
    private $propertyWithOptions = [];

    /**
     * Used for get class properties and arguments
     *
     * @var array
     */
    private $propertyWithArguments = [];

    /**
     * Force to all command starts with self::AREA_MAKE
     *
     * @throws LaraAreaCommandException
     */
    protected function setSignature()
    {

        $keywords = [];
        foreach ($this->keywords as $key => $pattern) {
            if (is_numeric($key)) {
                $keywords[$pattern] = $pattern . '=';
            } else {
                $keywords[$key] = $pattern;
            }
        }

        $this->keywords = $keywords;
        $settings = $this->getCommandSettings();

        if (empty($this->signature)) {
            if (empty($this->commandName)) {
                $message = $this->attentionSprintF("%s property must be fill in class %s ", 'CommandName', get_class($this));
                throw new LaraAreaCommandException($message);
            }

            $this->signature = self::AREA_MAKE . $this->commandName . ' {pattern}';

        }

        if (!empty($settings)) {
            $this->signature .= implode_wrap($settings, ' {', '}');
        }
    }

    /**
     * Get commands arguments and options
     *
     * @return array
     * @throws LaraAreaCommandException
     */
    public function getCommandSettings()
    {
        $arguments = $this->getCommandArguments();
        $options = $this->getCommandOptions();
        $settings = array_merge(array_values($arguments), array_values($options));

        return array_unique($settings);
    }

    /**
     * Get artisan command arguments
     *
     * @return array
     * @throws LaraAreaCommandException
     */
    public function getCommandArguments()
    {
        $defaultArguments = $this->processCommandSettings($this->defaultArguments, $this->propertyWithArguments);
        $arguments = $this->processCommandSettings($this->commandArguments, $this->propertyWithArguments);
        return array_merge($defaultArguments, $arguments);
    }

    /**
     * Get artisan command options
     *
     * @return array
     * @throws LaraAreaCommandException
     */
    public function getCommandOptions()
    {
        // need
        $defaultOptions = $this->processCommandSettings($this->defaultOptions, $this->propertyWithOptions, '--');
        $keyOptions = $this->processCommandSettings($this->keywords, $this->propertyWithOptions, '--');
        $commandOptions = $this->processCommandSettings($this->commandOptions, $this->propertyWithOptions, '--');

        $options = array_merge($keyOptions, $defaultOptions);
        $options = array_merge($options, $commandOptions);
        return $options;
    }

    /**
     * make command options as [$property => $option]
     *
     * @param $settings
     * @param $resultCorrection
     * @param $delimiter
     * @return array
     * @throws LaraAreaCommandException
     */
    protected function processCommandSettings($settings, &$resultCorrection, $delimiter = '')
    {
        $_settings = [];

        if (!is_array($settings)) {
            return $_settings;
        }

        foreach ($settings as $property => $setting) {
            if (is_numeric($property)) {

                if (\Illuminate\Support\Str::contains($setting, [':', '|'])) {
                    // @TODO fix
                    $message = $this->attentionSprintF('%s this structure in this time not available', ':, |');
                    throw new LaraAreaCommandException($message);
                } elseif (\Illuminate\Support\Str::contains($setting, ['='])) {
                    $property = Str::before($setting, '=');
                } elseif (\Illuminate\Support\Str::contains($setting, ['?'])) {
                    $property = Str::before($setting, '?');
                } elseif (\Illuminate\Support\Str::contains($setting, ['*'])) {
                    $property = Str::before($setting, '*');
                } else {
                    $property = $setting;
                }
            }

            $resultCorrection[\Illuminate\Support\Str::camel($property)] = $property;
            $_settings[\Illuminate\Support\Str::camel($property)] = $delimiter . $setting;
        }

        return $_settings;
    }
}
