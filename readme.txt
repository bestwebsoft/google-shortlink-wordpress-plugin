=== Google Shortlink by BestWebSoft ===
Contributors: bestwebsoft
Donate link: https://bestwebsoft.com/donate/
Tags: add link shortener, firebase plugin, firebase dynamic plugin, google shortlink, firebase links, firebase plugin, google shortlink plugin, get short links, link statistics, links, google, goo.gl, shorturl
Requires at least: 4.0
Tested up to: 5.3
Stable tag: 1.5.7
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Replace external WordPress website links with Google shortlinks and track click stats.

== Description ==

Google Shortlink plugin is a useful tool to get short links from Google URL Shortener service without leaving your WordPress website. Generate short links by direct input and/or automatically. Replace all external links on your website with short links, restore or delete them from database, and manage statistic.

Install, activate, and save your time!

https://www.youtube.com/watch?v=SZIWLm8mmdU

= Features =

* Firebase Dynamic Links API
* Automatically generate short links
* Generate short links by direct output
* Add unlimited number of fields for direct links input
* View list of links with additional info:
	* Page URL
	* Short link
	* Number of total clicks
	* Articles that contain link
* Manage your links manually with the following options:
	* Replace
	* Restore
	* Delete
* Manage all external links automatically:
	* Scan website for new links
	* Replace
	* Restore
	* Restore all links and clear database
* Compatible with latest WordPress version
* Incredibly simple settings for fast setup without modifying code
* Detailed step-by-step documentation and videos

