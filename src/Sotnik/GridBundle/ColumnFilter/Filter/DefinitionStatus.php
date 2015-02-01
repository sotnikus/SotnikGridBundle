<?php
namespace Sotnik\GridBundle\ColumnFilter\Filter;

use Doctrine\ORM\QueryBuilder;

class DefinitionStatus extends BaseFilter implements ColumnFilterInterface, SelectFilterInterface
{
    const  IS_DEFINED = 'is-defined';
    const  IS_NOT_DEFINED = 'is-not-defined';

    public function getCases()
    {
        return [
            ['value' => self::IS_DEFINED, 'label' => self::IS_DEFINED],
            ['value' => self::IS_NOT_DEFINED, 'label' => self::IS_NOT_DEFINED],
        ];
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function transformQuery(QueryBuilder $queryBuilder)
    {
        if ($this->getValue() == self::IS_DEFINED) {
            $qb = $queryBuilder->andWhere($this->queryMapping . ' IS NOT NULL');
        } else {
            $qb = $queryBuilder->andWhere($this->queryMapping . ' IS NULL');
        }

        return $qb;
    }

    public function getRenderType()
    {
        return ColumnFilterInterface::SELECT;
    }
}
