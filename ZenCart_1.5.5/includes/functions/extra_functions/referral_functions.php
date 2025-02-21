<?php
// Referrals mod

function zen_get_sources($sources_id = '') {
	global $db;
	$sources_array = array();
	if (zen_not_null($sources_id)) {
		$sources = "select sources_name
                    from " . TABLE_SOURCES . "
                    where sources_id = '" . (int)$sources_id . "'";
		// $sources_values = $db->Execute($sources);
		// $sources_array = array('sources_name' => $sources_values->fields['sources_name']);
	} else {
		$sources = "select sources_id, sources_name
                  from " . TABLE_SOURCES . "
                  order by sources_name";
		//$sources_array = array('sources_name' => $sources_values->fields['sources_name']);
	}
	$sources_values = $db->Execute($sources);
	while (!$sources_values->EOF) {
    $sources_array[] = array('sources_id' => $sources_values->fields['sources_id'],
                             'sources_name' => $sources_values->fields['sources_name']);
		$sources_values->MoveNext();
	}

	return $sources_array;
}

////rmh referral
// Creates a pull-down list of sources
function zen_get_source_list($name, $show_other = false, $selected = 'PULL_DOWN_SOURCES', $parameters = '') {
	$sources_array = array(array('id' => '', 'text' => PULL_DOWN_SOURCES));
	$sources = zen_get_sources();

	foreach ($sources as $source) {
		$sources_array[] = array('id' => $source['sources_id'], 'text' => $source['sources_name']);
	}

	if ($show_other == 'true') {
		$sources_array[] = array('id' => '9999', 'text' => PULL_DOWN_OTHER);
	}

	return zen_draw_pull_down_menu($name, $sources_array, $selected, $parameters);
}
