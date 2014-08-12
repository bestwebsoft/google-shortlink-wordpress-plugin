=== Google Shortlink ===
Contributors: bestwebsoft
Donate link: https://www.2checkout.com/checkout/purchase?sid=1430388&quantity=1&product_id=94
Tags: change link, display links, display multiple links, get short links, get short links automatically, get short links by direct input, get short links without leaving site, goo.gl, googel, googgle, gogle, gogole, google, google shortlink plugin, external links, link statisctics,  links, redirect, replace external links, replace url, short, short url, shortener, shortlink, shorturl, swap links, tinyurl, url
Requires at least: 3.1
Tested up to: 4.0-beta3
Stable tag: 1.4.2
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin allows you to get short links from goo.gl servise without leaving your site. 

== Description ==

This plugin allows you to get short links from goo.gl service without leaving your site. It works in two modes: get short links by direct input and get short link for all external links on your site automatically. With this plugin you can replace all external links on your site with short links. The plugin also displays a click-through statistics for each short link.

http://www.youtube.com/watch?v=SZIWLm8mmdU

<a href="http://wordpress.org/plugins/google-shortlink/faq/" target="_blank">FAQ</a>

<a href="http://support.bestwebsoft.com" target="_blank">Support</a>

= Features =

* Actions: You can get a short link by direct input.
* Actions: You can get a short links for all external links on your site automatically.
* Actions: You can swap normal links to short links and vice versa on your site anytime.
* Display: You can choose the number of links to display on a page.
* Display: You can view the click-through statistics of short links.

= Recommended Plugins =

The author of the Google Shortlink also recommends the following plugins:

* <a href="http://wordpress.org/plugins/updater/">Updater</a> - This plugin updates WordPress core and the plugins to the recent versions. You can also use the auto mode or manual mode for updating and set email notifications.
There is also a premium version of the plugin <a href="http://bestwebsoft.com/plugin/updater-pro/?k=ed72e881dcfb65a3487b083775c694c1">Updater Pro</a> with more useful features available. It can make backup of all your files and database before updating. Also it can forbid some plugins or WordPress Core update.

= Translation =

* Russian (ru_RU)
* Ukrainian (uk)

If you would like to create your own language pack or update the existing one, you can send <a href="http://codex.wordpress.org/Translating_WordPress" target="_blank">the text of PO and MO files</a> for <a href="http://support.bestwebsoft.com" target="_blank">BestWebSoft</a> and we'll add it to the plugin. You can download the latest version of the program for work with PO and MO files  <a href="http://www.poedit.net/download.php" target="_blank">Poedit</a>.

= Technical support =

Dear users, our plugins are available for free download. If you have any questions or recommendations regarding the functionality of our plugins (existing options, new options, current issues), please feel free to contact us. Please note that we accept requests in English only. All messages in another languages won't be accepted.

If you notice any bugs in the plugins, you can notify us about it and we'll investigate and fix the issue then. Your request should contain URL of the website, issues description and WordPress admin panel credentials.
Moreover we can customize the plugin according to your requirements. It's a paid service (as a rule it costs $40, but the price can vary depending on the amount of the necessary changes and their complexity). Please note that we could also include this or that feature (developed for you) in the next release and share with the other users then. 
We can fix some things for free for the users who provide translation of our plugin into their native language (this should be a new translation of a certain plugin, you can check available translations on the official plugin page).

== Installation ==

1. Upload the `google-shortlink` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin via the 'Plugins' menu in WordPress.
3. Plugin settings are located in "BWS Plugins" > "Google Shortlink".
4. Plugin page is located in main menu.
4. Enter your API key for goo.gl account on plugin options page.

== Frequently Asked Questions ==

= Where can I get api key for this plugin? =

1. You must go to <a href="https://cloud.google.com/console/project">Google develoters console</a> and create a new project ( set fields Project name and Progect id as you wish ).
2. After creating the project go to the "APIs & auth" tab.
3. Find in the list of avaliable apis "URL Shortener API" and set it ON.
4. After that go to "Credentials" tab and click "CREATE NEW KEY" button in "Public API access" area.
5. After all in pop-up window choose "browser key". The generated key will appear on the page, and you can regenerate it anytime.

= How to replace all external links on my site with short links? =

1. Go to the plugin main page "Google Shortlink" in the main menu( Dashboard ).
2. Go to Additional options tab.
3. Set 'Scan web-site for new external links ' option on and click the button "Apply" below. Scanning your website may take some time, don't be afraid, it's normal.
4. Now you can manage those links as your wish with options on the main page. In particular case to replase all external links to short links set "Replace automatically all external links" option on and press "Apply button".

= Can I get short link for any link that I want? =

Yes, just paste it to one of the fields below "Type long links here:" title on 'Direct input' tab at plugin's main page and press a button "Get short links". A short link will appear beside and also be saved in db.

= I have some problems with the plugin's work. What Information should I provide to receive proper support? =

Please make sure that the problem hasn't been discussed yet on our forum (<a href="http://support.bestwebsoft.com" target="_blank">http://support.bestwebsoft.com</a>). If no, please provide the following data along with your problem's description:
1. the link to the page where the problem occurs
2. the name of the plugin and its version. If you are using a pro version - your order number.
3. the version of your WordPress installation
4. copy and paste into the message your system status report. Please read more here: <a href="https://docs.google.com/document/d/1Wi2X8RdRGXk9kMszQy1xItJrpN0ncXgioH935MaBKtc/edit" target="_blank">Instuction on System Status</a>

== Screenshots ==

1. 'Table of links' tab on plugin's main page.
2. 'Direct input' tab on plugin's main page.
3. 'Additional options' tab on plugin's main page.
4. Plugin settings page in WordPress admin panel.
5. 'FAQ' tab on plugin's main page

== Changelog ==

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
