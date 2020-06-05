<?php

namespace LaraAreaMake\Console\Traits;

trait MultiMakerTrait
{
    /**
     * @var
     */
    protected $dynamic;

    /**
     * @var array
     */
    protected $dynamicParts = [
        null
    ];

    /**
     * @return bool|void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \ReflectionException
     */
    public function handle()
    {
        $dynamicParts = $this->getDynamicParts();
        $immutableProperties = $this->getImmutableProperties();

        foreach ($dynamicParts as $dynamic) {
            $this->dynamicHandle($dynamic, $immutableProperties);
        }
    }

    /**
     * @return array
     */
    protected function getImmutableProperties()
    {
        return [
            'rootPath' => $this->rootPath,
        ];
    }

    /**
     * @param $dynamic
     * @return array
     */
    protected function getDynamicProperties($dynamic)
    {
        return [
            'rootPath' => $this->rootPath . DIRECTORY_SEPARATOR . $dynamic,
        ];
    }

    /**
     * @param $dynamic
     * @param $immutableProperties
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \ReflectionException
     */
    protected function dynamicHandle($dynamic, $immutableProperties)
    {
        $this->dynamic = $dynamic;
        $properties = $this->getDynamicProperties($dynamic);

        foreach ($properties as $property => $value) {
            $this->{$property} = $value;
        }

        parent::handle();

        foreach ($immutableProperties as $property => $value) {
            $this->{$property} = $value;
        }
    }

    /**
     * @return array
     */
    protected function getDynamicParts()
    {
        return $this->dynamicParts ?? [null];
    }
}
