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
namespace BiberLtd\Bundle\CoreBundle\Responses;
use BiberLtd\Bundle\CoreBundle\Core as Core;

class ModelResponse extends Core{
	public $result;
	public $error;
	public $stats;
	public $process;

	/**
	 * ModelResponse constructor.
	 *
	 * @param null      $resultSet
	 * @param int       $setCount
	 * @param int       $totalCount
	 * @param null      $lastInsertId
	 * @param bool|true $errorExist
	 * @param string    $errorCode
	 * @param string    $errorMessage
	 * @param int       $executionStart
	 * @param int       $executionEnd
	 */
	public function __construct($resultSet = null, $setCount = 0, $totalCount = 0, $lastInsertId = null, $errorExist = true, $errorCode = 'E:X:001', $errorMessage = 'Unknown error.', $executionStart = 0, $executionEnd = 0){
		$this->result = new \stdClass();
		$this->error = new \stdClass();
		$this->stats = new \stdClass();
		$this->process = new \stdClass();

		$this->result->set = $resultSet;
		$this->result->count = new \stdClass();
		$this->result->count->set = $setCount;
		$this->result->count->total = $totalCount;
		$this->result->lastInsertId = $lastInsertId;

		$this->error->exist = $errorExist;
		$this->error->code = $errorCode;
		$this->error->message = $errorMessage;

		$this->stats->execution = new \stdClass();
		$this->stats->execution->start = $executionStart == 0 ? time() : $executionStart;
		$this->stats->execution->end = $executionEnd == 0 ? time() : $executionEnd;

		$this->process->continue = false;
		$this->process->collection = new \stdClass();
		$this->process->collection->invalid = array();
		$this->process->collection->valid = array();
	}
}
