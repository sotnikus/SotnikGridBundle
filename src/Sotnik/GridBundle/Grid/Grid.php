<?php

namespace Sotnik\GridBundle\Grid;

use Doctrine\ORM\Query;
use Sotnik\GridBundle\Batch\BatchActionInterface;
use Sotnik\GridBundle\Batch\BatchRequestHandler;
use Sotnik\GridBundle\Column\ColumnInterface;
use Doctrine\ORM\QueryBuilder;
use Sotnik\GridBundle\Exception\GridException;
use Sotnik\GridBundle\Exception\InvalidArgumentException;
use Sotnik\GridBundle\Exception\InvalidGridIdException;
use Sotnik\GridBundle\Exception\InvalidValueException;
use Sotnik\GridBundle\RowAction\ActionColumnInterface;
use Sotnik\GridBundle\RowAction\Action;
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

    const GRID_BLOCK_NAME = 'grid';

    const GRID_ROW_TEMPLATE_BLOCK_NAME = 'grid_row';

    const GRID_TABLE_TEMPLATE_BLOCK_NAME = 'grid_table';

    const GRID_PAGINATION_BLOCK_NAME = 'pagination';

    const GRID_FILTERS_BLOCK_NAME = 'filters';

    const GRID_FILTER_BLOCK_NAME = 'column_filter_collection';

    const GRID_INLINE_ACTION_COLUMN_BLOCK_NAME = 'inline_action_column';

    const GRID_DROP_DOWN_ACTION_COLUMN_BLOCK_NAME = 'drop_down_action_column';

    /**
     * @var string
     */
    private $id = '';

    /**
     * @var ColumnInterface[]
     */
    private $columns = array();

    /**
     * @var BatchActionInterface[]
     */
    private $batchActions = [];

    /**
     * @var \Closure
     */
    private $batchActionIdGetter;

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
     * @var int
     */
    private $hydrationMode;

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
    private $perPageLimits = [20, 50, 100];

    /**
     * @var ActionColumnInterface[]
     */
    private $rowActions = [];

    /**
     * @var bool
     */
    private $leftJoinCollection = true;

    public function __construct($serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
        $this->request = $this->serviceContainer->get('request');
        $this->twig = $this->serviceContainer->get('twig');

        $this->setPerPageLimits($serviceContainer->getParameter('sotnik_grid.per_page_limits'));
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
     * @param QueryBuilder $queryBuilder
     * @param int $hydrationMode
     * @param bool $leftJoinCollection
     */
    public function setSource(
        QueryBuilder $queryBuilder,
        $hydrationMode = Query::HYDRATE_OBJECT,
        $leftJoinCollection = true
    ) {
        $this->hydrationMode = $hydrationMode;
        $this->queryBuilder = $queryBuilder;
        $this->leftJoinCollection = $leftJoinCollection;
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
    public function setPerPageLimits(array $perPageOptions)
    {
        $nonInt = array_filter(
            $perPageOptions,
            function ($el) {
                return !is_integer($el);
            }
        );

        if (count($nonInt) > 0) {
            throw new InvalidValueException('perPageLimits contains non-integer value');
        }

        $this->perPageLimits = $perPageOptions;
    }

    /**
     * @return array
     */
    public function getPerPageLimits()
    {
        return $this->perPageLimits;
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
        $column->setHydrationMode($this->hydrationMode);

        $this->columns[$column->getId()] = $column;
    }

    /**
     * @return ColumnInterface[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param string $idGetter
     * @param BatchActionInterface[] $batchActions
     * @throws InvalidArgumentException
     */
    public function setBatchActions($idGetter, array $batchActions)
    {
        $this->batchActionIdGetter = GridHelper::getQueryMapperClosure($idGetter, $this->hydrationMode);

        foreach ($batchActions as $batchAction) {
            if (!$batchAction instanceof BatchActionInterface) {
                throw new InvalidArgumentException('All batch actions should be instance of BatchActionInterface');
            }
            $this->batchActions[] = $batchAction;
        }
    }

    /**
     * @return BatchActionInterface[]
     */
    public function getBatchActions()
    {
        return $this->batchActions;
    }

    /**
     * @param ActionColumnInterface $rowActionCollection
     */
    public function addActionColumn(ActionColumnInterface $rowActionCollection)
    {
        $this->rowActions[] = $rowActionCollection;
    }

    /**
     * @return ActionColumnInterface[]
     */
    public function getActionColumns()
    {
        return $this->rowActions;
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
        $columns = [];

        foreach ($this->getColumns() as $index => $column) {

            $columns[$index]['isRaw'] = $column->getIsRaw();

            $columnTemplate = $column->getTemplate();

            if (empty($columnTemplate)) {
                $result = $column->getValueOfResultRow($row);
            } else {
                $templateContent = $this->twig->loadTemplate($columnTemplate);
                $result = $templateContent->render(['value' => $column->getValueOfResultRow($row)]);
                $columns[$index]['isRaw'] = true;
            }

            $columns[$index]['value'] = $result;

        }

        $batchId = null;
        if (!empty($this->batchActions)) {
            $idGetterFunction = $this->batchActionIdGetter;
            $batchId = $idGetterFunction($row);
        };

        $templateContent = $this->twig->loadTemplate($this->getGridTemplate());
        return $templateContent->renderBlock(
            self::GRID_ROW_TEMPLATE_BLOCK_NAME,
            [
                'batchId' => $batchId,
                'columns' => $columns,
                'gridId' => $this->getId(),
                'actionColumns' => $this->renderActionColumns($row),
            ]
        );
    }

    private function renderActionColumns($row)
    {
        $rowActions = [];
        $templateContent = $this->twig->loadTemplate($this->getGridTemplate());
        foreach ($this->getActionColumns() as $rowAction) {
            $actions = $rowAction->getActions();
            array_map(
                function ($el) use ($row) {
                    /** @var $el Action */
                    return $el->handleUrlGetter($row);
                },
                $actions
            );

            $blockName = strtolower(
                preg_replace('/([a-z])([A-Z])/', '$1_$2', (new \ReflectionClass($rowAction))->getShortName())
            );
            $rowActions[] = $templateContent->renderBlock($blockName, ['actions' => $actions]);
        }

        return $rowActions;
    }

    private function renderGridTable($rows)
    {
        $renderedRows = [];

        foreach ($rows as $row) {
            $renderedRows[] = $this->renderGridRow($row);
        }

        $batchId = null;
        if (!empty($this->batchActions)) {
            $batchId = true;
        };

        $templateContent = $this->twig->loadTemplate($this->getGridTemplate());
        return $templateContent->renderBlock(
            self::GRID_TABLE_TEMPLATE_BLOCK_NAME,
            [
                'batchId' => $batchId,
                'gridId' =>$this->getId(),
                'rows' => $renderedRows,
                'columns' => $this->getColumns(),
                'actionColumns' => $this->getActionColumns(),
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
                $this->getPerPageLimits()[0]
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
                'perPageLimits' => $this->getPerPageLimits(),
                'selectedPerPageLimit' => $this->pagination->getMaxPerPage()
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

        $batchRequestHandler = new BatchRequestHandler(
            $this->request,
            $this->getId(),
            $this->getBatchActions()
        );

        $batchRequestHandler->handle();

        $requestToQueryBuilderAdapter = new RequestToQueryBuilderAdapter(
            $this->getSource(),
            $this->getColumns(),
            $this->request
        );

        $requestToQueryBuilderAdapter->setParamsPrefix($this->getId());

        $this->setSource($requestToQueryBuilderAdapter->getAdaptedQueryBuilder(), $this->hydrationMode);

        $this->pagination = new Pagination($this->getSource(), $this->leftJoinCollection);

        $this->applyQueryToPagination();

        $result = $this->pagination->getResult($this->hydrationMode);

        $templateContent = $this->twig->loadTemplate($this->getGridTemplate());

        $html = $templateContent->renderBlock(
            self::GRID_BLOCK_NAME,
            [
                'batchActions' => $this->getBatchActions(),
                'filter' => $this->renderFilters(),
                'table' => $this->renderGridTable($result),
                'pagination' => $this->renderPagination(),
                'gridId' => $this->getId(),
                'pageParam' => self::PAGE_PARAMETER,
                'sortParam' => self::SORT_PARAMETER,
                'prePageParam' => self::PER_PAGE_PARAMETER
            ]
        );

        $gridResult = new GridResult($this->pagination->getQuery(), $html);

        return $gridResult;
    }
}
