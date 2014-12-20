<?php
namespace Sotnik\GridBundle\Grid;

use Doctrine\ORM\QueryBuilder;
use Sotnik\GridBundle\Column\ColumnInterface;
use Sotnik\GridBundle\Exception\FilterNotFoundException;
use Sotnik\GridBundle\Exception\InvalidArgumentException;
use Sotnik\GridBundle\Exception\InvalidRequestParameterValue;
use Symfony\Component\HttpFoundation\Request;
use Sotnik\GridBundle\ColumnSort\ColumnSortInterface;

class RequestToQueryBuilderAdapter implements RequestToQueryBuilderAdapterInterface
{

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * array with key of column id
     *
     * @var ColumnInterface[]
     */
    private $columns;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $paramsPrefix = '';

    public function __construct(QueryBuilder $queryBuilder, array $columns, Request $request)
    {
        $this->queryBuilder = $queryBuilder;
        $this->columns = $columns;
        $this->request = $request;
    }

    private function adaptSortPart()
    {
        $queryParams = $this->request->query->all();

        $sortParam = $this->paramsPrefix . Grid::SORT_PARAMETER;

        if (isset($queryParams[$sortParam])) {

            //validate
            $matches = [];
            preg_match('#^([a-zA_Z1-9_-])+~(desc|asc)$#', $queryParams[$sortParam], $matches);

            if (!isset($matches[0]) || $matches[0] != $queryParams[$sortParam]) {
                throw new InvalidRequestParameterValue(
                    sprintf('Invalid request sort parameter value "%s"', $queryParams[$sortParam])
                );
            }

            $sortParts = explode('~', $queryParams[$sortParam]);
            $direction = array_pop($sortParts);
            $columnId = implode('~', $sortParts);

            if (isset($this->columns[$columnId])) {
                if ($direction == ColumnSortInterface::DIRECTION_DESC) {
                    $columnSort = $this->columns[$columnId]->getColumnSort();
                    $columnSort->setDescQueryPart($this->queryBuilder);
                }

                if ($direction == ColumnSortInterface::DIRECTION_ASC) {
                    $columnSort = $this->columns[$columnId]->getColumnSort();
                    $columnSort->setAscQueryPart($this->queryBuilder);
                }
            }
        }
    }

    private function adaptFilterPart()
    {
        $queryParams = $this->request->query->all();

        foreach ($queryParams as $param => $value) {

            if ($value == "" ||
                in_array($param, [Grid::SORT_PARAMETER, Grid::PAGE_PARAMETER, Grid::PER_PAGE_PARAMETER])) {
                continue;
            }

            $matches = [];
            preg_match('#^' . $this->paramsPrefix . '([a-zA_Z_-])+~([a-z-])+.*?#', $param, $matches);
            if (!isset($matches[0]) || $matches[0] != $param) {
                continue;
            }

            if ($this->paramsPrefix != '') {
                $param = substr($param, strlen($this->paramsPrefix));
            }

            $paramParts = explode('~', $param);

            $columnId = $paramParts[0];
            $filterName = $paramParts[1];

            if (isset($this->columns[$columnId])) {

                $filter = $this->columns[$columnId]->getFilterCollection()->getFilter($filterName);
                if ($filter === null) {
                    throw new FilterNotFoundException(sprintf('Filter "%s" is not found', $filterName));
                }
                $filter->setValue($value);
                $filter->transformQuery($this->queryBuilder);
            }
        }
    }

    public function setParamsPrefix($paramsPrefix)
    {
        if ($paramsPrefix != '') {
            $matches = [];
            preg_match('#^[a-zA-Z1-9_]+?#U', $paramsPrefix, $matches);
            if (!isset($matches[0]) || $matches[0] != $paramsPrefix) {
                throw new InvalidArgumentException(
                    sprintf('Invalid grid id="%s", value should match a pattern: %s', $paramsPrefix, '^[a-zA-Z1-9_]+?')
                );
            }
        }

        $this->paramsPrefix = $paramsPrefix;
    }

    /**
     * @return QueryBuilder
     */
    public function getAdaptedQueryBuilder()
    {
        $this->adaptSortPart();
        $this->adaptFilterPart();

        return $this->queryBuilder;
    }
}
