<?php
// -----
// Last updated: v2.0.1.
//
class zcObserverReferrers extends base
{
    public function __construct()
    {
        if (!defined('REFERRAL_REQUIRED') || !defined('DISPLAY_REFERRAL_OTHER')) {
            return;
        }

        $this->attach($this, [
            /* From includes/modules/create_account.php */
            'NOTIFY_CREATE_ACCOUNT_VALIDATION_CHECK',
            'NOTIFY_LOGIN_SUCCESS_VIA_CREATE_ACCOUNT',
        ]);

        global $current_page_base;
        if ($current_page_base === FILENAME_LOGIN || $current_page_base === FILENAME_CREATE_ACCOUNT) {
            $this->attach($this, ['NOTIFY_FOOTER_END']);
        }
    }

    // -----
    // From includes/modules/create_account.php. 
    //
    public function notify_create_account_validation_check(&$class, string $e, $unused, bool &$error, bool &$send_welcome_email): void
    {
        if (REFERRAL_REQUIRED !== 'true') {
            return;
        }

        global $messageStack;

        if (!ctype_digit($_POST['source'])) {
            $error = true;
            $messageStack->add('create_account', ENTRY_SOURCE_ERROR, 'error');
        }

        if (DISPLAY_REFERRAL_OTHER === 'true' && $_POST['source'] === '9999' && empty($_POST['source_other'])) {
            $error = true;
            $messageStack->add('create_account', ENTRY_SOURCE_OTHER_ERROR);
        }
    }
    public function notify_login_success_via_create_account(&$class, string $e): void
    {
        global $db;

        $db->Execute(
            "UPDATE " . TABLE_CUSTOMERS_INFO . "
                SET customers_info_source_id = " . (int)$_POST['source'] . "
              WHERE customers_info_id = " . (int)$_SESSION['customer_id'] . "
              LIMIT 1"
        );

        if ($_POST['source'] === '9999') {
            zen_db_perform(TABLE_SOURCES_OTHER, ['customers_id' => $_SESSION['customer_id'], 'sources_other_name' => $_POST['source_other']]);
        }
    }

    // -----
    // Issued at the end of a template's common/tpl_main_page.php. Load the jQuery module to insert
    // the Referral entry-fields at the end of other create-account fields. Note that this event is
    // observed **only** on 'appropriate' pages.
    //
    public function notify_footer_end(&$class, string $e): void
    {
        global $template, $current_page_base;

        ob_start();
        require $template->get_template_dir('tpl_modules_referrer_form_fields.php', DIR_WS_TEMPLATE, $current_page_base, 'templates') . '/tpl_modules_referrer_form_fields.php'; 
        $referrer_field_entry = ob_get_contents();
        ob_end_clean();
?>
<script>
    jQuery(function() {
<?php
        if (function_exists('zca_bootstrap_active') && zca_bootstrap_active() === true) {
?>
        jQuery('#loginDetails-card').after(<?= json_encode($referrer_field_entry) ?>);
<?php
        } else {
?>
        jQuery('#email-address').closest('fieldset').after(<?= json_encode($referrer_field_entry) ?>);
<?php
        }
?>
    });
</script>
<?php
    }
}
