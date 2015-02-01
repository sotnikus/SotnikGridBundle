<?php
namespace Sotnik\GridBundle\ColumnFilter\Filter;

use Doctrine\ORM\QueryBuilder;

class MultipleSelect extends Select implements ColumnFilterInterface, SelectFilterInterface
{
    private $size = 4;

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function transformQuery(QueryBuilder $queryBuilder)
    {
        $paramName = $this->getParamName();

        return $queryBuilder->andWhere($this->queryMapping . ' IN (:' . $paramName . ')')
            ->setParameter($paramName, $this->getValue());
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getRenderType()
    {
        return self::MULTI_SELECT;
    }
}