If you have a feature suggestion or idea you'd like to see in the plugin, we'd love to hear about it! [Suggest a Feature](https://support.bestwebsoft.com/hc/en-us/requests/new)

= Documentation & Videos =

* [[Doc] Installation](https://docs.google.com/document/d/1-hvn6WRvWnOqj5v5pLUk7Awyu87lq5B_dO-Tv-MC9JQ/)
* [[Doc] How to Use](https://docs.google.com/document/d/13V7769ghm0d5KjzAIZIytnkLLc9yfWWe59jr81oQXEo/)

= Help & Support =

Visit our Help Center if you have any questions, our friendly Support Team is happy to help — <https://support.bestwebsoft.com/>

= Translation =

* Russian (ru_RU)
* Ukrainian (uk)

Some of these translations are not complete. We are constantly adding new features which should be translated. If you would like to create your own language pack or update the existing one, you can send [the text of PO and MO files](https://codex.wordpress.org/Translating_WordPress) to [BestWebSoft](https://support.bestwebsoft.com/hc/en-us/requests/new) and we'll add it to the plugin. You can download the latest version of the program for work with PO and MO [files Poedit](https://www.poedit.net/download.php).

= Recommended Plugins =

* [Updater](https://bestwebsoft.com/products/wordpress/plugins/updater/?k=ed72e881dcfb65a3487b083775c694c1) - Automatically check and update WordPress website core with all installed plugins and themes to the latest versions.

== Installation ==

1. Upload the `google-shortlink` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin via the 'Plugins' menu in WordPress.
3. Plugin settings are located in "BWS Panel" > "Google Shortlink".
4. Plugin page is located in main menu.
4. Enter your API key for goo.gl account on plugin options page.

[View a Step-by-step Instruction on Google Shortlink Installation](https://docs.google.com/document/d/1-hvn6WRvWnOqj5v5pLUk7Awyu87lq5B_dO-Tv-MC9JQ/edit)

== Frequently Asked Questions ==

= Where can I get API key for this plugin? =

Please complete the following:

1. You must go to [Google developers console](https://cloud.google.com/console/project) and create a new project (set fields "Project name" and "Project ID" as you wish).
2. After creating the project go to the “APIs & auth” tab.
3. Find in the list of available APIs “URL Shortener API” and set it ON.
4. After that go to “Credentials” tab and click “CREATE NEW KEY” button in “Public API access” area.
5. Choose “browser key” in pop-up window. The generated key will appear on the page, and you can regenerate it anytime.

= Can I get a shortlink for any link I need? =

Yes, just paste it to one of the fields below “Type long links here:” title on "Direct input" tab at plugin’s main page and press a button “Get short links”. A short link will appear beside and also be saved in the database.

= How to replace all external links on my website with shortlinks. =

Please complete the following:

1. Go to the plugin main page “Google Shortlink by BestWebSoft” in the main menu (Dashboard).
2. Go to Additional options tab.
3. Set "Scan web-site for new external links" option on and click the button “Apply” below. Scanning your website may take some time, don’t be afraid, it’s normal.
4. Now you can manage those links as your wish with options on the main page. In a particular case, to replace all external links to short links set “Replace automatically all external links” option on and press “Apply button”.

= I have some problems with the plugin's work. What Information should I provide to receive proper support? =

Please make sure that the problem hasn't been discussed yet on our forum (https://support.bestwebsoft.com). If no, please provide the following data along with your problem's description:
1. The link to the page where the problem occurs
2. The name of the plugin and its version. If you are using a pro version - your order number.
3. The version of your WordPress installation
4. Copy and paste into the message your system status report. Please read more here: [Instruction on System Status](https://docs.google.com/document/d/1Wi2X8RdRGXk9kMszQy1xItJrpN0ncXgioH935MaBKtc/)

== Screenshots ==

1. 'Table of links' tab on plugin's main page.
2. 'Direct input' tab on plugin's main page.
3. 'Additional options' tab on plugin's main page.
4. Plugin settings page in WordPress admin panel.
5. 'FAQ' tab on plugin's main page

== Changelog ==

= V1.5.7 - 14.11.2019 =
* Bugfix : Minor bugs were fixed.

= V1.5.6 - 04.09.2019 =
* Update : The deactivation feedback has been changed. Misleading buttons have been removed.

= V1.5.5 - 15.01.2019 =
* NEW : Ability to switch to Firebase Dynamic Links API has been added.
* Bugfix : The bug with the links search has been fixed.
* Bugfix : The bug with the incorrect error displaying during website scanning has been fixed.

= V1.5.4 - 05.05.2017 =
* Bugfix : The bug with incomplete short links replacement was fixed.
* Bugfix : The bug with empty post title displaying was fixed.

= V1.5.3 - 23.03.2017 =
* Bugfix : We fixed bug with short links.

= V1.5.2 - 12.10.2016 =
* Update : BWS plugins section is updated.

= V1.5.1 - 12.07.2016 =
* Update : Instruction on How to get API key has been updated.

= V1.5.0 - 25.04.2016 =
* Update : We updated all functionality for wordpress 4.5.

= V1.4.9 - 09.12.2015 =
* Bugfix : The bug with plugin menu duplicating was fixed.

= V1.4.8 - 02.11.2015 =
* Update : BWS plugins section is updated.
* Update : We updated all functionality for wordpress 4.3.1.

= V1.4.7 - 27.07.2015 =
* Update : We updated all functionality for wordpress 4.2.3.

= V1.4.6 - 20.05.2015 =
* Bugfix : We fixed bug with displaying the list of links.
* Update : We updated all functionality for wordpress 4.2.2.

= V1.4.5 - 01.04.2015 =
* Bugfix : Plugin optimization is done.
* Update : We updated all functionality for wordpress 4.1.1.

= V1.4.4 - 30.12.2014 =
* Update : BWS plugins section is updated.
* Update : We updated all functionality for wordpress 4.1.

= V1.4.3 - 09.09.2014 =
* Bugfix : The misprint in the code was fixed.
* Bugfix : Check ajax referer is added.
* Update : We updated all functionality for wordpress 4.0.

= V1.4.2 - 12.08.2014 =
* Bugfix : Security Exploit was fixed.
* Update : We updated all functionality for wordpress 4.0-beta3.

= V1.4.1 - 20.05.2014 =
* Bugfix : Bug with replacing, restoring, deleting of links was fixed.
* Bugfix : We fixed a bug which created a database when you first start the plugin.
* Update : BWS plugins section is updated.
* Update : We updated all functionality for wordpress 3.9.1.
* NEW : The Ukrainian language file is added to the plugin.

= V1.4 - 31.03.2014 =
* Update : We added support for custom types.
* Bugfix : Plugin optimization is done.
* Update : BWS menu and screenshots are updated.

= V1.3 - 10.02.2014 =
* NEW : New interface of the plugin page was created.
* NEW : Ajax support for main plugin's functions was added.

= V1.2 - 30.01.2014 =
* Bugfix : The bug for incorrect scanning for new links was fixed.
* NEW : Pagination for links table was added.
* NEW : Translation  into Russian ( ru_RU ) was added.

= V1.1 - 25.01.2014 =
* NEW : Ability to get short links automatically was added.

= V1.0 - 20.01.2014 =
* NEW : Ability to get short links by direct input was added.

== Upgrade Notice ==

= V1.5.7 =
* Bugs fixed.

= V1.5.6 =
* Usability improved.

= V1.5.5 =
* Bugs fixed
* New features added.

= V1.5.4 =
* Bugs fixed

= V1.5.3 =
We fixed bug with short links.

= V1.5.2 =
Plugin optimization completed.

= V1.5.1 =
Instruction on How to get API key has been updated.

= V1.5.0 =
We updated all functionality for wordpress 4.5.

= V1.4.9 =
The bug with plugin menu duplicating was fixed.

= V1.4.8 =
BWS plugins section is updated. We updated all functionality for wordpress 4.3.1.

= V1.4.7 =
We updated all functionality for wordpress 4.2.3.

= V1.4.6 =
We fixed bug with displaying the list of links. We updated all functionality for wordpress 4.2.2.

= V1.4.5 =
Plugin optimization is done. We updated all functionality for wordpress 4.1.1.

= V1.4.4 =
BWS plugins section is updated. We updated all functionality for wordpress 4.1.

= V1.4.3 =
The misprint in the code was fixed. Check ajax referer is added. We updated all functionality for wordpress 4.0.

= V1.4.2 =
Security Exploit was fixed. We updated all functionality for wordpress 4.0-beta3.

= V1.4.1 =
Bug with replacing, restoring, deleting of links was fixed. We fixed a bug creating a database when you first start the plugin. BWS plugins section is updated. We updated all functionality for wordpress 3.9.1. The Ukrainian language file is added to the plugin.

= V1.4 =
We added support for custom types. Plugin optimization is done. BWS menu and screenshots are updated.

= V1.3 =
We remade the interface of the plugin page. Ajax for main plugin's functions was added.

= V1.2 =
We fixed a bug that appeared when user scanned site for new links. We added pagination for the table of links. We added translation into Russian ( ru_RU ).

= V1.1 =
We added the ability to get short links automatically.

= V1.0 =
Upgrade immediately.
