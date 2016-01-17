<?php
/**
 * @author		Can Berkol
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        10.12.2015
 */
namespace BiberLtd\Bundle\CoreBundle;

/** Required for better & instant error handling for the support team */
use \BiberLtd\Bundle\CoreBundle\Exceptions as CoreExceptions;
use \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse;

class CoreModel extends Core{
    /**
     * @var string
     */
    protected $dbConnection = 'default';
    /**
     * @var string
     */
    protected $orm = 'doctrine';
    /**
     * @var \object
     */
    protected $em;
    /**
     * @var \object
     */
    protected $entity;
    /**
     * @var \object
     */
    protected $kernel;
    /**
     * @var \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    protected $response;
    /**
     * @var array
     */
    protected $languages = [];

    /**
     * CoreModel constructor.
     *
     * @param             $kernel
     * @param string|null $dbConnection
     * @param string|null $orm
     */
    public function __construct($kernel, string $dbConnection = null, string $orm = null){
        $this->dbConnection = $dbConnection ?? 'default';
        $this->orm =  $orm ?? 'doctrine';
        $this->kernel = $kernel;
        unset($kernel);
        /**
         * Set the connection with the required database.
         */
        $this->em = $this->kernel->getContainer()->get($this->orm)->getManager($this->dbConnection);
    }

    /**
     * Destructor.
     */
    public function __destruct() {
        foreach ($this as $property => $value) {
            $this->$property = null;
        }
    }

    /**
     * @param \Doctrine\ORM\Query $query
     * @param array|null          $limit
     *
     * @return \Doctrine\ORM\Query
     */
    public function addLimit(\Doctrine\ORM\Query $query, array $limit = null) {
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
     * @param string    $exception
     * @param string    $msg
     * @param string    $code
     * @param bool|null $isCore
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function createException(string $exception, string $msg, string $code, bool $isCore = null){
        $isCore = $isCore ?? true;
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
     * @param string      $entity
     * @param string|null $detail
     *
     * @return bool
     */
    public function getEntityDefinition(string $entity, string $detail = null){
        $detail = $detail ?? 'name';
        if (isset($this->entity[$entity][$detail])) {
            return $this->entity[$entity][$detail];
        }
        return false;
    }

    /**
     * @param array $collection
     * @param       $entity
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    protected function deleteEntities(array $collection, $entity) {
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
     * @param array $collection
     * @param       $entity
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    protected function insertEntities(array $collection, $entity) {
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
	 * @param string $key
	 * @param mixed $value
	 * @param string $method
	 *
	 * @return string
	 *
	 * Prepares CONDITION value for WHERE clauses. This function prepares the right side of the equation.
	 */
    protected function prepareCondition(string $key, $value, string $method = ''){
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
                    $condition .= 'between ' . $value[0]->format('Y-m-d H:i:s') . ' and ' . $value[1]->format('Y-m-d H:i:s');
                }
                break;
        }
        return $condition;
    }

	/**
	 * @param string $table
	 * @param string $column
	 * @param array  $values
	 *
	 * @return string
	 */
	protected function prepareDelete(string $table, string $column, array $values){
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
	 * @param array $filter
	 *
	 * @return string
	 */
    protected function prepareWhere(array $filter){
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
	 * @param array  $collection
	 * @param string $entity
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
    protected function updateEntities(array $collection, string $entity)    {
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
	 * @param string $column
	 *
	 * @return string
	 */
    protected function translateColumnName(string $column){
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