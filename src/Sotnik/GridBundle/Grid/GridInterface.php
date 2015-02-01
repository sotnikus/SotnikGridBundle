<?php

namespace Sotnik\GridBundle\Grid;

use Sotnik\GridBundle\Batch\BatchActionInterface;
use Sotnik\GridBundle\Column\ColumnInterface;
use Doctrine\ORM\QueryBuilder;
use Sotnik\GridBundle\RowAction\Action;
use Sotnik\GridBundle\RowAction\ActionColumnInterface;

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
     * @param int $hydrationMode
     * @param bool $leftJoinCollection
     */
    public function setSource(QueryBuilder $queryBuilder, $hydrationMode, $leftJoinCollection);

    /**
     * @return queryBuilder
     */
    public function getSource();

    /**
     * @param array $perPageOptions
     * @return mixed
     */
    public function setPerPageLimits(array $perPageOptions);

    /**
     * @return array
     */
    public function getPerPageLimits();

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
     * @param string $idGetter
     * @param BatchActionInterface[] $batchActions
     * @return void
     */
    public function setBatchActions($idGetter, array $batchActions);

    /**
     * @return BatchActionInterface[]
     */
    public function getBatchActions();

    /**
     * @param ActionColumnInterface $rowActionCollection
     */
    public function addActionColumn(ActionColumnInterface $rowActionCollection);

    /**
     * @return ActionColumnInterface[]
     */
    public function getActionColumns();

    /**
     * @return GridResultInterface
     */
    public function getGridResult();
}
