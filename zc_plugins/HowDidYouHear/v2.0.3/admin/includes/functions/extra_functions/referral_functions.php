<?php
// referrals mod

// rmh referral
function zen_get_sources_name($source_id, $customers_id): string
{
    global $db;

    if ($source_id === '9999') {
        $sources_query = "SELECT sources_other_name AS sources_name FROM " . TABLE_SOURCES_OTHER . " WHERE customers_id = " . (int)$customers_id;
    } else {
        $sources_query = "SELECT sources_name FROM " . TABLE_SOURCES . " WHERE sources_id = " . (int)$source_id;
    }

    $sources = $db->Execute($sources_query . ' LIMIT 1');
    if ($sources->EOF) {
        if ($source_id === '9999') {
            return TEXT_OTHER;
        } else {
            return TEXT_NONE;
        }
    } else {
        return $sources->fields['sources_name'];
    }
}
