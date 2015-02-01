<?php
namespace Sotnik\GridBundle\Pagination;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class Pagination implements PaginationInterface
{

    private $currentPage = 1;

    private $maxPerPage = 10;

    private $totalCount = 0;

    private $totalPages = 0;

    private $query;

    private $leftJoinCollection = true;

    public function __construct(QueryBuilder $queryBuilder, $leftJoinCollection = true)
    {
        $this->query = $queryBuilder;
        $this->leftJoinCollection = $leftJoinCollection;
    }

    /**
     * @param integer $currentPage
     * @return void
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param integer $maxPerPage
     * @return mixed
     */
    public function setMaxPerPage($maxPerPage)
    {
        $this->maxPerPage = $maxPerPage > 0 ? $maxPerPage : $this->maxPerPage;
    }

    /**
     * @return integer
     */
    public function getMaxPerPage()
    {
        return $this->maxPerPage;
    }

    /**
     * @return integer
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @return integer
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * @return bool
     */
    public function getLeftJoinCollection()
    {
        return $this->leftJoinCollection;
    }

    /**
     * @return QueryBuilder
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param int $hydrationMode
     * @return Paginator|int
     * @throws \InvalidArgumentException
     */
    public function getResult($hydrationMode)
    {
        if (!in_array($hydrationMode, [Query::HYDRATE_OBJECT || Query::HYDRATE_ARRAY])) {
            throw new \InvalidArgumentException('Invalid hydration mode. Should be HYDRATE_OBJECT or HYDRATE_ARRAY');
        }

        $firstResult = $this->maxPerPage * ($this->currentPage - 1);

        $this->query = $this->query->setFirstResult($firstResult)->setMaxResults($this->maxPerPage);

        $result = new Paginator(
            $this->query->getQuery()->setHydrationMode($hydrationMode),
            $this->getLeftJoinCollection()
        );

        $this->totalCount = count($result);

        if ($this->totalCount % $this->maxPerPage > 0) {
            $this->totalPages = intval($this->totalCount / $this->maxPerPage) + 1;
        } else {
            $this->totalPages = intval($this->totalCount / $this->maxPerPage);
        }

        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->setCurrentPage($this->totalPages);
            $result = $this->getResult($hydrationMode);
        }

        return $result;
    }
}