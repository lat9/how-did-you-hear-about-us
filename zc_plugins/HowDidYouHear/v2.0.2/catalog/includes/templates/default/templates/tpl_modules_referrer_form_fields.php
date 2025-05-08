<?php
//rmh referral start
?>
<fieldset id="referral-wrapper">
    <legend><?= TABLE_HEADING_REFERRAL_DETAILS ?></legend>

    <label class="inputLabel" for="referral-source"><?= ENTRY_SOURCE ?></label>
    <?= zen_get_source_list('source', (DISPLAY_REFERRAL_OTHER === 'true'), '', 'id="referral-source" ' . (!empty(ENTRY_SOURCE_TEXT) ? 'required' : '')) ?>
    <div class="clearBoth p-2"></div>
<?php
if (DISPLAY_REFERRAL_OTHER === 'true') {
?>
    <label class="inputLabel" for="referral-other"><?php echo ENTRY_SOURCE_OTHER; ?></label>
    <?= zen_draw_input_field('source_other', '', 'id="referral-other"') ?>
    <div class="clearBoth p-2"></div>
<?php
}
?>
</fieldset>
