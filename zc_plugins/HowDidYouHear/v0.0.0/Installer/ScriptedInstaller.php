<?php
// -----
// Admin-level installation script for the "encapsulated" How Did You Hear About Us plugin for Zen Cart, by lat9.
// Copyright (C) 2025, Vinos de Frutas Tropicales.
//
// Last updated: v2.0.2
//
use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{
    protected function executeInstall()
    {
        if (!$this->purgeOldFiles()) {
            return false;
        }

        global $sniffer;
        zen_define_default('TABLE_SOURCES', DB_PREFIX . 'sources');
        zen_define_default('TABLE_SOURCES_OTHER', DB_PREFIX . 'sources_other');
        if (!$sniffer->table_exists(TABLE_SOURCES)) {
            $this->executeInstallerSql(
                "CREATE TABLE " . TABLE_SOURCES . " (
                    sources_id int NOT NULL auto_increment,
                    sources_name varchar(64) NOT NULL,
                    PRIMARY KEY (sources_id),
                    KEY IDX_SOURCES_NAME (sources_name)
                )"
            );
            $this->executeInstallerSql(
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
            $this->executeInstallerSql(
                "CREATE TABLE " . TABLE_SOURCES_OTHER . " (
                    customers_id int NOT NULL default 0,
                    sources_other_name varchar(64) NOT NULL,
                    PRIMARY KEY (customers_id)
                )"
            );
        }

        if (!$sniffer->field_exists(TABLE_CUSTOMERS_INFO, 'customers_info_source_id')) {
            $this->executeInstallerSql(
                "ALTER TABLE " . TABLE_CUSTOMERS_INFO . "
                   ADD customers_info_source_id int NOT NULL AFTER customers_info_date_account_last_modified"
            );
        }

        $this->executeInstallerSql(
            "INSERT IGNORE INTO " . TABLE_CONFIGURATION . "
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

        return true;
    }

    // -----
    // Note: This (https://github.com/zencart/zencart/pull/6498) Zen Cart PR must
    // be present in the base code or a PHP Fatal error is generated due to the
    // function signature difference.
    //
    protected function executeUpgrade($oldVersion)
    {
    }

    /**
     * @return bool
     */
    protected function executeUninstall()
    {
        zen_deregister_admin_pages(['referrals', 'stats_referral_sources']);

        $this->executeInstallerSql(
            "DELETE FROM " . TABLE_CONFIGURATION . "
              WHERE configuration_key IN ('DISPLAY_REFERRAL_OTHER', 'REFERRAL_REQUIRED')"
        );

        return true;
    }

    protected function purgeOldFiles(): bool
    {
        $filesToDelete = [
            DIR_FS_ADMIN . 'referrals.php',
            DIR_FS_ADMIN . 'stats_referral_sources.php',
            DIR_FS_ADMIN . 'includes/extra_datafiles/referral_database_tables.php',
            DIR_FS_ADMIN . 'includes/extra_datafiles/referrals_filenames.php',
            DIR_FS_ADMIN . 'includes/functions/extra_functions/referral_functions.php',
            DIR_FS_ADMIN . 'includes/functions/extra_functions/reg_referrals_mod.php',
            DIR_FS_ADMIN . 'includes/languages/english/referrals.php',
            DIR_FS_ADMIN . 'includes/languages/english/stats_referral_sources.php',
            DIR_FS_ADMIN . 'includes/languages/english/extra_definitions/referral_defines.php',

            DIR_FS_CATALOG . 'includes/extra_datafiles/referral_database_tables.php',
            DIR_FS_CATALOG . 'includes/extra_datafiles/referrals_filenames.php',
            DIR_FS_CATALOG . 'includes/functions/extra_functions/referral_functions.php',
            DIR_FS_CATALOG . 'includes/languages/english/extra_definitions/referral_defines.php',
        ];

        $errorOccurred = false;
        foreach ($filesToDelete as $key => $nextFile) {
            if (file_exists($nextFile)) {
                $result = unlink($nextFile);
                if (!$result && file_exists($nextFile)) {
                    $errorOccurred = true;
                    $this->errorContainer->addError(
                        0,
                        sprintf(ERROR_UNABLE_TO_DELETE_FILE, $nextFile),
                        false,
                        // this str_replace has to do DIR_FS_ADMIN before CATALOG because catalog is contained within admin, so results are wrong.
                        // also, '[admin_directory]' is used to obfuscate the admin dir name, in case the user copy/pastes output to a public forum for help.
                        sprintf(ERROR_UNABLE_TO_DELETE_FILE, str_replace([DIR_FS_ADMIN, DIR_FS_CATALOG], ['[admin_directory]/', ''], $nextFile))
                    );
                }
            }
        }
        return !$errorOccurred;
    }
}
