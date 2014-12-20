<?php

namespace Sotnik\GridBundle\Grid;

use Sotnik\GridBundle\Column\ColumnInterface;
use Doctrine\ORM\QueryBuilder;

interface GridInterface
{

    /**
     * @param $gridTemplate
     * @return void
     */
    public function setGridTemplate($gridTemplate);

    /**
     * @return string
     */
    public function getGridTemplate();

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function setSource(QueryBuilder $queryBuilder);

    /**
     * @return queryBuilder
     */
    public function getSource();

    /**
     * @param array $perPageOptions
     * @return mixed
     */
    public function setPerPageOptions(array $perPageOptions);

    /**
     * @return array
     */
    public function getPerPageOptions();

    /**
     * @param string $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getId();

    /**
     * @param ColumnInterface $column
     * @return mixed
     */
    public function addColumn(ColumnInterface $column);

    /**
     * @return array
     */
    public function getColumns();

    /**
     * @return GridResultInterface
     */
    public function getGridResult();
}
