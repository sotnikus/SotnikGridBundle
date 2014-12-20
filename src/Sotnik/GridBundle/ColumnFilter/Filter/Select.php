<?php
namespace Sotnik\GridBundle\ColumnFilter\Filter;

use Doctrine\ORM\QueryBuilder;

class Select extends BaseFilter implements ColumnFilterInterface
{
    private $cases = [];

    public function __construct(array $cases)
    {
        foreach ($cases as $key => $case) {
            if (is_int($key)) {
                $this->cases[] = ['value' => $case, 'label' => $case];
            } else {
                $this->cases[] = ['value' => $key, 'label' => $case];
            }
        }
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

        return $queryBuilder->andWhere($this->queryMapping . ' = :' . $paramName)
            ->setParameter($paramName, $this->getValue());
    }

    public function getRenderType()
    {
        return self::SELECT;
    }
}
