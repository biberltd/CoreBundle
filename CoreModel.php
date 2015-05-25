<?php
/**
 * @vendor      BiberLtd
 * @package     BiberLtd\Core
 * @name        CoreModel
 *
 * @author      Can Berkol
 *              Said İmamoğlu
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.2.7
 * @date        25.05.2015
 *
 */

namespace BiberLtd\Bundle\CoreBundle;

/** Required for better & instant error handling for the support team */
use \BiberLtd\Bundle\CoreBundle\Exceptions as CoreExceptions;
use BiberLtd\Bundle\CoreBundle\Responses\ModelResponse;

class CoreModel extends Core{
    protected $dbConnection = 'default';
    protected $orm = 'doctrine';
    protected $em;
    protected $entity;
    protected $kernel;
    protected $response;
    protected $languages = array();

    /**
     * @name            __construct ()
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.2.7
     *
     * @param           object 		$kernel
     * @param           string 		$dbConnection
     * @param           string 		$orm
     */
    public function __construct($kernel, $dbConnection = 'default', $orm = 'doctrine'){
        $this->dbConnection = $dbConnection;
        $this->orm = $orm;
        $this->kernel = $kernel;
		$this->response = new ModelResponse();
        /**
         * Set the connection with the required database.
         */
        $this->em = $this->kernel->getContainer()->get($this->orm)->getManager($this->dbConnection);
        $this->resetResponse();
    }

