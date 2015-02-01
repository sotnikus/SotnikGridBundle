<?php
namespace Sotnik\GridBundle\Column;

use Sotnik\GridBundle\ColumnFilter\FilterCollectionInterface;
use Sotnik\GridBundle\ColumnSort\ColumnSortInterface;
use Sotnik\GridBundle\ColumnFilter\Filter\ColumnFilterInterface;
use Twig_Environment;
use Symfony\Component\DependencyInjection\ContainerInterface;

interface ColumnInterface
{

    /**
     * @param $hydrationMode
     * @throws \InvalidArgumentException
     */
    public function setHydrationMode($hydrationMode);

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
     * @param bool $sortable
     */
    public function setIsSortable($sortable);

    /**
     * @return bool
     */
    public function getIsSortable();

    /**
     * @param bool $isRaw
     */
    public function setIsRaw($isRaw);

    /**
     * @return bool
     */
    public function getIsRaw();

    /**
     * @param string $width
     */
    public function setWidth($width);

    /**
     * @return string
     */
    public function getWidth();

    /**
     * @param ColumnFilterInterface $filter
     * @return mixed
     */
    public function addFilter(ColumnFilterInterface $filter);

    /**
     * @return void
     */
    public function resetFilters();

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
