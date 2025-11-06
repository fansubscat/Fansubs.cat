# Installing and configuring the Fansubs.cat code

[![Versió en català](https://img.shields.io/badge/Versi%C3%B3%20en%20catal%C3%A0%20disponible%20aqu%C3%AD-blue.svg)](https://github.com/fansubscat/Fansubs.cat/blob/master/INSTALLING.md)

## Introduction

This document details how to install the code from the Fansubs.cat site and the related background services. Since this document is targeted to people who want to install it in other domains and in other languages, we will try to detail all things that are specifically tailored for the Catalan language version, how you can change the language and how to edit the most language-specific code.

The code for Fansubs.cat was first written in 2015. This implies that some portions of the code are not so clear and the oldest portion, the news site, is highly coupled with the format used on blogs from Catalan fansubs. Therefore, we recommend disabling it if you pretend to make an install independent from Fansubs.cat. In this case, we also recommend disabling the «Links» section, which was designed to link back to the Catalan community, and changing the «Who are we?» section and the privacy policy. You will find an explanation of how to do that later.

## Required hardware

The Fansubs.cat web server is a simple VPS with an amd64 architecture, 1 GB of RAM and a 25 GB hard drive. The code is designed to work with one or several storage and streaming servers, which should have the adequate dimensions for storing the content and making it visible through HTTP. The anime and live action sites can work directly by streaming from MEGA, but the manga site explicitly requires a storage server.

## Required software

To install and use the Fansubs.cat code, you will need:
- Debian Linux 12
- Apache 2.4
- MariaDB 10.11
- PHP 8.2
- MegaCMD 1.6

## Installing the server

Install Debian Linux 12 («bookworm») normally.

Install these required packages:

	apt install apache2 php mariadb-server php-mysql php-curl php-dom php-gd php-mbstring php-zip imagemagick
	
Install MegaCMD by following the instructions at https://mega.io/cmd#download.

Create the `/srv/fansubscat/` directory, which will store the Fansubs.cat code.

Copy the `common`, `database`, `services`, `temporary` and `websites` directories from the code to `/srv/fansubscat`.

Create a database with its username and password by running `mariadb -u root` and typing:

	CREATE DATABASE fansubscat;
	GRANT ALL ON fansubscat.* TO 'user'@'localhost' IDENTIFIED BY 'password';
	FLUSH PRIVILEGES;

Then, change to your database and run the initial import:

	USE fansubscat;
	\. /srv/fansubscat/database/database_structure.sql
	\. /srv/fansubscat/database/initial_values.sql

You can now exit MariaDB.

Create an Apache host for each subdomain and redirect it to the following directories. You can use any domain name and you can also change the name of each subdomain:

* `www.maindomain.xyz` **and** `maindomain.xyz` -> `/srv/fansubscat/websites/main/`
* `admin.maindomain.xyz` -> `/srv/fansubscat/websites/admin/`
* `anime.maindomain.xyz` -> `/srv/fansubscat/websites/catalogue/`
* `liveaction.maindomain.xyz` -> `/srv/fansubscat/websites/catalogue/`
* `manga.maindomain.xyz` -> `/srv/fansubscat/websites/catalogue/`
* `news.maindomain.xyz` -> `/srv/fansubscat/websites/news/`
* `static.maindomain.xyz` -> `/srv/fansubscat/websites/static/`
* `users.maindomain.xyz` -> `/srv/fansubscat/websites/users/`
* `advent.maindomain.xyz` -> `/srv/fansubscat/websites/advent/`
* `api.maindomain.xyz` -> `/srv/fansubscat/websites/api/`
* `community.maindomain.xyz` -> `/srv/fansubscat/websites/community/`

You must also create the following hosts for the hentai domain:

* `www.hentaidomain.xyz` **and** `hentaidomain.xyz` -> `/srv/fansubscat/websites/main/`
* `anime.hentaidomain.xyz` -> `/srv/fansubscat/websites/catalogue/`
* `manga.hentaidomain.xyz` -> `/srv/fansubscat/websites/catalogue/`
* `news.hentaidomain.xyz` -> `/srv/fansubscat/websites/news/`
* `static.hentaidomain.xyz` -> `/srv/fansubscat/websites/static/`
* `users.hentaidomain.xyz` -> `/srv/fansubscat/websites/users/`
* `api.hentaidomain.xyz` -> `/srv/fansubscat/websites/api/`

You will need to enable the Apache `proxy` and `headers` modules. You can do that by running:

	a2enmod rewrite proxy headers
	
The site requires using SSL. We recommend using Certbot. Certificate setup and the configuration for each Apache host is outside of the scope of this document: set them up as you deem more convenient.

All files stores under `/srv/fansubscat` must be owned by the `www-data` user or errors will occur when saving generated content. You can change all of them by using:

	chmod -R www-data:www.data /srv/fansubscat
	
All .sh files in the `services` directory must have the executable flag set. You can set it by running:

	chmod a+x /srv/fansubscat/services/*.sh
	
Set up the cron jobs that run the services by running `crontab -e -u www-data` and copying the contents of the `cron_jobs/crontab.txt` file in the code.

If you want to be able to upload RAR archives, you will need to install the php-rar PECL extension.

## Configuring the site

Under the `common/config` directory you will find a `config.example.inc.php` file. You must rename it to `config.inc.php` and set it up as needed. You must enter your database access credentials, domains and subdomains, the name for the sites, users and API keys from social networks (Bluesky, Discord, Mastodon, Telegram and X), an SMTP server for posting e-mails, etc. You will find an explanation of each field in the same file.

Change `/etc/php/8.2/apache2/php.ini` and set `session.cookie_lifetime` to `0`. If you installed the php-rar extension, enable it by using `extension=rar.so`.

In the `websites/users/.htaccess` file, change the regular expression at the line starting with `SetEnvIf Origin` so it matches your domains.

In order to ensure an optimal result, you will have to change all `.xml` and `.webmanifest` files in the `websites/static/favicons` subdirectories to reflect your domain names and subdomains and the site titles.

Once all that is done, the site will be accessible. You will have to log into the admin panel with your desired username and password for the administrator account, which will be created at that time.

Now the site will be up, but looking just like Fansubs.cat. In the next section we find how you can modify that.

This guide does not include the installation and setup of the community forum: this step is outside of the scope of this document and you will have to set it up on your own.

## Changing the language and customizing the site

The current codebase is made specifically for Fansubs.cat, that uses Catalan. If you want your setup to use another language, you will have to modify certain elements:

1) Copy the `common/languages/lang_ca.json` file and translate it into your language. If you find any string referencing the Catalan language, change it to your language or edit it as you wish. You will probably also want to change the privacy policy and the explanation for the «Who are we?» section, which is highly specific to the Catalan case.

2) Build the language file for the JavaScript code by running the `rebuild_javascript_strings.php` script under the `services` directory.

3) Copy the `websites/static/js/videostream-lang_ca.js` file and translate it into your language (or get it from the VideoJS site, if it already exists).

4) Edit the `common/config/config.inc.php` file and change the `SITE_LANGUAGE` attribute to the ISO code for your language (it must be the same as the one in the file names you edited in steps 1, 2 and 3) and the `SITE_LOCALE` attribute to your desired locale (it has to be installed in your system).

5) Edit all `.htaccess` files from the `websites` subdirectories so all short URLs specified in them match the ones in your language file (they are all the strings that start with `url.`).

6) Edit all `.webmanifest` files from the `websites/static/favicons` subdirectories so that the site titles are in the corresponding language, and change their icons if you prefer.

7) Change all existing logos and images in `websites/static/images/site` with yours, but keep them at the same sizes as the original ones.

8) You can disable the following functionalities or parts of the site in the `common/config/config.inc.php` file:
	* `DISABLE_NEWS`: Disables functionality related to news (you will have to make the subdomain not accessible or just not create it).
	* `DISABLE_LINKS`: Disables functionality related to the links section in the main site (you will have to make it not accessible by editing its reference in the `.htaccess` file).
	* `DISABLE_LIVE_ACTION`: Disables functionality related to live action content (you will have to make the subdomain not accessible or just not create it).
	* `DISABLE_ADVENT`: Disables functionality related to advent calendars (you will have to make the subdomain not accessible or just not create it).
	* `DISABLE_RESOURCES`: Hides the link to the resources site in the admin panel.
	* `DISABLE_COMMUNITY`: Hides the link and the sync with the community forum.
	* `DISABLE_FOOLS_DAY`: Disables the special functionality for December 28th.
	* `DISABLE_SANT_JORDI_DAY`: Disables the special functionality for April 23th.
	* `DISABLE_HALLOWEEN_DAYS`: Disables the special functionality for October 31st and November 1st.
	* `DISABLE_CHRISTMAS_DAYS`: Disables the special functionality for December 5th to January 6th.
	* `DISABLE_STATUS`: Disables the status page link at the site footer.
	* `DISABLE_REMOTE_STORAGE_FOR_STREAMING`: Disables the external storage server for streaming video (MEGA will be used instead).
	* `DISABLE_REMOTE_STORAGE_FOR_MANGA`: Disables the external storage server for manga (the local `storage` directory will be used).

9) When you already have content in your site, change the social network previews existing in `websites/static/social` with your own.

If you have followed all these steps, your site should have your own appearance and should be in your language. Now you just have to fill it with content!
