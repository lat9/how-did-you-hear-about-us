<?php
/*
  $Id: referrals.php,v 1.00 2004/06/07 22:50:52 rmh Exp $

  adapted from osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  portions Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
require 'includes/application_top.php';

$action = $_GET['action'] ?? '';
$page_param = (isset($_GET['page'])) ? ((int)$_GET['page'] . '&') : '';
$_GET['page'] = (int)($_GET['page'] ?? '1');

if ($action !== '') {
    switch ($action) {
        case 'insert':
        case 'save':
            $sources_id = (int)($_GET['sID'] ?? 0);
            $sources_name = zen_db_prepare_input($_POST['sources_name']);

            $sql_data_array = ['sources_name' => $sources_name];

            if ($action === 'insert') {
                zen_db_perform(TABLE_SOURCES, $sql_data_array);
                $sources_id = zen_db_insert_id();
            } else {
                zen_db_perform(TABLE_SOURCES, $sql_data_array, 'update', 'sources_id = ' . (int)$sources_id . ' LIMIT 1');
            }

            zen_redirect(zen_href_link(FILENAME_REFERRALS, $page_param . 'sID=' . $sources_id));
            break;
        case 'deleteconfirm':
            $sources_id = (int)($_POST['sID'] ?? 0);

            $source_query = "DELETE FROM " . TABLE_SOURCES . " WHERE sources_id = $sources_id LIMIT 1";
            $sources = $db->Execute ($source_query);
            zen_redirect(zen_href_link(FILENAME_REFERRALS, rtrim('&', $page_param)));
            break;
    }
}
?>
<!doctype html>
<html <?= HTML_PARAMS ?>>
  <head>
    <?php require DIR_WS_INCLUDES . 'admin_html_head.php'; ?>
  </head>
  <body>
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->

    <!-- body //-->
    <div class="container-fluid">
      <h1><?= HEADING_TITLE ?></h1>
      <div class="row">
        <!-- body_text //-->
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 configurationColumnLeft">
          <table class="table table-hover" role="listbox">
            <thead>
              <tr class="dataTableHeadingRow">
                <th class="dataTableHeadingContent"><?= TABLE_HEADING_REFERRALS ?></th>
                <th class="dataTableHeadingContent text-right"><?= TABLE_HEADING_ACTION ?></th>
              </tr>
            </thead>
            <tbody>
<?php
$sources_query_raw = "SELECT sources_id, sources_name FROM " . TABLE_SOURCES . " ORDER BY sources_name";
$sources_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $sources_query_raw, $sources_query_numrows);
$sources = $db->Execute($sources_query_raw);

foreach ($sources as $source) {
    if ((!isset($_GET['sID']) || $_GET['sID'] == $source['sources_id']) && !isset($cInfo) && strpos($action, 'new') !== 0) {
        $cInfo = new objectInfo($source);
    }
    if (isset($cInfo) && is_object($cInfo) && $source['sources_id'] == $cInfo->sources_id) {
        $row_params = 'id="defaultSelected" class="dataTableRowSelected"';
        $link_param = '&action=edit';
        $row_icon = zen_icon('caret-right', '', '2x', true);
    } else {
        $row_params = 'class="dataTableRow"';
        $link_param = '';
        $row_icon =
            '<a href="' . zen_href_link(FILENAME_REFERRALS, $page_param . 'sID=' . $source['sources_id']) . '" data-toggle="tooltip" title="' . IMAGE_ICON_INFO . '" role="button">' .
                zen_icon('circle-info', '', '2x', true, false) .
            '</a>';
    }
?>
                <tr <?= $row_params ?> onclick="document.location.href='<?= zen_href_link(FILENAME_REFERRALS, $page_param . 'sID=' . $source['sources_id'] . $link_param) ?>'">
                    <td class="dataTableContent"><?= $sources->fields['sources_name']; ?></td>
                    <td class="dataTableContent text-right"><?= $row_icon ?></td>
                </tr>
<?php
}

?>
            </tbody>
          </table>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 configurationColumnRight">
<?php
$heading = [];
$contents = [];
switch ($action) {
    case 'new':
        $heading[] = ['text' => '<b>' . TEXT_HEADING_NEW_REFERRAL . '</b>'];

        $contents = ['form' => zen_draw_form('sources', FILENAME_REFERRALS, 'action=insert', 'post',  'class="form-horizontal"')];
        $contents[] = ['text' => TEXT_NEW_INTRO];
        $contents[] = ['text' => '<br>' . TEXT_REFERRALS_NAME . '<br>' . zen_draw_input_field('sources_name', '', 'class="form-control"')];

        $contents[] = ['align' => 'center', 'text' => '<br><button type="submit" class="btn btn-primary">' . IMAGE_INSERT . '</button> <a href="' . zen_href_link(FILENAME_REFERRALS, $page_param) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>'];
        break;
    case 'edit':
        $heading[] = ['text' => '<b>' . TEXT_HEADING_EDIT_REFERRAL . '</b>'];

        $contents = ['form' => zen_draw_form('sources', FILENAME_REFERRALS, $page_param . 'sID=' . $cInfo->sources_id . '&action=save', 'post', 'class="form-horizontal"')];
        $contents[] = ['text' => TEXT_EDIT_INTRO];
        $contents[] = ['text' => '<br>' . TEXT_REFERRALS_NAME . '<br>' . zen_draw_input_field('sources_name', $cInfo->sources_name, 'class="form-control"')];

        $contents[] = ['align' => 'center', 'text' => '<br><button type="submit" class="btn btn-primary">' . IMAGE_UPDATE . '</button> <a href="' . zen_href_link(FILENAME_REFERRALS, $page_param . 'sID=' . $cInfo->sources_id) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>'];
        break;
    case 'delete':
        $heading[] = ['text' => '<b>' . TEXT_HEADING_DELETE_REFERRAL . '</b>'];

        $contents = ['form' => zen_draw_form('sources', FILENAME_REFERRALS, $page_param . '&action=deleteconfirm', 'post')];
        $contents[] = ['text' => TEXT_DELETE_INTRO . zen_draw_hidden_field('sID', $cInfo->sources_id)];
        $contents[] = ['text' => '<br><b>' . $cInfo->sources_name . '</b>'];

        $contents[] = ['align' => 'center', 'text' => '<br><button type="submit" class="btn btn-danger">' . IMAGE_DELETE . '</button> <a href="' . zen_href_link(FILENAME_REFERRALS, $page_param . 'sID=' . $cInfo->sources_id) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>'];
        break;
    default:
        if (isset($cInfo) && is_object($cInfo)) {
            $heading[] = ['text' => '<b>' . $cInfo->sources_name . '</b>'];

            $contents[] = ['align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_REFERRALS, $page_param . 'sID=' . $cInfo->sources_id . '&action=edit') .'" class="btn btn-primary" role="button">' . IMAGE_EDIT . '</a> <a href="' . zen_href_link(FILENAME_REFERRALS, $page_param . 'sID=' . $cInfo->sources_id . '&action=delete') . '" class="btn btn-warning" role="button">' . IMAGE_DELETE . '</a>'];
        }
        break;
}

if (!empty($heading) && !empty($contents)) {
    $box = new box();
    echo $box->infoBox($heading, $contents);
}
?>
        </div>
      </div>
      <div class="row">
        <table class="table">
          <tr>
            <td><?= $sources_split->display_count($sources_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REFERRALS) ?></td>
            <td class="text-right"><?= $sources_split->display_links($sources_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']) ?></td>
          </tr>
<?php
if ($action === '') {
?>
            <tr>
              <td colspan="2" class="text-right"><a href="<?= zen_href_link(FILENAME_REFERRALS, $page_param . 'action=new') ?>" class="btn btn-primary" role="button"><?= IMAGE_INSERT ?></a></td>
            </tr>
<?php
}
?>
        </table>
      </div>
      <!-- body_text_eof //-->
    </div>
    <!-- body_eof //-->

    <!-- footer //-->
<?php require DIR_WS_INCLUDES . 'footer.php'; ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require DIR_WS_INCLUDES . 'application_bottom.php'; ?>
