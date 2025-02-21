<?php
//rmh referral begin
$define = [
    'ENTRY_SOURCE' => 'How did you hear about us:',
    'ENTRY_SOURCE_ERROR' => 'Please select how you first heard about us.',
    'ENTRY_SOURCE_OTHER' => '(if &quot;Other&quot; please specify):',
    'ENTRY_SOURCE_OTHER_ERROR' => 'Please enter how you first heard about us.',
    'PULL_DOWN_SOURCES' => 'Please select a source',
    'PULL_DOWN_OTHER' => 'Other - (please specify)',
    'ENTRY_SOURCE_TEXT' => '',
    'ENTRY_SOURCE_OTHER_TEXT'  => '',
];
if (defined('REFERRAL_REQUIRED') && REFERRAL_REQUIRED === 'true') {
    $define['ENTRY_SOURCE_TEXT'] = '*';
    $define['ENTRY_SOURCE_OTHER_TEXT'] = '*';
}
return $define;
//rmh referral end
