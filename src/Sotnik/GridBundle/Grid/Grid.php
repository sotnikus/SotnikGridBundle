<?php

namespace Sotnik\GridBundle\Grid;

use Sotnik\GridBundle\Column\ColumnInterface;
use Doctrine\ORM\QueryBuilder;
use Sotnik\GridBundle\Exception\GridException;
use Sotnik\GridBundle\Exception\InvalidGridIdException;
use Sotnik\GridBundle\Exception\InvalidValueException;
use Symfony\Component\HttpFoundation\Request;
use Sotnik\GridBundle\Pagination\Pagination;
use Twig_Environment;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sotnik\GridBundle\Pagination\PaginationInterface;

class Grid implements GridInterface
{
    const GRID_TEMPLATE = 'SotnikGridBundle::gridTemplate.html.twig';

    const PAGE_PARAMETER = 'page';

    const PER_PAGE_PARAMETER = 'per-page';

    const SORT_PARAMETER = 'sort';

    const GRID_ROW_TEMPLATE_BLOCK_NAME = 'grid_row';

    const GRID_TABLE_TEMPLATE_BLOCK_NAME = 'grid_table';

    const GRID_PAGINATION_BLOCK_NAME = 'pagination';

    const GRID_FILTERS_BLOCK_NAME = 'filters';

    const GRID_FILTER_BLOCK_NAME = 'column_filter_collection';

    /**
     * @var string
     */
    private $id = '';

    /**
     * @var ColumnInterface[]
     */
    private $columns = array();

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ContainerInterface
     */
    private $serviceContainer;

    /**
     * @var string
     */
    private $gridTemplate = self::GRID_TEMPLATE;

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var PaginationInterface
     */
    private $pagination;

    /**
     * @var array
     */
    private $perPageOptions = [20, 50, 100];

    public function __construct($serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
        $this->request = $this->serviceContainer->get('request');
        $this->twig = $this->serviceContainer->get('twig');
    }

    /**
     * @param string $gridTemplate
     */
    public function setGridTemplate($gridTemplate)
    {
        $this->gridTemplate = $gridTemplate;
    }

