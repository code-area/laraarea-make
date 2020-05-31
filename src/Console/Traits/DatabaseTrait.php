<?php

namespace LaraAreaMake\Console\Traits;

use LaraAreaMake\Exceptions\LaraAreaCommandException;
use LaraAreaSupport\Facades\LaraAreaDB;

trait DatabaseTrait
{
    /**
     * @var array
     */
    public $ignoreTables = [];

    /**
     * @var
     */
    public $ignoreTableColumns;

    /**
     * @param $pattern
     * @param $stubContent
     * @return mixed
     * @throws LaraAreaCommandException
     */
    public function handleBasedDatabase($pattern, $stubContent)
    {
        // @TODO make based config.file
        if(method_exists($this, 'makeBasedDb')) {
            $dbStructure = $this->getDatabaseStructure($pattern);
            return $this->makeBasedDb($dbStructure, $stubContent);
        }
        $message = sprintf('%s must be contain makeBasedDb method', get_class($this));
        throw new LaraAreaCommandException($message);
    }


    /**
     * @param $pattern
     * @param $dbStructure
     * @return array
     * @throws LaraAreaCommandException
     */
    public function getDatabaseStructure($pattern)
    {
        $dbStructure = LaraAreaDB::getDBStructure();
        $dbTables = $dbStructure->keys()->all();

        if ($pattern == config('laraarea_maker.by_database')) {
            $tables = $dbTables;
        } else {
            // @TODO ':' make dynamically
            $str = \Illuminate\Support\Str::replaceFirst(config('laraarea_maker.by_database') . ':', '', $pattern);
            $tables = explode(',', $str);
            $diffTables = array_diff($tables, $dbTables);
            if($diffTables) {
                $message = $this->attentionSprintF(
                    'Only %s table can make by database. %s tables absent in your db please fix it',
                    implode(',', $dbTables),
                    implode(',', $diffTables)
                );
                throw new LaraAreaCommandException($message);
            }
        }
        $ignoreTables = $this->getIgnoreTables();
        foreach ($ignoreTables as $table) {
            unset($dbStructure[$table]);
        }

        return $dbStructure->only($tables)->toArray();
    }

    /**
     * @return array|\Illuminate\Config\Repository|mixed
     */
    protected function getIgnoreTables()
    {
        return !empty($this->ignoreTables) ? $this->ignoreTables : config('laraarea_maker.ignore_tables', []);
    }
}
