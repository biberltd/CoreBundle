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

class ModelResponse{
	public $result;
	public $error;
	public $stats;
	public $process;

	/**
	 * ModelResponse constructor.
	 *
	 * @param null        $resultSet
	 * @param int|null    $setCount
	 * @param int|null    $totalCount
	 * @param int|null    $lastInsertId
	 * @param bool|null   $errorExist
	 * @param string|null $errorCode
	 * @param string|null $errorMessage
	 * @param float|null  $executionStart
	 * @param float|null  $executionEnd
	 */
	public function __construct($resultSet = null, int $setCount = null, int $totalCount = null, int $lastInsertId = null, bool $errorExist = null, string $errorCode = null, string $errorMessage = null, float $executionStart = null, float $executionEnd = null){
		$executionStart = $executionStart ?? 0.00;
		$executionEnd = $executionEnd ?? 0.00;
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
		$this->stats->execution->start = $executionStart == 0 ? microtime(true) : $executionStart;
		$this->stats->execution->end = $executionEnd == 0 ? microtime(true) : $executionEnd;

		$this->process->continue = false;
		$this->process->collection = new \stdClass();
		$this->process->collection->invalid = [];
		$this->process->collection->valid = [];
	}
}
