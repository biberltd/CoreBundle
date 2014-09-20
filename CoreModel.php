<?php

/**
 * CoreModel Class
 *
 * This class provides core foundations for Biber Ltd. Model Services.
 *
 * @vendor      BiberLtd
 * @package	    BiberLtd\Core
 * @name	    CoreModel
 *
 * @author		Can Berkol
 *              Said İmamoğlu
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.2.3
 * @date        17.06.2014
 *
 */

namespace BiberLtd\Bundle\CoreBundle;

/** Required for better & instant error handling for the support team */
use BiberLtd\Bundle\CoreBundle\Exceptions as CoreExceptions;

class CoreModel {

    /** @var $db_connection     string          Decides which DB connection to be used. Defaults to "default" */
    protected $db_connection = 'default';

    /** @var $orm               string          Decides which ORM to use. Defaults to "doctrine" */
    protected $orm = 'doctrine';

    /** @var $em                EntityManager */
    protected $em;

    /** @var $entity            Stores entity names to be used. */
    protected $entity;

    /** @var $kernel            Application Kernel */
    protected $kernel;

    /** @var $response          Returned response */
    protected $response = array();

    /** @var $languages          Registered languages */
    protected $languages = array();

    /**
     * @name            __construct()
     *                  Constructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           object          $kernel
     * @param           string          $db_connection  Database connection key as set in app/config.yml
     * @param           string          $orm            ORM that is used.
     */
    public function __construct($kernel, $db_connection = 'default', $orm = 'doctrine') {
        $this->db_connection = $db_connection;
        $this->orm = $orm;
        $this->kernel = $kernel;
        $this->response['rowCount'] = 0;
        /**
         * Set the connection with the required database.
         */
        $this->em = $this->kernel->getContainer()->get($this->orm)->getManager($this->db_connection);
        $this->resetResponse();
    }

    /**
     * @name            __destruct()
     *                  Destructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     */
    public function __destruct() {
        foreach ($this as $property => $value) {
            $this->$property = null;
        }
    }
    
    /**
     * @name            debug()
     * prints provided content
     *
     * @author          Said İmamoğlu
     *
     * @param mixed $var Content of variable
     * @param bool $exit true|false
     * @since           1.0.0
     * @version         1.1.9
     *
     */
    public function debug($var,$exit=true) {
        echo '<pre>';
        var_dump($var);
        if ($exit) {
            die;
        }
    }
    /**
     * @name            debugClass()
     * prints provided class methods
     *
     * @author          Said İmamoğlu
     *
     * @param mixed $class Class
     * @param bool $exit true|false
     * @since           1.2.1
     * @version         1.2.1
     *
     */
    public function debugClass($class, $exit = true) {
        if (is_object($class)) {
            $reflectionClass = new \ReflectionClass($class);
            $methods = $reflectionClass->getMethods();
            foreach ($methods as $method) {
                echo $method->class.'->'.$method->name.'()'.'<br>';
            }
        } else{
            echo $class .' is not a valid Class.';
        }
        if ($exit) {
            die;
        }
    }

    /**
     * @name            resetResponse()
     *                  Resets response.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          bool
     */
    protected function resetResponse() {
        /**
         * Reset response
         */
        $this->response = array(
            'result' => array(
                'set' => null,
                'total_rows' => 0,
                'last_insert_id' => null,
            ),
            'error' => true,
            'code' => 'err.response.reset',
            'rowCount' => 0
        );
        return true;
    }

    /**
     * @name            addLimit()
     *                  Adds limit instruction to query object.
     *
     * @author          Can Berkol
     *
     * @since           1.1.2
     * @version         1.1.2
     *
     * @param           object              $query                  Query object.
     * @param           array               $limit
     *
     * @return          object              $query
     */
    public function addLimit($query, $limit = null) {
        /**
         * Prepare LIMIT section of query
         */
        if ($limit != null) {
            if (isset($limit['start']) && isset($limit['count'])) {
                if (isset($limit['pagination'])) {
                    $this->response['rowCount'] = count($query->getResult());
                }
                /** If limit is set */
                $query->setFirstResult($limit['start']);
                $query->setMaxResults($limit['count']);
            } else {
                new CoreExceptions\InvalidLimitException($this->kernel, '');
            }
        }
        return $query;
    }

