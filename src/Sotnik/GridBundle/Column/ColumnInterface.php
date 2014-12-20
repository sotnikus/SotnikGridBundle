<?php

namespace Sotnik\GridBundle\Column;

use Sotnik\GridBundle\ColumnFilter\FilterCollectionInterface;
use Sotnik\GridBundle\ColumnSort\ColumnSortInterface;
use Sotnik\GridBundle\ColumnFilter\Filter\ColumnFilterInterface;
use Twig_Environment;

interface ColumnInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $label
     * @return void
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param ColumnSortInterface $columnSort
     * @return void
     */
    public function setColumnSort(ColumnSortInterface $columnSort);

    /**
     * @return ColumnSortInterface
     */
    public function getColumnSort();

    /**
     * @param $template
     * @return void
     */
    public function setTemplate($template);

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @param ColumnFilterInterface $filter
     * @return mixed
     */
    public function addFilter(ColumnFilterInterface $filter);

    /**
     * @return FilterCollectionInterface
     */
    public function getFilterCollection();

    /**
     * @return string
     */
    public function getQueryMapping();

    /**
     * @param $row
     * @return mixed
     */
    public function getValueOfResultRow($row);
}
