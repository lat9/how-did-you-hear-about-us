<?php
//define('CATEGORY_SOURCE', 'Referral Source'); //rmh referral

//rmh referral begin
define('ENTRY_SOURCE', 'How did you hear about us:');
define('ENTRY_SOURCE_ERROR', 'Please select how you first heard about us.');
define('ENTRY_SOURCE_OTHER', '(if "Other" please specify):');
define('ENTRY_SOURCE_OTHER_ERROR', 'Please enter how you first heard about us.');
if (REFERRAL_REQUIRED == 'true') {
  define('ENTRY_SOURCE_TEXT', '*');
  define('ENTRY_SOURCE_OTHER_TEXT', '*');
} else {
  define('ENTRY_SOURCE_TEXT', '');
  define('ENTRY_SOURCE_OTHER_TEXT', '');
}
//rmh referral end
define('PULL_DOWN_SOURCES', 'Please select a source');
define('PULL_DOWN_OTHER', 'Other - (please specify)'); //rmh referral
