<?php

namespace LaraAreaMake\Console\Traits;

use LaraAreaMake\Exceptions\LaraAreaCommandException;
use LaraAreaSupport\Facades\LaraAreaDB;

trait DatabaseTrait
{
    /**
     * @var
     */
    protected $dbStructure;

    /**
     * @var
     */
    protected $fullDbStructure;

    /**
     * @var
     */
    protected $table;

    /**
     * @var
     */
    protected $tableColumns;

    /**
     * @var array
     */
    public $ignoreTables = [];

    /**
     * @param $pattern
     * @param $stubContent
     * @return bool
     * @throws LaraAreaCommandException
     */
    public function handleBasedDatabase($pattern, $stubContent)
    {
        // @TODO make based config.file
        $dbStructure = $this->getDatabaseStructure($pattern);
        return $this->makeBasedDb($dbStructure, $stubContent);
    }

    /**
     * @param $dbStructure
     * @param $content
     * @return bool
     */
    protected function makeBasedDb($dbStructure, $content)
    {
        $this->dbStructure = collect($dbStructure);
        foreach ($dbStructure as $table => $columnsInfo) {
            $this->setArguments();
            $this->setOptions();
            $this->__confirm = true;
            $this->__confirmOverwrite = true;
            $this->__choiceDefault = true;

            $this->__pattern = $this->processInput('pattern', $table);
            $this->table = $table;
            $this->tableColumns = $columnsInfo;
            $this->setDatabaseOptions($table, $columnsInfo);
            $this->createFileBy($this->__pattern, $content);
        }
        return true;
    }

    /**
     * @param $table
     * @param $columnsInfo
     */
    protected function setDatabaseOptions($table, $columnsInfo)
    {

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
        $this->fullDbStructure = $dbStructure->toArray();
        $dbTables = $dbStructure->keys()->all();

        if ($pattern == config('laraarea_make.by_database')) {
            $tables = $dbTables;
        } else {
            // @TODO ':' make dynamically
            $str = \Illuminate\Support\Str::replaceFirst(config('laraarea_make.by_database') . ':', '', $pattern);
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
        return !empty($this->ignoreTables) ? $this->ignoreTables : config('laraarea_make.ignore_tables', []);
    }
}
