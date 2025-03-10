<?php
class zcObserverReferrersAdmin extends base
{
    public function __construct()
    {
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
}
