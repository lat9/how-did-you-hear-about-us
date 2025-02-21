Instructions for installing How Did You Hear About Us for Zen Cart 

================= IMPORTANT =====================
Please BACKUP your store and database before attempting any of these changes.
Use this software at your own risk.
This contribution is licensed under the terms of the GPL; see included license.txt.

Core files that are modified are:
/admin/customers.php
/admin/orders.php
/includes/modules/YOUR_TEMPLATE_NAME/create_account.php
/includes/templates/YOUR_TEMPLATE_NAME/templates/tpl_modules_create_account.php

=================================================

1. Please use the correct folders for your version of Zen Cart.
   For support please see the zen-cart forum http://www.zen-cart.com/showthread.php?35623

2. Unpack the zip file to a temporary directory on your hard drive. 
   The files should unzip in a directory structure similar to the structure of 
   your Zen Cart store's files.
   
3. Copy the contents of the unzipped directory to the corresponding locations in your
   Zen Cart installation except for the `install_referrals.sql` file and this 
   installation file. If you are using any overrides in Zen Cart that include the
   files in this plugin, use an application such as Beyond Compare or WinMerge
   to compare your files and resolve any differences.
   There is one file:
   (\includes\templates\YOUR_TEMPLATE_NAME\templates\tpl_modules_create_account.php)
   which should be put in your template override folder i.e.
   (\includes\templates\NAME_OF_YOUR_TEMPLATE_HERE\templates\tpl_modules_create_account.php)
   If you don't apply the changes from this template file, then the pulldown won't show to the customers.

   Similarly the /includes/modules/YOUR_TEMPLATE_NAME/create_account.php file should be put into 
   an override folder, ie: /includes/modules/NAME_OF_YOUR_TEMPLATE_HERE/create_account.php
   If you don't apply the changes for this module file, then the customer's selections won't be stored, and your reports will be empty.

4. Run the SQL file install_referrals.sql on your database. You can use the 
   Install SQL Patches in the Zen Cart Admin Panel under Tools -> Install SQL
   Patches or you can use phpmyadmin for this purpose.

5. Enjoy your newly installed contribution!


How to Use How Did You Hear About Us 
====================================

This contrib creates a drop-down box that prompts the customer where they 
found out about the site. Admin can make this required or not 
(Admin->Configuration->Customer Details). Also includes ability to control 
whether or not an "Other" appears in the dropdown and if so, displays an 
additional text input box (Admin->Configuration->Customer Details).

The Admin can predefine the list of choices for the pulldown, via the Admin->Localization->Referral Source Choices menu.

Information is then stored in customers_info table for reports and data mining.
Reports are displayed in Admin->Reports->Referral Sources. If you double click on `Other` a list of
all the customer-entered "Other" sources will appear with the dates that they were entered.


Removing this plugin
--------------------
1. Edit the /admin-foldername-here/includes/functions/extra_functions/reg_referrals.php file
   In this file, uncomment the LAST EIGHT LINES of the file, and save the file. 
   Then login to your Admin. You should see that the menu options have disappeared from the menu (Check the Reports menu, for example).
   THIS WILL ALSO MEAN THAT ALL THE DATA THIS MOD HAD COLLECTED HAS ALSO BEEN REMOVED PERMANENTLY.

2. Now delete all the files that you installed as part of this plugin.
   
   BE AWARE THAT any file that you had replaced/overwritten will need to have their originals restored from a backup of the original Zen Cart versions of those files.
   This includes at least the following files:
   /admin/customers.php
   /admin/orders.php
   /includes/modules/YOUR_TEMPLATE_NAME/create_account.php
  /includes/templates/YOUR_TEMPLATE_NAME/templates/tpl_modules_create_account.php



Authors
-------
The update to v1.5.5 was done by Judy Gunderson (stellarweb) www.zencart-ecommerce-website-design.com
Please use the Zen Cart support forum for any assistance you may require.

The update to v1.5.3/v1.5.4 was done by Judy Gunderson (stellarweb) www.zencart-ecommerce-website-design.com
Please use the Zen Cart support forum for any assistance you may require.

The update to v1.5.0/v1.5.1 was done by DrByte www.zen-cart.com
Please use the Zen Cart support forum for any assistance you may require.

The update to 139h was done by That Software Guy
http://www.thatsoftwareguy.com

The update to 139a was done by JT Website Design.
http://www.jtwebsitedesign.com/

The update from 1.3.7/8 was done by Judy Gunderson at 
http://zencart-ecommerce-website-design.com

The update from 1.3.1 to 1.3.6_beta was done by CES. 
This contribution was converted to ZenCart from the original osCommerce contribution.

Thanks to Ryan Hobbs for the original osCommerce contribution and Keith W (homewetbar) who added functionality to the osCommerce 1.5 version of this contribution.
