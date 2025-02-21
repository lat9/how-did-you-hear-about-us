<?php
/*
  $Id: stats_referral_sources.php,v 1.0 2004/06/07 22:50:52 rmh Exp $

  adapted from osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  portions Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
require 'includes/application_top.php';
?>
<!doctype html>
<html <?= HTML_PARAMS ?>>
  <head>
      <?php require DIR_WS_INCLUDES . 'admin_html_head.php'; ?>
  </head>
  <body>
    <!-- header //-->
    <?php require DIR_WS_INCLUDES . 'header.php'; ?>
    <!-- header_eof //-->
    <div class="container-fluid">
      <h1 class="pageHeading"><?= HEADING_TITLE ?></h1>
      <!-- body //-->
      <table class="table table-hover">
        <thead>
          <tr class="dataTableHeadingRow">
            <th class="dataTableHeadingContent"><?= TABLE_HEADING_REFERRALS ?></th>
            <th class="dataTableHeadingContent"><?= TABLE_HEADING_VIEWED ?></th>
          </tr>
        </thead>
        <tbody>
<?php
$_GET['page'] = (int)($_GET['page'] ?? '1');
$action = $_GET['action'] ?? '';
if ($action === 'display_other') {
    $referrals_query_raw =
        "SELECT COUNT(ci.customers_info_source_id) AS no_referrals, so.sources_other_name AS sources_name
           FROM " . TABLE_CUSTOMERS_INFO . " ci, " . TABLE_SOURCES_OTHER . " so
          WHERE ci.customers_info_source_id = 9999
            AND so.customers_id = ci.customers_info_id
          GROUP BY so.sources_other_name
          ORDER BY so.sources_other_name DESC";
} else {
    $referrals_query_raw =
        "SELECT COUNT(ci.customers_info_source_id) AS no_referrals, s.sources_name, s.sources_id
           FROM " . TABLE_CUSTOMERS_INFO . " ci
                LEFT JOIN " . TABLE_SOURCES . " s
                    ON s.sources_id = ci.customers_info_source_id
          GROUP BY s.sources_id
          ORDER BY ci.customers_info_source_id DESC";
}
$referrers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_REPORTS, $referrals_query_raw, $referrers_query_numrows);
$referrers = $db->Execute($referrals_query_raw);
foreach ($referrers as $referrer) {
?>
          <tr class="dataTableRow">
            <td class="dataTableContent"><?= zen_output_string_protected(empty($referrer['sources_name']) ? TEXT_OTHER : $referrer['sources_name']) ?></td>
            <td class="dataTableContent"><?= $referrer['no_referrals'] ?></td>
          </tr>
<?php
}
?>
          <tr class="dataTableRow">
            <td class="dataTableContent text-right" colspan="2">
              <a href="<?= zen_href_link(FILENAME_STATS_REFERRAL_SOURCES, ($action === 'display_other') ? '' : 'action=display_other') ?>" role="button" class="btn btn-primary">
                <?= ($action === 'display_other') ? BUTTON_VIEW_DEFAULT : BUTTON_VIEW_OTHER ?>
              </a>
            </td>
        </tbody>
      </table>
      <table class="table">
        <tr>
          <td><?= $referrers_split->display_count($referrers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_REPORTS, (int)$_GET['page'], TEXT_DISPLAY_NUMBER_OF_REFERRALS) ?></td>
          <td class="text-right"><?= $referrers_split->display_links($referrers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_REPORTS, MAX_DISPLAY_PAGE_LINKS, (int)$_GET['page']) ?></td>
        </tr>
      </table>
      <!-- body_text_eof //-->
    </div>
    <!-- body_eof //-->

    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
