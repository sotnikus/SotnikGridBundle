<?php
namespace Sotnik\GridBundle\Pagination;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class Pagination implements PaginationInterface
{

    private $currentPage = 1;

    private $maxPerPage = 10;

    private $totalCount = 0;

    private $totalPages = 0;

    private $query;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->query = $queryBuilder;
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
        $this->maxPerPage = $maxPerPage;
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
     * @return QueryBuilder
     */
    public function getQuery()
    {
        return $this->query;
    }

    public function getResult()
    {

        $firstResult = $this->maxPerPage * ($this->currentPage - 1);

        $this->query = $this->query->setFirstResult($firstResult)->setMaxResults($this->maxPerPage);

        $result = new Paginator($this->query, true);

        $this->totalCount = count($result);

        if ($this->totalCount % $this->maxPerPage > 0) {
            $this->totalPages = intval($this->totalCount / $this->maxPerPage) + 1;
        } else {
            $this->totalPages = intval($this->totalCount / $this->maxPerPage);
        }

        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->setCurrentPage($this->totalPages);
            $result = $this->getResult();
        }

        return $result;
    }
}