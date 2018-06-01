<?php

/**
 * Class ilReportingModel: Each report must implement its own model which extends this class.
 *
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 * @version $Id:
 *
 */
abstract class ilReportingModel {

	/** @var  ilDB */
	protected $db;
	/** @var \ilReportingPlugin */
	protected $pl;
	/** @var  ilObjUser */
	protected $user;
	/** @var  ilAccess */
	protected $access;


	public function __construct() {
		global $DIC;
		$this->db = $DIC->database();
		$this->pl = ilReportingPlugin::getInstance();
		$this->user = $DIC->user();
		$this->access = $DIC->access();
	}


	abstract public function getSearchData(array $filters);


	abstract public function getReportData(array $ids, array $filters);


	/**
	 * Return all the ref-ids (of Categories) where the current user can administrate users
	 *
	 * @return array
	 */
	protected function getRefIdsWhereUserCanAdministrateUsers() {
		$sql = 'SELECT DISTINCT time_limit_owner FROM usr_data';
		$set = $this->db->query($sql);
		$refIds = array();
		while ($rec = $this->db->fetchAssoc($set)) {
			$refIds[] = $rec['time_limit_owner'];
		}
		foreach ($refIds as $k => $refId) {
			if (!$this->access->checkAccess('read_users', '', $refId)) {
				unset($refIds[$k]);
			}
		}

		return $refIds;
	}


	/**
	 * Build records from SQL Query string
	 *
	 * @param $sql SQL String
	 *
	 * @return array
	 */
	protected function buildRecords($sql) {
		$sql = preg_replace('/[ ]{2,}|[\t]|[\n]/', ' ', trim($sql));
		$result = $this->db->query($sql);
		$return = array();
		while ($rec = $this->db->fetchAssoc($result)) {
			$return[] = $rec;
		}

		return $return;
	}


	/**
	 * @param int $test_id
	 *
	 * @return ilTestEvaluationData
	 */
	public function getTestEvaluation($test_id) {
		$test = new ilObjTest($test_id);

		$evaluation = new ilTestEvaluationData($test);

		return $evaluation;
	}
}
