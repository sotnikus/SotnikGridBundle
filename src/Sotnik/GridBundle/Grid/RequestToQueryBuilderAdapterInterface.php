<?php
namespace Sotnik\GridBundle\Grid;

use Doctrine\ORM\QueryBuilder;

interface RequestToQueryBuilderAdapterInterface
{
    /**
     * @param string $paramsPrefix
     * @return void
     */
    public function setParamsPrefix($paramsPrefix);

    /**
     * @return QueryBuilder
     */
    public function getAdaptedQueryBuilder();
}
