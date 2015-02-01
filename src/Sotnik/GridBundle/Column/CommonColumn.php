<?php

namespace Sotnik\GridBundle\Column;

use Doctrine\ORM\Query;
use Sotnik\GridBundle\ColumnFilter\FilterCollectionInterface;
use Sotnik\GridBundle\ColumnSort\ColumnSort;
use Sotnik\GridBundle\ColumnSort\ColumnSortInterface;
use Sotnik\GridBundle\ColumnFilter\Filter\ColumnFilterInterface;
use Sotnik\GridBundle\Exception\InvalidColumnIdException;
use Sotnik\GridBundle\Grid\GridHelper;
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

    /**
     * @var bool
     */
    private $sortable = true;

    /**
     * @var bool
     */
    private $isRaw = true;

    /**
     * @var string
     */
    private $width = '';

    public function __construct($id, $resultGetter, $queryMapping)
    {
        $this->id = $id;
        $this->validateColumnId();

        $this->resultGetter = $resultGetter;

        $this->filterCollection = new FilterCollection();
        $this->queryMapping = $queryMapping;
        $this->setColumnSort(new ColumnSort($this->queryMapping));
    }

    /**
     * @param $hydrationMode
     * @throws \InvalidArgumentException
     */
    public function setHydrationMode($hydrationMode)
    {
        if (!in_array($hydrationMode, [Query::HYDRATE_OBJECT || Query::HYDRATE_ARRAY])) {
            throw new \InvalidArgumentException('Invalid hydration mode. Should be HYDRATE_OBJECT or HYDRATE_ARRAY');
        }

        $this->resultGetter = GridHelper::getQueryMapperClosure($this->resultGetter, $hydrationMode);
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
     * @param bool $sortable
     */
    public function setIsSortable($sortable)
    {
        $this->sortable = $sortable;
    }

    /**
     * @return bool
     */
    public function getIsSortable()
    {
        return $this->sortable;
    }

    /**
     * @param bool $isRaw
     */
    public function setIsRaw($isRaw)
    {
        $this->isRaw = $isRaw;
    }

    /**
     * @return bool
     */
    public function getIsRaw()
    {
        return $this->isRaw;
    }

    /**
     * @param string $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
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
     * @return void
     */
    public function resetFilters()
    {
        $this->filterCollection->reset();
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