    /**
     * @name            createException()
     *                  Handles exception creation in a centralized manner. The function updates response code and
     *                  returns updated response object.
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @since           1.1.1
     * @version         1.2.3
     *
     * @param           string              $exception              Name of exception
     * @param           string              $msg                    Custom part of message
     * @param           string              $code                   error code
     * @param           bool                $isCore                 Defines if the exception belongs to Core Package.
     *
     * @return          array               $this->response
     */
    public function createException($exception, $msg, $code, $isCore = true) {
        $this->resetResponse();
        if ($isCore) {
            if (!strpos($exception,'Exception')){
                $exception = '\\BiberLtd\\Bundle\\CoreBundle\\Exceptions\\'.$exception.'Exception';
            }
            else{
                $exception = '\\BiberLtd\\Bundle\\CoreBundle\\Exceptions\\'.$exception;
            }
        }
        new $exception($this->kernel, $msg);
        $this->response['error'] = true;
        $this->response['code'] = $code;
        return $this->response;
    }

    /**
     * @name            getEntityDefinition()
     *                  Returns entity detail from entity property.
     *
     * @author          Can Berkol
     *
     * @since           1.1.7
     * @version         1.1.7
     *
     * @param           string              $entity
     * @param           string              $detail                 name, alias
     *
     * @return          mixed               string|false
     */
    public function getEntityDefinition($entity, $detail = 'name') {
        if (isset($this->entity[$entity][$detail])) {
            return $this->entity[$entity][$detail];
        }
        return false;
    }
    /**
     * @name            delete_entities()
     *                  Processes common delete functionalities and returns a sub response to instruct the calling
     *                  DELETE method what to do next.
     *
     * @author          Can Berkol
     *
     * @since           1.2.0
     * @version         1.2.0
     *
     * @param           array               $collection
     * @param           string              $entity                 Name of entity including namespace.
     *
     * @return          array               $sub_response           Sub response that contains instructions.
     *                                                              'process'       continue or stop
     *                                                              'item_count'    count of items to be deleted including entry values
     *                                                              'entries'       collection of entities
     *                                                                    'invalid' entities that have not been processed
     *                                                                    'valid'   entities that have been processed.
     */
    protected function deleteEntities($collection, $entity) {
        $sub_response = array('process' => 'stop');
        /** Loop through items and collect values. */
        $delete_count = 0;
        foreach ($collection as $item) {
            /** If the item is an entity we'll remove it from entity manager */
            if (!$item instanceof $entity) {
                new CoreExceptions\InvalidEntityException($this->kernel, $entity);
                $sub_response['process'] = 'continue';
                $sub_response['entries']['invalid'][] = $item;
            }
            /** Here we remove the entity from entity manager */
            $sub_response['entries']['valid'][] = $item;
            $this->em->remove($item);
            $delete_count++;
        }
        if ($delete_count > 0) {
            /** fluesh changes if there are items waiting to be deleted */
            $this->em->flush();
            $sub_response['item_count'] = $delete_count;
        } else {
            new CoreExceptions\InvalidParameterException($this->kernel, 'Array');
            $this->response['code'] = 'err.invalid.parameter.collection';
        }

        return $sub_response;
    }
    /**
     * @name            delete_entities()
     *                  Processes common delete functionalities and returns a sub response to instruct the calling
     *                  DELETE method what to do next.
     *
     * @author          Can Berkol
     *
     * @since           1.1.0
     * @version         1.2.0
     *
     * @use             $this->deleteEntities()
     * @deprecated      use deleteEntities() instead.
     *
     * @param           array               $collection
     * @param           string              $entity                 Name of entity including namespace.
     *
     * @return          array               $sub_response           Sub response that contains instructions.
     *                                                              'process'       continue or stop
     *                                                              'item_count'    count of items to be deleted including entry values
     *                                                              'entries'       collection of entities
     *                                                                    'invalid' entities that have not been processed
     *                                                                    'valid'   entities that have been processed.
     */
    protected function delete_entities($collection, $entity) {
        return $this->deleteEntities($collection, $entity);
    }
    /**
     * @name            insertEntities()
     *                  Processes common insert functionalities and returns a sub response to instruct the calling
     *                  INSERT method what to do next.
     *
     * @author          Can Berkol
     *
     * @since           1.1.0
     * @version         1.2.0
     *
     * @param           array               $collection
     * @param           string              $entity                 Name of entity including namespace.
     *
     * @return          array               $sub_response           Sub response that contains instructions.
     *                                                              'process'       continue or stop
     *                                                              'item_count'    count of items to be deleted including entry values
     *                                                              'entries'       collection of entities
     *                                                                    'invalid' entities that have not been processed
     *                                                                    'valid'   entities that have been processed.
     */
    protected function insertEntities($collection, $entity) {
        $sub_response = array('process' => 'stop');
        /** Loop through items and collect values. */
        $insert_count = 0;
        foreach ($collection as $item) {
            /** If the item is an entity we'll remove it from entity manager */
            if (!$item instanceof $entity) {
                new CoreExceptions\InvalidEntityException($this->kernel, $entity);
                $sub_response['process'] = 'continue';
                $sub_response['entries']['invalid'][] = $item;
            }
            /** Here we remove the entity from entity manager */
            $sub_response['entries']['valid'][] = $item;
            // $this->em->persist($item);
            $insert_count++;
        }

        if ($insert_count > 0) {
            /** flush changes if there are items waiting to be deleted */
            $this->em->flush();
            $sub_response['item_count'] = $insert_count;
        } else {
            new CoreExceptions\InvalidParameterException($this->kernel, 'Array');
            $this->response['code'] = 'err.invalid.parameter.collection';
        }
        return $sub_response;
    }
    /**
     * @name            insert_entities()
     *                  Processes common insert functionalities and returns a sub response to instruct the calling
     *                  INSERT method what to do next.
     *
     * @author          Can Berkol
     *
     * @since           1.1.0
     * @version         1.2.0
     *
     * @use             $this->insertEntities()
     * @deprecated
     *
     * @param           array               $collection
     * @param           string              $entity                 Name of entity including namespace.
     *
     * @return          array               $sub_response           Sub response that contains instructions.
     *                                                              'process'       continue or stop
     *                                                              'item_count'    count of items to be deleted including entry values
     *                                                              'entries'       collection of entities
     *                                                                    'invalid' entities that have not been processed
     *                                                                    'valid'   entities that have been processed.
     */
    protected function insert_entities($collection, $entity) {
        return $this->insertEntities($collection, $entity);
    }
    /**
     * @name            prepare_condition()
     *                  Prepares CONDITION value for WHERE clauses. This function prepares the right side of the equation.
     *                  id IN(3,4,5);
     *
     * @author          Can Berkol
     *
     * @since           1.1.0
     * @version         1.2.0
     *
     * @use             prepareCondition()
     *
     * @deprecated      Use $prepareCondition instead.
     *
     * @param           string              $key            starts, ends, contains, in, include, not_in, exclude
     * @param           mixed               $value          array, string or integer
     *
     * @return          string
     */
    protected function prepare_condition($key, $value) {
        return $this->prepareCondition($key, $value);
    }
    /**
     * @name            prepareCondition()
     *                  Prepares CONDITION value for WHERE clauses. This function prepares the right side of the equation.
     *                  id IN(3,4,5);
     *
     * @author          Can Berkol
     *
     * @since           1.2.0
     * @version         1.2.0
     *
     * @param           string              $key            starts, ends, contains, in, include, not_in, exclude
     * @param           mixed               $value          array, string or integer
     *
     * @return          string
     */
    protected function prepareCondition($key, $value) {
        if($value instanceof \DateTime){
            $value = $value->format('Y-m-d h:i:s');
        }
        $condition = '';
        switch ($key) {
            case 'starts':
                $condition .= ' LIKE \'' . $value[0] . '%\' ';
                break;
            case 'ends':
                $condition .= ' LIKE \'%' . $value[0] . '\' ';
                break;
            case 'contains':
                if (is_array($value)) {
                    $condition .= ' LIKE \'%' . $value[0] . '%\' ';
                } else{
                    $condition .= ' LIKE \'%' . $value . '%\' ';
                }
                break;
            case 'in':
            case 'include':
                $in = '';
                foreach ($value as $item) {
                    if (is_string($item)) {
                        $in .= '\'' . $item . '\',';
                    } else {
                        $in .= $item . ',';
                    }
                }
                $in = rtrim($in, ",");
                $condition .= ' IN(' . $in . ') ';
                break;
            case 'not_in':
            case 'exclude':
                $not_in = '';
                foreach ($value as $item) {
                    if (is_string($item)) {
                        $not_in .= '\'' . $item . '\',';
                    } else {
                        $not_in .= $item . ',';
                    }
                }
                $condition .= ' NOT IN(' . rtrim($not_in,','). ') ';
                break;
            case '=':
            case 'eq':
            case 'equal':
                switch ($value) {
                    case is_string($value):
                        $value = "'" . $value . "'";
                        break;
                    case is_numeric($value):
                        break;
                }
                $condition .= ' = ' . $value;
                break;
            case '!=':
            case 'not_eq':
            case 'not_equal':
                switch ($value) {
                    case is_string($value):
                        $value = "'" . $value . "'";
                        break;
                    case is_numeric($value):
                        break;
                }
                $condition .= ' != ' . $value;
                break;
            case 'null':
            case 'isnull':
            case 'is_null':
                $condition .= ' IS NULL ';
                break;
            case 'notnull':
            case 'isnotnull':
                $condition .= ' IS NOT NULL ';
                break;
            case '>':
            case 'gt':
            case 'greater_than':
                switch ($value) {
                    case is_string($value):
                        $value = "'" . $value . "'";
                        break;
                    case is_numeric($value):
                        break;
                }
                $condition .= ' > ' . $value;
                break;
            case '>=':
            case 'gte':
            case 'greater_than_equal':
                switch ($value) {
                    case is_string($value):
                        $value = "'" . $value . "'";
                        break;
                    case is_numeric($value):
                        break;
                }
                $condition .= ' >= ' . $value;
                break;
            case '<':
            case 'lt':
            case 'less_than':
                switch ($value) {
                    case is_string($value):
                        $value = "'" . $value . "'";
                        break;
                    case is_numeric($value):
                        break;
                }
                $condition .= ' < ' . $value;
                break;
            case '=<':
            case 'lte':
            case 'less_than_equal':
                switch ($value) {
                    case is_string($value):
                        $value = "'" . $value . "'";
                        break;
                    case is_numeric($value):
                        break;
                }
                $condition .= ' <= ' . $value;
                break;
            case 'between':
                if (is_array($value) && count($value)==2) {
                    $condition.= 'between '.$value[0].' and '. $value[1];
                }
                break;
        }
        return $condition;
    }

