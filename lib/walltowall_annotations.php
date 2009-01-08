<?php
	
	/**
	 * Count the number of annotations
	 * 
	 * @param $entity_id int/array
	 * @param $entity_type string
	 * @param $entity_subtype string
	 * @param $name string
	 * @param $owner_guid int/array
	 */
	function walltowall_count_annotations($entity_guid = 0, $entity_type = "", $entity_subtype = "", $name = "", $owner_guid = 0)
	{
		global $CONFIG;
		
		$sum = "count";

		if (is_array($entity_guid)) {
			if (sizeof($entity_guid) > 0) {
				foreach($entity_guid as $key => $val) {
					$entity_guid[$key] = (int) $val;			
				}
			} else {
				$entity_guid = 0;
			}
		} else {
			$entity_guid = (int)$entity_guid;
		}
		
		if (is_array($owner_guid)) {
			if (sizeof($owner_guid) > 0) {
				foreach($owner_guid as $key => $val) {
					$owner_guid[$key] = (int) $val;
				}
			} else {
				$owner_guid = 0;
			}
		} else {
			$owner_guid = (int)$owner_guid;
		}


		$entity_type = sanitise_string($entity_type);
		$entity_subtype = get_subtype_id($entity_type, $entity_subtype);
		$name = get_metastring_id($name);
		
		if (empty($name)) return 0;
		
		$where = array();
		
		if ($entity_guid) {
      if (is_array($entity_guid)) 
	   		$where[] = "a.entity_guid in (". implode(",",$entity_guid) . ")";
      else
			$where[] = "a.entity_guid=$entity_guid";    
    }

		if ($entity_type!="")
			$where[] = "e.type='$entity_type'";
		if ($entity_subtype)
			$where[] = "e.subtype=$entity_subtype";
			
		if ($owner_guid != 0 && !is_array($owner_guid)) {
			$where[] = "a.owner_guid=$owner_guid";
		} else {
			if (is_array($owner_guid))
				$where[] = "a.owner_guid in (" . implode(",",$owner_guid) . ")";
		}
    			
		if ($name!="")
			$where[] = "a.name_id='$name'";
			
		if ($sum != "count")
			$where[] = "a.value_type='integer'"; // Limit on integer types
		
		$query = "SELECT $sum(ms.string) as sum from {$CONFIG->dbprefix}annotations a JOIN {$CONFIG->dbprefix}entities e on a.entity_guid = e.guid JOIN {$CONFIG->dbprefix}metastrings ms on a.value_id=ms.id WHERE ";
		foreach ($where as $w)
			$query .= " $w and ";
		$query .= get_access_sql_suffix("a"); // now add access
		
		$row = get_data_row($query);
		if ($row)
			return $row->sum;
			
		return false;
	}
	
?>