    /**
     * @name            __destruct ()
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
     * @name            addLimit ()
     *
     * @author          Can Berkol
     *
     * @since           1.1.2
     * @version         1.2.6
     *
     * @param           object 		$query
     * @param           array 		$limit
     *
     * @return          object              $query
     */
    public function addLimit($query, $limit = null) {
        if ($limit != null) {
            if (isset($limit['start']) && isset($limit['count'])) {
                if (isset($limit['pagination'])) {
                    $this->response->result->count->set = count($query->getResult());
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
     * @name            createException ()
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @since           1.1.1
     * @version         1.2.6
     *
     * @param           string 		$exception
     * @param           string 		$msg
     * @param           string 		$code
     * @param           bool 		$isCore 				Defines if the exception belongs to Core Package.
     *
     * @return          array       $this->response
     */
    public function createException($exception, $msg, $code, $isCore = true){
        if ($isCore) {
            if (!strpos($exception, 'Exception')) {
                $exception = '\\BiberLtd\\Bundle\\CoreBundle\\Exceptions\\' . $exception . 'Exception';
            } else {
                $exception = '\\BiberLtd\\Bundle\\CoreBundle\\Exceptions\\' . $exception;
            }
        }
        new $exception($this->kernel, $msg);
        $this->response->error->exist = true;
        $this->response->error->code = $code;
        $this->response->error->message = $msg;

        return $this->response;
    }

    /**
     * @name            getEntityDefinition ()
     *
     * @author          Can Berkol
     *
     * @since           1.1.7
     * @version         1.1.7
     *
     * @param           string 		$entity
     * @param           string 		$detail name, alias
     *
     * @return          mixed       string|false
     */
    public function getEntityDefinition($entity, $detail = 'name'){
        if (isset($this->entity[$entity][$detail])) {
            return $this->entity[$entity][$detail];
        }
        return false;
    }

    /**
     * @name            deleteEntities()
     *
     * @author          Can Berkol
     *
     * @since           1.2.0
     * @version         1.2.6
     *
     * @param           array 		$collection
     * @param           string 		$entity
     *
	 * @return          BiberLtd\Bundle\Core\Responses\ModelResponse            $subResponse
	 *
	 */
    protected function deleteEntities($collection, $entity) {
        $subResponse = new ModelResponse();
		$subResponse->process->continue = false;
        /** Loop through items and collect values. */
        $deleteCount = 0;
        foreach ($collection as $item) {
            if (!$item instanceof $entity) {
                new CoreExceptions\InvalidEntityException($this->kernel, $entity);
				$subResponse->process->continue = true;
				$subResponse->process->collection->invalid[] = $item;
            }
            /** Here we remove the entity from entity manager */
			$subResponse->process->collection->valid[] = $item;
            $this->em->remove($item);
            $deleteCount++;
        }
        if ($deleteCount > 0) {
            /** fluesh changes if there are items waiting to be deleted */
            $this->em->flush();
            $subResponse->result->count->set = $deleteCount;
        }
		else {
            new CoreExceptions\InvalidParameterException($this->kernel, 'Array');
            $this->response->error->code = 'E:X:002';
            $this->response->error->message = 'A parameter is missing or it has wrong value.';
        	return $this->response;
		}
        return $subResponse;
    }

    /**
     * @name            insertEntities()
     *
     * @author          Can Berkol
     *
     * @since           1.1.0
     * @version         1.2.6
     *
     * @param           array 			$collection
     * @param           string 			$entity
     *
	 * @return          BiberLtd\Bundle\Core\Responses\ModelResponse            $subResponse
	 *
	 */
    protected function insertEntities($collection, $entity) {
		$subResponse = new ModelResponse();
		$subResponse->process->continue = false;
        $insertCount = 0;
        foreach ($collection as $item) {
            if (!$item instanceof $entity) {
                new CoreExceptions\InvalidEntityException($this->kernel, $entity);
				$subResponse->process->continue = true;
				$subResponse->process->collection->invalid[] = $item;
            }
			$subResponse->process->collection->valid[] = $item;
            $this->em->persist($item);
			$insertCount++;
        }

        if ($insertCount > 0) {
            /** flush changes if there are items waiting to be deleted */
            $this->em->flush();
			$subResponse->result->count->set = $insertCount;
        }
		else {
			new CoreExceptions\InvalidParameterException($this->kernel, 'Array');
			$this->response->error->code = 'E:X:002';
			$this->response->error->message = 'A parameter is missing or it has wrong value.';
			return $this->response;
        }
        return $subResponse;
    }
    /**
     * @name            prepareCondition ()
     *                  Prepares CONDITION value for WHERE clauses. This function prepares the right side of the equation.
     *                  id IN(3,4,5);
     *
     * @author          Can Berkol
     *
     * @since           1.2.0
     * @version         1.2.4
     *
     * @param           string 		$key 			starts, ends, contains, in, include, not_in, exclude
     * @param           mixed 		$value 			array, string or integer
     * @param           string 		$method 		Simple, one parameter methods only.
     *
     * @return          string
     */
    protected function prepareCondition($key, $value, $method = ''){
        if ($value instanceof \DateTime) {
            $value = $value->format('Y-m-d h:i:s');
        }
        $condition = '';
        switch ($key) {
            case 'starts':
                if (empty($method)) {
                    $condition .= ' LIKE \'' . $value[0] . '%\' ';
                }
				else {
                    $condition .= ' LIKE ' . $method . '(\'' . $value[0] . '%\') ';
                }
                break;
            case 'ends':
                if (empty($method)) {
                    $condition .= ' LIKE \'%' . $value[0] . '\' ';
                }
				else {
                    $condition .= ' LIKE ' . $method . '(\'%' . $value[0] . '\') ';
                }
                break;
            case 'contains':
                if (is_array($value)) {
                    if (empty($method)) {
                        $condition .= ' LIKE \'%' . $value[0] . '%\' ';
                    }
					else {
                        $condition .= ' LIKE ' . $method . '(\'%' . $value[0] . '%\') ';
                    }
                }
				else {
                    if (empty($method)) {
                        $condition .= ' LIKE \'%' . $value . '%\' ';
                    }
					else {
                        $condition .= ' LIKE ' . $method . '(\'%' . $value . '%\') ';
                    }
                }
                break;
            case 'in':
            case 'include':
                $in = '';
                foreach ($value as $item) {
                    if (is_string($item)) {
                        if (empty($method)) {
                            $in .= '\'' . $item . '\',';
                        }
						else {
                            $in .= $method . '(\'' . $item . '\'),';
                        }
                    }
					else {
                        if (empty($method)) {
                            $in .= $item . ',';
                        } else {
                            $in .= $method . '(' . $item . '),';
                        }
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
                        if (empty($method)) {
                            $not_in .= '\'' . $item . '\',';
                        }
						else {
                            $not_in .= $method . '(\'' . $item . '\'),';
                        }
                    }
					else {
                        if (empty($method)) {
                            $not_in .= $item . ',';
                        } else {
                            $not_in .= $method . '(' . $item . '),';
                        }
                    }
                }
                $condition .= ' NOT IN(' . rtrim($not_in, ',') . ') ';
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
                        if (empty($method)) {
                            $value = "'" . $value . "'";
                        }
						else {
                            $value = $method . "('" . $value . "')";
                        }
                        break;
                    case is_numeric($value):
                        if (!empty($method)) {
                            $value = $method . '(' . $value . ')';
                        }
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
                        if (empty($method)) {
                            $value = "'" . $value . "'";
                        }
						else {
                            $value = $method . "('" . $value . "')";
                        }
                        break;
                    case is_numeric($value):
                        if (!empty($method)) {
                            $value = $method . '(' . $value . ')';
                        }
                        break;
                }
                $condition .= ' > ' . $value;
                break;
            case '>=':
            case 'gte':
            case 'greater_than_equal':
                switch ($value) {
                    case is_string($value):
                        if (empty($method)) {
                            $value = "'" . $value . "'";
                        }
						else {
                            $value = $method . "('" . $value . "')";
                        }
                        break;
                    case is_numeric($value):
                        if (!empty($method)) {
                            $value = $method . '(' . $value . ')';
                        }
                        break;
                }
                $condition .= ' >= ' . $value;
                break;
            case '<':
            case 'lt':
            case 'less_than':
                switch ($value) {
                    case is_string($value):
                        if (empty($method)) {
                            $value = "'" . $value . "'";
                        }
						else {
                            $value = $method . "('" . $value . "')";
                        }
                        break;
                    case is_numeric($value):
                        if (!empty($method)) {
                            $value = $method . '(' . $value . ')';
                        }
                        break;
                }
                $condition .= ' < ' . $value;
                break;
            case '=<':
            case '<=':
            case 'lte':
            case 'less_than_equal':
                switch ($value) {
                    case is_string($value):
                        if (empty($method)) {
                            $value = "'" . $value . "'";
                        }
						else {
                            $value = $method . "('" . $value . "')";
                        }
                        break;
                    case is_numeric($value):
                        if (!empty($method)) {
                            $value = $method . '(' . $value . ')';
                        }
                        break;
                }
                $condition .= ' <= ' . $value;
                break;
            case 'between':
                if (is_array($value) && count($value) == 2) {
                    $condition .= 'between ' . $value[0] . ' and ' . $value[1];
                }
                break;
        }
        return $condition;
    }
    /**
     * @name            prepareDelete ()
     *
     * @author          Can Berkol
     *
     * @since           1.2.0
     * @version         1.2.6
     *
     * @param           string $table table name.
     * @param           string $column column name
     * @param           mixed $values array of values.
     *
     * @return          string
     */
    protected function prepareDelete($table, $column, $values){
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $column, 'comparison' => 'in', 'value' => $values),
                )
            )
        );
        $condition = $this->prepareWhere($filter);
        $qStr = 'DELETE '
            		.' FROM '.$table
            		.' WHERE '.$condition;

