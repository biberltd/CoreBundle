<?php
/**
 * @vendor      BiberLtd
 * @package		Core
 * @subpackage	Responses
 * @name	    ModelResponse
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.0
 * @date        30.04.2015
 */

namespace BiberLtd\Bundle\CoreBundle\Responses;
use BiberLtd\Bundle\CoreBundle\Core as Core;

class ModelResponse extends Core{
	public $result;
	public $error;
	public $stats;
	public $process;

	/**
	 * @name            __construct()
	 *                  Constructor.
	 *
	 * @author          Can Berkol
	 *
	 * @since           1.0.0
	 * @version         1.0.0
	 *
	 * @param           mixed		$resultSet
	 * @param			integer		$setCount
	 * @param			integer		$totalCount
	 * @param			integer		$totalCount
	 * @param			mixed		$lastInsertId
	 * @param			bool		$errorExist
	 * @param			string		$errorCode
	 * @param			string		$errorMessage
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
		$this->stats->execution->end = $executionEnd == 0 ? time : $executionEnd;

		$this->process->continue = false;
		$this->process->collection = new \stdClass();
		$this->process->collection->invalid = array();
		$this->process->collection->valid = array();
	}
}
/**
 * Change Log
 * **************************************
 * v1.0.0					   30.04.2015
 * Can Berkol
 * **************************************
 * - Main release
 *
 */