    /**
     * @return string
     */
    public function getGridTemplate()
    {
        return $this->gridTemplate;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    public function setSource(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return queryBuilder
     */
    public function getSource()
    {
        return $this->queryBuilder;
    }

    /**
     * @param array $perPageOptions
     * @throws InvalidValueException
     * @return mixed
     */
    public function setPerPageOptions(array $perPageOptions)
    {
        $nonInt = array_filter(
            $perPageOptions,
            function ($el) {
                return is_integer($el) ? true : false;
            }
        );

        if (count($nonInt) > 0) {
            throw new InvalidValueException('perPageOptions contains non-integer value');
        }

        $this->perPageOptions = $perPageOptions;
    }

    /**
     * @return array
     */
    public function getPerPageOptions()
    {
        return $this->perPageOptions;
    }


    /**
     * @param $id
     * @throws InvalidGridIdException
     * @return mixed
     */
    public function setId($id)
    {
        $matches = [];
        preg_match('#^[a-zA-Z1-9]+?#U', $id, $matches);
        if (!isset($matches[0]) || $matches[0] != $id) {
            throw new InvalidGridIdException(sprintf(
                'Invalid grid id="%s", value should match a pattern: %s',
                $id,
                '^[a-zA-Z1-9]+?'
            ));
        }

        $this->id = $id . '_';
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param ColumnInterface $column
     * @return mixed|void
     */
    public function addColumn(ColumnInterface $column)
    {
        $this->columns[$column->getId()] = $column;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    //--- render ---

    private function renderFilters()
    {
        $filterCollections = [];

        foreach ($this->getColumns() as $column) {
            $columnFilterCollection = $column->getFilterCollection();

            if ($columnFilterCollection->count() > 0) {

                $filterCollections[] = [
                    'columnId' => $column->getId(),
                    'label' => $column->getLabel(),
                    'filters' => $columnFilterCollection->getFilters(),
                ];
            }
        }

        $templateContent = $this->twig->loadTemplate($this->getGridTemplate());
        return $templateContent->renderBlock(
            self::GRID_FILTERS_BLOCK_NAME,
            [
                'gridId' => $this->getId(),
                'route' => $this->request->get('_route'),
                'columnsFilters' => $filterCollections,
                'queryParams' => $this->request->query->all(),
                'perPage' => $this->pagination->getMaxPerPage()
            ]
        );
    }

    private function renderGridRow($row)
    {
        $columnValues = [];

        foreach ($this->getColumns() as $column) {

            $columnTemplate = $column->getTemplate();

            if (empty($columnTemplate)) {
                $result = $column->getValueOfResultRow($row);
            } else {
                $templateContent = $this->twig->loadTemplate($columnTemplate);
                $result = $templateContent->render(['value' => $column->getValueOfResultRow($row)]);
            }

            $columnValues[] = $result;
        }

        $templateContent = $this->twig->loadTemplate($this->getGridTemplate());
        return $templateContent->renderBlock(self::GRID_ROW_TEMPLATE_BLOCK_NAME, ['columns' => $columnValues]);

    }

    private function renderGridTable($rows)
    {
        $renderedRows = [];

        foreach ($rows as $row) {
            $renderedRows[] = $this->renderGridRow($row);
        }

        $templateContent = $this->twig->loadTemplate($this->getGridTemplate());
        return $templateContent->renderBlock(
            self::GRID_TABLE_TEMPLATE_BLOCK_NAME,
            [
                'rows' => $renderedRows,
                'columns' => $this->getColumns(),
                'route' => $this->request->get('_route'),
                'queryParams' => $this->request->query->all(),
                'sortParameterName' => $this->getId() . self::SORT_PARAMETER
            ]
        );
    }

    private function applyQueryToPagination()
    {
        $this->pagination->setCurrentPage(
            $this->request->query->get(
                $this->getId() . self::PAGE_PARAMETER,
                $this->pagination->getCurrentPage()
            )
        );

        $this->pagination->setMaxPerPage(
            $this->request->query->get(
                $this->getId() . self::PER_PAGE_PARAMETER,
                $this->getPerPageOptions()[0]
            )
        );
    }

    private function renderPagination()
    {
        $templateContent = $this->twig->loadTemplate($this->getGridTemplate());
        return $templateContent->renderBlock(
            self::GRID_PAGINATION_BLOCK_NAME,
            [
                'totalPages' => $this->pagination->getTotalPages(),
                'totalCount' => $this->pagination->getTotalCount(),
                'currentPage' => $this->pagination->getCurrentPage(),
                'pageParameterName' => $this->getId() . self::PAGE_PARAMETER,
                'route' => $this->request->get('_route'),
                'queryParams' => $this->request->query->all(),
                'gridId' => $this->getId(),
                'perPageOptions' => $this->getPerPageOptions(),
                'selectedPerPageOption' => $this->pagination->getMaxPerPage()
            ]
        );
    }

    /**
     * @throws GridException
     * @return GridResultInterface
     */
    public function getGridResult()
    {
        if ($this->getSource() === null) {
            throw new GridException('Source is not defined');
        }

        $requestToQueryBuilderAdapter = new RequestToQueryBuilderAdapter($this->getSource(), $this->getColumns(
        ), $this->request);
        $requestToQueryBuilderAdapter->setParamsPrefix($this->getId());

        $this->setSource($requestToQueryBuilderAdapter->getAdaptedQueryBuilder());

        $this->pagination = new Pagination($this->getSource());

        $this->applyQueryToPagination();

        $result = $this->pagination->getResult();

        $html = $this->renderFilters() . $this->renderGridTable($result) . $this->renderPagination();

        $gridResult = new GridResult($this->pagination->getQuery(), $html);

        return $gridResult;
    }
}
