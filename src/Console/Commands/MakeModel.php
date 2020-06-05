<?php

namespace LaraAreaMake\Console\Commands;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LaraAreaMake\Console\Abstracts\ClassMaker;
use LaraAreaMake\Exceptions\LaraAreaCommandException;

class MakeModel extends ClassMaker
{
    //php artisan area-make:model _db
    //php artisan area-make:model [test2] --choice-default --confirm --use [Carbon/Carbon,CodeAreaSupport/Str,App\\Models,Illuminate/Database/Eloquent/Model] --abstract --path app/Models --interfaces Illuminate\\Contracts\\Queue\\ShouldQueue --trait [CodeAreaModel\\Traits\\ModelExtrasTrait,Illuminate\\Database\\Eloquent\\SoftDeletes] --constant [testName=test,as=as,name=name] --property [public=[0=name,description=some___description]] --method [public=[[arguments=[0=name,str=sas],content=return___true____,name=aa]]]

    /**
     * @var string
     */
    public $commandName = 'model';

    /**
     * @var string
     */
    public $signature  = 'area-make:model {pattern}';

    /**
     * @var string
     */
    public $instance = 'Model';

    /**
     * @var string
     */
    public $rootPath = 'app' . DIRECTORY_SEPARATOR . 'Models';


    /**
     * @var string
     */
    protected $description = 'Make flexible Models with extends CodeAreaModel\Models\CodeAreaModel';

    /**
     * @var bool
     */
    public $makeBase = true;



    /**
     * @var string
     */
    public $parent = Model::class;

    /**
     * @var array
     */
    public $notFillableColumns = [];

    /**
     * @param $dbStructure
     * @param $content
     * @return bool
     * @throws LaraAreaCommandException
     */
    protected function makeBasedDb($dbStructure, $content)
    {
        $this->__confirm = true;
        $this->__confirmOverwrite = true;

        if ($this->makeBase) {
            $this->__abstract = true;
            if (false == $this->createBaseParent($content)) {
                // @TODO show dont saved message
                return false;
            }
            $this->__abstract = false;
        }

        $data = [];
        $notFillableColumns = $this->getNotFillableColumns();
        foreach ($this->fullDbStructure as $table => $columnsInfo) {
            $pattern = \Illuminate\Support\Str::singular(\Illuminate\Support\Str::title($table));
            $pattern = str_replace('_', '', $pattern);
            $data[$table]['pattern'] = $pattern;
            $customTableColumns = $this->getNotFillableColumnsByTable($table);
            $notFillableTableColumns = array_merge($customTableColumns, $notFillableColumns);
            foreach ($columnsInfo as $column => $_data) {

                if ('PRI' == $_data['key']) {
                    continue;
                }

                if (in_array($column, $notFillableTableColumns)) {
                    continue;
                }

                $data[$table]['columns'][] = $column;
                if (\Illuminate\Support\Str::endsWith($column, '_id')) {
                    $relation = \Illuminate\Support\Str::replaceLast('_id', '', $column);
                    $data[$table]['methods']['belongs_to'][] = $relation;
                    $data[\Illuminate\Support\Str::plural($relation)]['methods']['has_many'][] = $table;
                }
            }
        }
        $ignoreTables = $this->getIgnoreTables();
        foreach ($data as $table => $info) {
            if (in_array($table, $ignoreTables)) {
                continue;
            }
            if (! in_array($table, array_keys($dbStructure))) {
                continue;
            }

            $this->__use = [];
            if (!isset($info['pattern'])) {
                continue;
            }

            $this->__pattern = $info['pattern'];
            $this->__property = [];
            if ($table != \Illuminate\Support\Str::plural($table)) {
                $this->__property['protected']['table'] = $table;
            }

            $this->__property['public'] = [
                'fillable' => $info['columns'],
            ];

            if (!empty($info['const'])) {
                foreach ($info['const'] as $const => $value) {
                    $this->__constant[strtoupper($const)] = $value;
                }
            }

            if (!empty($info['methods'])) {
                $methods = [];
                if (!empty($info['methods']['belongs_to'])) {
                    foreach ($info['methods']['belongs_to'] as $relation) {
                        $_table = \Illuminate\Support\Str::plural($relation);

                        $template = empty($data[$_table]['pattern'])
                            ? TAB . TAB . TAB . 'return true;// @TODO fix'
                            : TAB . TAB . TAB . sprintf('return $this->belongsTo(%s::class);', $data[$_table]['pattern']) ;
                        $methods[] = [
                            'name' => $relation,
                            'content' => $template,
                            'comment' => [
                                'return' => [
                                    $this->processNamespace(BelongsTo::class)
                                ]
                            ]
                        ];
                    }
                }

                if (!empty($info['methods']['has_many'])) {
                    foreach ($info['methods']['has_many'] as $_table) {
                        $template = empty($data[$_table]['pattern'])
                            ? TAB . TAB . TAB . 'return true;// @TODO fix'
                            : TAB . TAB . TAB . sprintf('return $this->hasMany(%s::class);', $data[$_table]['pattern']) ;
                        $methods[] = [
                            'name' => $_table,
                            'content' => $template,
                            'comment' => [
                                'return' => [
                                    $this->processNamespace(HasMany::class)
                                ]
                            ]
                        ];
                    }
                }

                $this->__method = [
                    'public' => $methods
                ];
            }
            $this->createFileBy($this->__pattern, $content);
        }
    }


    /**
     * @return array|\Illuminate\Config\Repository|mixed
     */
    protected function getNotFillableColumns()
    {
        return !empty($this->notFillableColumns)
            ? $this->notFillableColumns
            : config('laraarea_make.not_fillable_columns', []);
    }

    /**
     * @return array|\Illuminate\Config\Repository|mixed
     */
    protected function getNotFillableColumnsByTable($table)
    {
        $config = !empty($this->notFillableTableColumns)
            ? $this->notFillableTableColumns:
            config('laraarea_make.not_fillable_table_columns', []);

        return $config[$table] ?? [];
    }
}