    /**
     * @name            prepare_delete()
     *                  Prepares DELETE query..
     *
     * @author          Can Berkol
     *
     * @since           1.1.0
     * @version         1.2.0
     *
     * @use             $this->prepareDelete()
     * @deprecated      Use prepareDelete() instead
     *
     * @param           string              $table          table name.
     * @param           string              $column         column name
     * @param           mixed               $values          array of values.
     *
     * @return          string
     */
    protected function prepare_delete($table, $column, $values) {
        return $this->prepareDelete($table, $column, $values);
    }
    /**
     * @name            prepareDelete()
     *                  Prepares DELETE query..
     *
     * @author          Can Berkol
     *
     * @since           1.2.0
     * @version         1.2.0
     *
     * @param           string              $table          table name.
     * @param           string              $column         column name
     * @param           mixed               $values          array of values.
     *
     * @return          string
     */
    protected function prepareDelete($table, $column, $values) {
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $column, 'comparison' => 'in', 'value' => $values),
                )
            )
        );
        $condition = $this->prepare_where($filter);
        $q_str = 'DELETE '
            . ' FROM ' . $table
            . ' WHERE ' . $condition;

        return $q_str;
    }
    /**
     * @name            prepare_where()
     *                  Prepares CONDITION statement.
     *
     * @author          Can Berkol
     *
     * @since           1.1.0
     * @version         1.2.0
     *
     * @use             $this->prepareWhere()
     * @deprecated      use prepareWhere() instead.
     *
     * @param           mixed               array           $filter             Multi-dimensional array
     *
     *                                  Example:
     *                                  $filter = array(
     *                                      'glue' => 'and',
     *                                      'condition' => array(
     *                                          0  => array(
     *                                                  'glue' => 'and'
     *                                                  'condition' => array(
     *                                                      0 => array('id', 'in', array(1,2,3,4))
     *                                                  ),
     *                                          ),
     *                                          1 => array(
     *                                              'glue' => 'or',
     *                                              'condition' => array(
     *                                                  0 => array(
     *                                                      'glue' => 'or',
     *                                                      'condition' => array(
     *                                                          0 => array('status', '=', 'a'),
     *                                                          1 => array('price', '>', 255)
     *                                                      ),
     *                                                  ),
     *                                                  1 => array(
     *                                                      'glue => 'and',
     *                                                      'condition => array(
     *                                                          0 => array('name', 'contains', 'oto')
     *                                                      )
     *                                                  )
     *                                              )
     *                                          ),
     *                                     )
     *                                  );
     *                                  Outputted WHERE is
     *
     *                                  ((p.id IN (1,2,3,4))) AND ((p.status = 'a' OR p.price > 255) OR (pl.name LIKE '%oto%'))
     *
     * @return          string
     */
    protected function prepare_where($filter) {
       return $this->prepareWhere($filter);
    }
    /**
     * @name            prepareWhere()
     *                  Prepares CONDITION statement.
     *
     * @author          Can Berkol
     *
     * @since           1.2.0
     * @version         1.2.0
     *
     * @param           mixed           $filter             Multi-dimensional array
     *
     *                                  Example:
     *                                  $filter = array(
     *                                      'glue' => 'and',
     *                                      'condition' => array(
     *                                          0  => array(
     *                                                  'glue' => 'and'
     *                                                  'condition' => array(
     *                                                      0 => array('id', 'in', array(1,2,3,4))
     *                                                  ),
     *                                          ),
     *                                          1 => array(
     *                                              'glue' => 'or',
     *                                              'condition' => array(
     *                                                  0 => array(
     *                                                      'glue' => 'or',
     *                                                      'condition' => array(
     *                                                          0 => array('status', '=', 'a'),
     *                                                          1 => array('price', '>', 255)
     *                                                      ),
     *                                                  ),
     *                                                  1 => array(
     *                                                      'glue => 'and',
     *                                                      'condition => array(
     *                                                          0 => array('name', 'contains', 'oto')
     *                                                      )
     *                                                  )
     *                                              )
     *                                          ),
     *                                     )
     *                                  );
     *                                  Outputted WHERE is
     *
     *                                  ((p.id IN (1,2,3,4))) AND ((p.status = 'a' OR p.price > 255) OR (pl.name LIKE '%oto%'))
     *
     * @return          string
     */
    protected function prepareWhere($filter) {
        $group_str = '';
        foreach ($filter as $group) {
            $group_str .= ' (';
            $cond_str = '';
            $glue = ' AND ';
            if ($group['glue'] == 'or') {
                $glue = ' OR ';
            }
            $condition = $group['condition'];
            if (!array_key_exists('column', $condition)) {
                $cond_str .= $this->prepareWhere($condition);
            } else {
                $prepared_str = $this->prepareCondition($condition['comparison'], $condition['value']);
                $cond_str .= ' ' . $condition['column'] . $prepared_str;
            }

            $group_str .= $cond_str . ')' . $glue;
        }
        $group_str = rtrim($group_str, $glue);

        return $group_str;
    }
    /**
     * @name            updateEntities()
     *                  Processes common update functionalities and returns a sub response to instruct the calling
     *                  UPDATE method what to do next.
     *
     * @author          Can Berkol
     *
     * @since           1.2.0
     * @version         1.2.0
     *
     * @param           array               $collection
     * @param           string              $entity                 Name of entity including namespace.
     *
     * @return          array               $sub_response           Sub response that contains instructions.
     *                                                              'process'       continue or stop
     *                                                              'item_count'    count of items to be deleted including entry values
     *                                                              'entries'       collection of entities
     *                                                                    'invalid' entities that have not been processed
     *                                                                    'valid'   entities that have been processed.
     */
    protected function updateEntities($collection, $entity) {
        $sub_response = array('process' => 'stop');
        /** Loop through items and collect values. */
        $update_count = 0;
        foreach ($collection as $item) {
            /** If the item is an entity we'll remove it from entity manager */
            if (!$item instanceof $entity || !$item->isModified()) {
                new CoreExceptions\InvalidEntityException($this->kernel, $entity);
                $sub_response['process'] = 'continue';
                $sub_response['entries']['invalid'][] = $item;
            }
            /** Here we remove the entity from entity manager */
            $sub_response['entries']['valid'][] = $item;
            $this->em->persist($item);
            $update_count++;
        }
        if ($update_count > 0) {
            /** fluesh changes if there are items waiting to be deleted */
            $this->em->flush();
            $sub_response['item_count'] = $update_count;
        } else {
            new CoreExceptions\InvalidParameterException($this->kernel, 'Array');
            $this->response['code'] = 'err.invalid.parameter.collection';
        }

        return $sub_response;
    }
    /**
     * @name            update_entities()
     *                  Processes common update functionalities and returns a sub response to instruct the calling
     *                  UPDATE method what to do next.
     *
     * @author          Can Berkol
     *
     * @since           1.1.0
     * @version         1.2.0
     *
     * @use             $this->updateEntities
     * @deprecated      use updateEntities instead.
     *
     * @param           array               $collection
     * @param           string              $entity                 Name of entity including namespace.
     *
     * @return          array               $sub_response           Sub response that contains instructions.
     *                                                              'process'       continue or stop
     *                                                              'item_count'    count of items to be deleted including entry values
     *                                                              'entries'       collection of entities
     *                                                                    'invalid' entities that have not been processed
     *                                                                    'valid'   entities that have been processed.
     */
    protected function update_entities($collection, $entity) {
        return $this->updateEntities($collection, $entity);
    }

    /**
     * @name            translateColumnName()
     *                  Change $column name from underscore to camelCase;
     *
     * @author          Said İmamoğlu
     *
     * @since           1.1.0
     * @version         1.1.0
     *
     * @param           array               $column
     *
     * @return          string              $result
     * 
     */
    protected function translateColumnName($column) {
        if (strpos($column, '_')) {
            $words = explode('_', $column);
            if (count($words) > 0) {
                $result = '';
                foreach ($words as $word) {
                    if ($word !== '' || empty($word)) {
                        $result .= ucfirst($word);
                    }
                }
                return $result;
            } else {
                return ucfirst($column);
            }
        } else {
            return ucfirst($column);
        }
    }

}

