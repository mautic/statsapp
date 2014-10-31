<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) 2014 WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace StatsApp\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;
use StatsApp\Factory;

/**
 * Class BaseRepository
 */
class BaseRepository extends EntityRepository
{

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * Set the Factory object
     *
     * @param Factory $factory
     *
     * @return void
     */
    public function setFactory(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Get a single entity
     *
     * @param int $id
     *
     * @return null|object
     */
    public function getEntity($id = 0)
    {
        return $this->find($id);
    }

    /**
     * Get a list of entities
     *
     * @param array $args
     *
     * @return Paginator
     */
    public function getEntities($args = array())
    {
        $alias = $this->getTableAlias();

        if (isset($args['qb'])) {
            $q = $args['qb'];
        } else {
            $q = $this->createQueryBuilder($alias)
                ->select($alias);
        }

        $this->buildClauses($q, $args);
        $query = $q->getQuery();

        if (isset($args['hydration_mode'])) {
            $mode = strtoupper($args['hydration_mode']);
            $query->setHydrationMode(constant("\\Doctrine\\ORM\\Query::$mode"));
        }

        return new Paginator($query);
    }

    /**
     * Save an entity through the repository
     *
     * @param object $entity
     * @param bool   $flush true by default; use false if persisting in batches
     *
     * @return void
     */
    public function saveEntity($entity, $flush = true)
    {
        $this->_em->persist($entity);

        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Persist an array of entities
     *
     * @param array $entities
     *
     * @return void
     */
    public function saveEntities(array $entities)
    {
        // Iterate over the results so the events are dispatched on each delete
        $batchSize = 20;
        foreach ($entities as $k => $entity) {
            $this->saveEntity($entity, false);

            if ((($k + 1) % $batchSize) === 0) {
                $this->_em->flush();
            }
        }

        $this->_em->flush();
    }

    /**
     * Delete an entity through the repository
     *
     * @param object $entity
     * @param bool   $flush true by default; use false if persisting in batches
     *
     * @return void
     */
    public function deleteEntity($entity, $flush = true)
    {
        $this->_em->remove($entity);

        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Builds the additional clauses for a query
     *
     * @param \Doctrine\ORM\QueryBuilder $q
     * @param array                      $args
     *
     * @return bool
     */
    protected function buildClauses(&$q, array $args)
    {
        $this->buildWhereClause($q, $args);
        $this->buildOrderByClause($q, $args);
        $this->buildLimiterClauses($q, $args);

        return true;
    }

    /**
     * Builds the WHERE clauses for a query
     *
     * @param \Doctrine\ORM\QueryBuilder $q
     * @param array                      $args
     *
     * @return void
     */
    protected function buildWhereClause(&$q, array $args)
    {
        $filter = array_key_exists('filter', $args) ? $args['filter'] : '';
        $string = '';

        if (!empty($filter)) {
            if (is_array($filter)) {
                if (isset($filter['force']) && !empty($filter['force'])) {
                    // Defined columns with keys of column, expr, value
                    $forceParameters  = array();
                    $forceExpressions = $q->expr()->andX();

                    foreach ($filter['force'] as $f) {
                        list($expr, $parameters) = $this->getFilterExpr($q, $f);

                        $forceExpressions->add($expr);

                        if (is_array($parameters)) {
                            $forceParameters = array_merge($forceParameters, $parameters);
                        }
                    }
                }

                if (!empty($filter['string'])) {
                    $string = $filter['string'];
                }
            } else {
                $string = $filter;
            }

            // Parse the filter if set
            if (!empty($string) || !empty($forceExpressions)) {
                if (!empty($forceExpressions)) {
                    // We have some required filters
                    $filterCount = ($forceExpressions instanceof \Countable) ? count($forceExpressions) : count($forceExpressions->getParts());

                    if (!empty($filterCount)) {
                        $q->where($forceExpressions)
                          ->setParameters($forceParameters);
                    }
                }
            }
        }
    }

    /**
     * Gets the filter expressions for a query
     *
     * @param \Doctrine\ORM\QueryBuilder $q
     * @param array                      $filter
     *
     * @return array
     */
    protected function getFilterExpr(&$q, $filter)
    {
        $unique    = $this->generateRandomParameterName();
        $func      = (!empty($filter['operator'])) ? $filter['operator'] : $filter['expr'];
        $parameter = false;
        if (in_array($func, array('isNull', 'isNotNull'))) {
            $expr = $q->expr()->{$func}($filter['column']);
        } elseif (in_array($func, array('in', 'notIn'))) {
            $expr = $q->expr()->{$func}($filter['column'], $filter['value']);
        } else {
            if (isset($filter['strict']) && !$filter['strict']) {
                $filter['value'] = "%{$filter['value']}%";
            }
            $expr      = $q->expr()->{$func}($filter['column'], ':' . $unique);
            $parameter = array($unique => $filter['value']);
        }

        if (!empty($filter['not'])) {
            $expr = $q->expr()->not($expr);
        }

        return [$expr, $parameter];
    }

    /**
     * Builds a catch all WHERE clause for a query
     *
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param array                      $filter
     *
     * @return array
     */
    protected function addCatchAllWhereClause(&$qb, $filter)
    {
        return [false, array()];
    }

    /**
     * Builds the ORDER clauses for a query
     *
     * @param \Doctrine\ORM\QueryBuilder $q
     * @param array                      $args
     *
     * @return void
     */
    protected function buildOrderByClause(&$q, array $args)
    {
        $orderBy    = array_key_exists('orderBy', $args) ? $args['orderBy'] : '';
        $orderByDir = array_key_exists('orderByDir', $args) ? $args['orderByDir'] : '';
        if (empty($orderBy)) {
            $defaultOrder = $this->getDefaultOrder();

            foreach ($defaultOrder as $order) {
                $q->addOrderBy($order[0], $order[1]);
            }
        } else {
            // Add direction after each column
            $parts = explode(',', $orderBy);

            foreach ($parts as $order) {
                $q->addOrderBy($order, $orderByDir);
            }
        }
    }

    /**
     * Get the default ordering for a query
     *
     * @return array
     */
    protected function getDefaultOrder()
    {
        return array();
    }

    /**
     * Builds the LIMIT clauses for a query
     *
     * @param \Doctrine\ORM\QueryBuilder $q
     * @param array                      $args
     *
     * @return void
     */
    protected function buildLimiterClauses(&$q, array $args)
    {
        $start = array_key_exists('start', $args) ? $args['start'] : 0;
        $limit = array_key_exists('limit', $args) ? $args['limit'] : 30;

        if (!empty($limit)) {
            $q->setFirstResult($start)
                ->setMaxResults($limit);
        }
    }

    /**
     * Generates a random parameter name
     *
     * @return string
     */
    protected function generateRandomParameterName()
    {
        $alpha_numeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        return substr(str_shuffle($alpha_numeric), 0, 8);
    }

    /**
     * Returns a andX Expr() that takes into account isPublished, publishUp and publishDown dates
     * The Expr() sets a :now parameter that must be set in the calling function
     *
     * @param \Doctrine\ORM\QueryBuilder $q
     * @param string                     $alias
     *
     * @return \Doctrine\ORM\Query\Expr\AndX
     */
    public function getPublishedByDateExpression($q, $alias = null)
    {
        if ($alias === null) {
            $alias = $this->getTableAlias();
        }

        return $q->expr()->andX(
            $q->expr()->eq("$alias.isPublished", true),
            $q->expr()->orX(
                $q->expr()->isNull("$alias.publishUp"),
                $q->expr()->gte("$alias.publishUp", ':now')
            ),
            $q->expr()->orX(
                $q->expr()->isNull("$alias.publishDown"),
                $q->expr()->lte("$alias.publishDown", ':now')
            )
        );
    }

    /**
     * Retrieves the table's alias
     *
     * @return string
     */
    public function getTableAlias()
    {
        return 'e';
    }

    /**
     * Gets the properties of an ORM entity
     *
     * @param string $entityClass
     * @param bool   $convertCamelCase
     *
     * @return array
     */
    public function getBaseColumns($entityClass, $convertCamelCase = false)
    {
        static $baseCols = array();

        if (empty($baseCols[$entityClass])) {
            // Get a list of properties from the entity
            $entity  = new $entityClass();
            $reflect = new \ReflectionClass($entity);
            $props   = $reflect->getProperties();

            if ($parentClass = $reflect->getParentClass()) {
                $parentProps = $parentClass->getProperties();
                $props       = array_merge($parentProps, $props);
            }

            $baseCols[$entityClass] = array();

            foreach ($props as $p) {
                if (!in_array($p->name, $baseCols[$entityClass])) {
                    $n = $p->name;

                    if ($convertCamelCase) {
                        $n = preg_replace('/(?<=\\w)(?=[A-Z])/', "_$1", $n);
                        $n = strtolower($n);
                    }

                    $baseCols[$entityClass][] = $n;
                }
            }
        }

        return $baseCols[$entityClass];
    }

    /**
     * Examines the arguments passed to getEntities and converts ORM properties to dBAL column names
     *
     * @param string $entityClass
     * @param array  $args
     *
     * @return array
     */
    public function convertOrmProperties($entityClass, array $args)
    {
        $properties = $this->getBaseColumns($entityClass);

        // Check force filters
        if (isset($args['filter']['force']) && is_array($args['filter']['force'])) {
            foreach ($args['filter']['force'] as $k => &$f) {
                $col   = $f['column'];
                $alias = '';

                if (strpos($col, '.') !== false) {
                    list($alias, $col) = explode('.', $col);
                }

                if (in_array($col, $properties)) {
                    $col = preg_replace('/(?<=\\w)(?=[A-Z])/', "_$1", $col);
                    $col = strtolower($col);
                }

                $f['column'] = (!empty($alias)) ? $alias . '.' . $col : $col;
            }
        }

        // Check order by
        if (isset($args['order'])) {
            if (is_array($args['order'])) {
                foreach ($args['order'] as &$o) {
                    $alias = '';

                    if (strpos($o, '.') !== false) {
                        list($alias, $o) = explode('.', $o);
                    }

                    if (in_array($o, $properties)) {
                        $o = preg_replace('/(?<=\\w)(?=[A-Z])/', "_$1", $o);
                        $o = strtolower($o);
                    }

                    $o = (!empty($alias)) ? $alias . '.' . $o : $o;
                }
            }
        }

        return $args;
    }
}