        return $qStr;
    }

    /**
     * @name            prepareWhere ()
     *                  Prepares CONDITION statement.
     *
     * @author          Can Berkol
     *
     * @since           1.2.0
     * @version         1.2.6
     *
     * @param           mixed 		$filter

     * @return          string
     */
    protected function prepareWhere($filter){
        $groupStr = '';
        foreach ($filter as $group) {
			$groupStr .= ' (';
            $condStr = '';
            $glue = ' AND ';
            if ($group['glue'] == 'or') {
                $glue = ' OR ';
            }
            $condition = $group['condition'];
            if (!array_key_exists('column', $condition)) {
				$condStr .= $this->prepareWhere($condition);
            } else {
                if (!isset($condition['method'])) {
                    $condition['method'] = '';
                }
                $prepared_str = $this->prepareCondition($condition['comparison'], $condition['value'], $condition['method']);
				$condStr .= ' ' . $condition['column'] . $prepared_str;
            }

			$groupStr .= $condStr . ')' . $glue;
        }
		$groupStr = rtrim($groupStr, $glue);

        return $groupStr;
    }

    /**
     * @name            updateEntities ()
     *
     * @author          Can Berkol
     *
     * @since           1.2.0
     * @version         1.2.6
     *
     * @param           array 			$collection
     * @param           string 			$entity
     *
	 * @return          array           $sub_response
	 *
	 */
    protected function updateEntities($collection, $entity)    {
		$subResponse = new ModelResponse();
		$subResponse->process->continue = false;
		$updateCount = 0;
        foreach ($collection as $item) {
            /** If the item is an entity we'll remove it from entity manager */
            if (!$item instanceof $entity || !$item->isModified()) {
                new CoreExceptions\InvalidEntityException($this->kernel, $entity);
				$subResponse->process->continue = true;
				$subResponse->process->collection->invalid[] = $item;
            }
            /** Here we remove the entity from entity manager */
			$subResponse->process->collection->valid[] = $item;
            $this->em->persist($item);
			$updateCount++;
        }
        if ($updateCount > 0) {
            /** fluesh changes if there are items waiting to be deleted */
            $this->em->flush();
			$subResponse->result->count->set = $updateCount;
        } else {
			new CoreExceptions\InvalidParameterException($this->kernel, 'Array');
			$this->response->error->code = 'E:X:002';
			$this->response->error->message = 'A parameter is missing or it has wrong value.';
			return $this->response;
        }

        return $subResponse;
    }

    /**
     * @name            translateColumnName ()
     *
     * @author          Said İmamoğlu
     *
     * @since           1.1.0
     * @version         1.1.0
     *
     * @param           array 		$column
     *
     * @return          string      $result
     *
     */
    protected function translateColumnName($column){
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
 * v1.2.7                      25.05.2015
 * Can Berkol
 * **************************************
 * BF :: $this->$dbConnection is fixed to $this->dbConnection
 *
 * **************************************
 * v1.2.6                      01.05.2015
 * Can Berkol
 * **************************************
 * CR :: New $response object does not require $this->resetResponse(). Deleted.
 * CR :: __construct() content is converted to camelCase.
 * CR :: Deprecated methods removed.
 * CR :: Responses & SubResponses are converted to BiberLtd\Core\Responses\ModelResponse
 *
 * **************************************
 * v1.2.5                      Can Berkol
 * 17.02.2014
 * **************************************
 * U prepareCondition()
 * U prepareWhere()
 *
 * **************************************
 * v1.2.4                      Can Berkol
 * 16.02.2014
 * **************************************
 * U prepareCondition()
 *
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