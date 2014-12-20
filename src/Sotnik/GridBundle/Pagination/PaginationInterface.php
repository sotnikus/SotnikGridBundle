<?php
namespace Sotnik\GridBundle\Pagination;

interface PaginationInterface
{
    /**
     * @param integer $currentPage
     * @return void
     */
    public function setCurrentPage($currentPage);

    /**
     * @return integer
     */
    public function getCurrentPage();

    /**
     * @param integer $maxPerPage
     * @return mixed
     */
    public function setMaxPerPage($maxPerPage);

    /**
     * @return integer
     */
    public function getMaxPerPage();

    /**
     * @return integer
     */
    public function getResult();

    /**
     * @return integer
     */
    public function getTotalCount();

    /**
     * @return integer
     */
    public function getTotalPages();

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQuery();
}
