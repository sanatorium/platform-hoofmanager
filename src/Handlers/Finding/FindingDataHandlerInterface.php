<?php namespace Sanatorium\Hoofmanager\Handlers\Finding;

interface FindingDataHandlerInterface {

	/**
	 * Prepares the given data for being stored.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	public function prepare(array $data);

}