/**
 * Change Log
 * **************************************
 * v1.2.3                      Can Berkol
 * 17.06.2014
 * **************************************
 * U createException()
 *
 * **************************************
 * v1.2.2                   Said İmamoğlu
 * 02.04.2014
 * **************************************
 * U prepareCondition()
 *
 * **************************************
 * v1.2.1                   Said İmamoğlu
 * 05.03.2014
 * **************************************
 * A debugClass()
 *
 * **************************************
 * v1.2.0                      Can Berkol
 * 24.02.2014
 * **************************************
 * A deleteEntities()
 * A insertEntities()
 * A prepareCondition()
 * A prepareDelete()
 * A prepareWhere()
 * A updateEntities()
 * R delete_entities()
 * R insert_entities()
 * R prepare_delete()
 * R prepare_where()
 * R update_entities()
 * U createException()
 *
 * **************************************
 * v 1.1.9                  Said İmamoğlu
 * 19.02.2014
 * **************************************
 * U debug()
 *
 * **************************************
 * v1.1.8                      Can Berkol
 * 05.02.2014
 * **************************************
 * U prepare_conditions()
 *
 * **************************************
 * v1.1.7                      Can Berkol
 * 10.01.2014
 * **************************************
 * A getEntityDefinition()
 *
 * **************************************
 * v1.1.6                      Can Berkol
 * 10.01.2014
 * **************************************
 * B update_entities()
 *
 * **************************************
 * v1.1.5                      Can Berkol
 * 01.01.2014
 * **************************************
 * B prepare_condition()
 *
 * **************************************
 * v1.1.4                    Said İmamoğlu
 * 18.12.2013
 * **************************************
 * A translateColumnName()
 *
 * **************************************
 * v1.1.4                      Can Berkol
 * 25.11.2013
 * **************************************
 * U createException()  $isCore parameter added.
 *
 * **************************************
 * v1.1.3                      Can Berkol
 * 14.11.2013
 * **************************************
 * U prepare_condition()
 *
 * **************************************
 * v1.1.2                      Can Berkol
 * 13.11.2013
 * **************************************
 * U prepare_condition()
 *
 * **************************************
 * v1.1.1                   Said İmamoğlu
 * 07.11.2013
 * **************************************
 * A addLimit()
 * A createException()
 *
 * **************************************
 * v1.1.0                      Can Berkol
 * 14.10.2013
 * **************************************
 * A delete_entities()
 * A prepare_condition()
 * A prepare_delete()
 * A prepare_where()
 * A update_entities()
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 08.09.2013
 * **************************************
 * A __construct()
 * A __destruct()
 * A resetResponse()
 */