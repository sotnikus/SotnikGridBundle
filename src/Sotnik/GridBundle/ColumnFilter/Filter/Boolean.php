<?php
namespace Sotnik\GridBundle\ColumnFilter\Filter;

use Doctrine\ORM\QueryBuilder;

class Boolean extends BaseFilter implements ColumnFilterInterface, SelectFilterInterface
{
    private $cases = [];

    public function __construct($trueLabel = 'true', $falseLabel = 'false')
    {
        $this->cases = [
            ['value' => 1, 'label' => $trueLabel],
            ['value' => 0, 'label' => $falseLabel],
        ];
    }

    public function getCases()
    {
        return $this->cases;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function transformQuery(QueryBuilder $queryBuilder)
    {
        $paramName = $this->getParamName();

        $qb = $queryBuilder->andWhere($this->queryMapping . ' = :' . $paramName);

        if ($this->getValue() == 1) {
            $qb->setParameter($paramName, true);
        } else {
            $qb->setParameter($paramName, false);
        }

    }

    public function getRenderType()
    {
        return self::SELECT;
    }
}
