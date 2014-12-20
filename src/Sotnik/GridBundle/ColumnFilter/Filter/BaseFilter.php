<?php
namespace Sotnik\GridBundle\ColumnFilter\Filter;

class BaseFilter
{
    protected $queryMapping;

    protected $value = '';

    public function setQueryMapping($queryMapping)
    {
        $this->queryMapping = $queryMapping;
    }

    public function getQueryMapping()
    {
        return $this->queryMapping;
    }

    public function setValue($value)
    {
        if (is_array($value)) {
            array_walk_recursive(
                $value,
                function ($item, $key) {
                    trim($item);
                }
            );
        } else {
            $value = trim($value);
        }

        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getName()
    {
        $name = explode('\\', get_class($this));
        $name = array_pop($name);

        return strtolower(preg_replace('|(.+)([A-Z])|U', '$1-$2', $name, -1, $count));
    }

    protected function getParamName()
    {
        return 'param' . md5($this->getName() . $this->queryMapping);
    }

    public function getRenderType()
    {
        return ColumnFilterInterface::INPUT;
    }
}
