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
	 * @param null        $resultSet
	 * @param int|null    $setCount
	 * @param int         $totalCount
	 * @param int|null    $lastInsertId
	 * @param bool|null   $errorExist
	 * @param string|null $errorCode
	 * @param string|null $errorMessage
	 * @param int|null    $executionStart
	 * @param int|null    $executionEnd
	 */
	public function __construct($resultSet = null, int $setCount = null, int $totalCount = 0, int $lastInsertId = null, bool $errorExist = null, string $errorCode = null, string $errorMessage = null, int $executionStart = null, int $executionEnd = null){
		$executionStart = $executionStart ?? 0;
		$executionEnd = $executionEnd ?? 0;

		$this->result = new \stdClass();
		$this->error = new \stdClass();
		$this->stats = new \stdClass();
		$this->process = new \stdClass();

		$this->result->set = $resultSet;
		$this->result->count = new \stdClass();
		$this->result->count->set = $setCount ?? 0;
		$this->result->count->total = $totalCount ?? 0;
		$this->result->lastInsertId = $lastInsertId;

		$this->error->exist = $errorExist ?? true;
		$this->error->code = $errorCode ?? 'E:X:001';
		$this->error->message = $errorMessage ?? 'Unknown error.';

		$this->stats->execution = new \stdClass();
		$this->stats->execution->start = $executionStart == 0 ? microtime() : $executionStart;
		$this->stats->execution->end = $executionEnd == 0 ? microtime() : $executionEnd;

		$this->process->continue = false;
		$this->process->collection = new \stdClass();
		$this->process->collection->invalid = [];
		$this->process->collection->valid = [];
	}
}
