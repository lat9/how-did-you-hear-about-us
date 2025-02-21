<?php
class zcObserverReferrersAdmin extends base
{
    public function __construct()
    {
        if (!isset($_SESSION['admin_id'])) {
            return;
        }

        $this->install();

        $this->attach($this, [
            /* From admin/customers.php */
            'NOTIFY_ADMIN_CUSTOMERS_LISTING_HEADER',
            'NOTIFY_ADMIN_CUSTOMERS_LISTING_NEW_FIELDS',
            'NOTIFY_ADMIN_CUSTOMERS_LISTING_ELEMENT',

            /* From admin/orders.php */
            'NOTIFY_ADMIN_ORDERS_ADDRESS_FOOTERS',
        ]);
    }

    // -----
    // From admin/customers.php.  Adding a column containing the customer's referrer, a.k.a.
    // How Did You Hear About Us.
    //
    public function notify_admin_customers_listing_header(&$class, string $e, $unused, array &$extra_headings): void
    {
        $extra_headings[] = [
            'content' => TABLE_HEADING_REFERRED_BY,
        ];
    }
    public function notify_admin_customers_listing_new_fields(&$class, string $e, $unused, string &$new_fields, string &$display_order): void
    {
        $new_fields .= ', ci.customers_info_source_id';
    }
    public function notify_admin_customers_listing_element(&$class, string $e, array $result_customer, array &$additional_columns, array &$customer): void
    {
        $additional_columns[] = [
            'content' => zen_output_string_protected(zen_get_sources_name($result_customer['customers_info_source_id'], $customer['customers_id'])),
        ];
    }

    // -----
    // From admin/orders.php, adding the customer's referral to the "Customers" address section.
    //
    public function notify_admin_orders_address_footers(&$class, string $e, string $which, string|array &$footer_suffix, array|bool &$customer): void
    {
        if ($which !== 'customer') {
            return;
        }

        global $db;
        if (!is_array($footer_suffix)) {
            $footer_suffix = [];
        }

        $customers_id = (int)$customer['id'];
        $referred_by = $db->Execute(
            "SELECT customers_info_source_id
               FROM " . TABLE_CUSTOMERS_INFO . "
              WHERE customers_info_id = $customers_id
              LIMIT 1"
        );
        $customers_info_source_id = $referred_by->EOF ? '9999' : $referred_by->fields['customers_info_source_id'];

        $footer_suffix[TABLE_HEADING_REFERRED_BY] = zen_output_string_protected(zen_get_sources_name($customers_info_source_id, $customers_id));
    }

    protected function install(): void
    {
        global $sniffer, $db;

        if (isset($_SESSION['referrers_installed'])) {
            return;
        }

        if (!$sniffer->table_exists(TABLE_SOURCES)) {
            $db->Execute(
                "CREATE TABLE " . TABLE_SOURCES . " (
                    sources_id int NOT NULL auto_increment,
                    sources_name varchar(64) NOT NULL,
                    PRIMARY KEY (sources_id),
                    KEY IDX_SOURCES_NAME (sources_name)
                )"
            );
            $db->Execute(
                "INSERT INTO " . TABLE_SOURCES . "
                    (sources_name)
                 VALUES
                    ('Google'),
                    ('Yahoo!'),
                    ('Flyer'),
                    ('Bing'),
                    ('Friend')"
            );
        }

        if (!$sniffer->table_exists(TABLE_SOURCES_OTHER)) {
            $db->Execute(
                "CREATE TABLE " . TABLE_SOURCES_OTHER . " (
                    customers_id int NOT NULL default 0,
                    sources_other_name varchar(64) NOT NULL,
                    PRIMARY KEY (customers_id)
                )"
            );
        }

        if (!$sniffer->field_exists(TABLE_CUSTOMERS_INFO, 'customers_info_source_id')) {
            $db->Execute(
                "ALTER TABLE " . TABLE_CUSTOMERS_INFO . "
                   ADD customers_info_source_id int NOT NULL AFTER customers_info_date_account_last_modified"
            );
        }

        $db->Execute(
            "INSERT IGNORE INTO configuration
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added)
             VALUES
                ('Display \"Other\" Referral option?', 'DISPLAY_REFERRAL_OTHER', 'true', 'Display \"Other - please specify\" with text box in referral source in account creation', 5, 10, 'zen_cfg_select_option([\'true\', \'false\'], ', now()),

                ('Require Referral Source?', 'REFERRAL_REQUIRED', 'true', 'Require the Referral Source in account creation', 5, 11, 'zen_cfg_select_option([\'true\', \'false\'], ', now())"
        );

        if (!zen_page_key_exists('referrals')) {
            zen_register_admin_page('referrals', 'BOX_CUSTOMERS_REFERRALS', 'FILENAME_REFERRALS', '', 'localization', 'Y');
        }
        if (!zen_page_key_exists('stats_referral_sources')) {
            zen_register_admin_page('stats_referral_sources', 'BOX_REPORTS_REFERRAL_SOURCES', 'FILENAME_STATS_REFERRAL_SOURCES', '', 'reports', 'Y');
        }

        $_SESSION['referrers_installed'] = true;
    }
}
