<?php

namespace Sotnik\GridBundle\Column;

use Sotnik\GridBundle\ColumnFilter\FilterCollectionInterface;
use Sotnik\GridBundle\ColumnSort\ColumnSort;
use Sotnik\GridBundle\ColumnSort\ColumnSortInterface;
use Sotnik\GridBundle\ColumnFilter\Filter\ColumnFilterInterface;
use Sotnik\GridBundle\Exception\InvalidColumnIdException;
use Twig_Environment;
use Sotnik\GridBundle\ColumnFilter\FilterCollection;

class CommonColumn implements ColumnInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $label;

    /**
     * @var \Closure
     */
    private $resultGetter;

    /**
     * @var string
     */
    private $template = '';

    /**
     * @var ColumnSortInterface
     */
    private $columnSort;

    /**
     * @var string
     */
    private $queryMapping;

    /**
     * @var FilterCollectionInterface
     */
    private $filterCollection;

    public function __construct($id, $resultGetter, $queryMapping)
    {
        $this->id = $id;
        $this->validateColumnId();

        if ($resultGetter instanceof \Closure) {
            $this->resultGetter = $resultGetter;
        } else {
            $parts = explode('.', $resultGetter);

            $this->resultGetter = function ($row) use ($parts) {
                $result = $row;
                foreach ($parts as $part) {
                    $getter = 'get' . ucfirst($part);
                    $result = $result->$getter();

                    if ($result === null) {
                        return null;
                    }
                }

                return $result;
            };
        }

        $this->filterCollection = new FilterCollection();
        $this->queryMapping = $queryMapping;
        $this->setColumnSort(new ColumnSort($this->queryMapping));
    }

    private function validateColumnId()
    {
        $matches = [];
        preg_match('|[a-zA-Z_-]+|', $this->getId(), $matches);

        if (!isset($matches[0]) || $matches[0] != $this->getId()) {
            throw new InvalidColumnIdException(
                sprintf('Column id="%s" is invalid. Only a-zA-Z_- are allowed', $this->getId())
            );
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param ColumnSortInterface $columnSort
     * @return void
     */
    public function setColumnSort(ColumnSortInterface $columnSort)
    {
        $this->columnSort = $columnSort;
    }

    /**
     * @return ColumnSortInterface
     */
    public function getColumnSort()
    {
        return $this->columnSort;
    }

    /**
     * @param string $label
     * @return void
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param $template
     * @return void
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param ColumnFilterInterface $filter
     * @return mixed
     */
    public function addFilter(ColumnFilterInterface $filter)
    {
        $filter->setQueryMapping($this->getQueryMapping());
        $this->filterCollection->addFilter($filter);
    }

    /**
     * @return FilterCollectionInterface
     */
    public function getFilterCollection()
    {
        return $this->filterCollection;
    }

    /**
     * @return string
     */
    public function getQueryMapping()
    {
        return $this->queryMapping;
    }

    /**
     * @param $row
     * @return mixed
     */
    public function getValueOfResultRow($row)
    {
        $getterFunction = $this->resultGetter;
        $value = $getterFunction($row);

        return $value;
    }
}
