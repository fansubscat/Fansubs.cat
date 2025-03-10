# Changelog

### 2.0.2 - 2024-01-21

- Small QoL improvement in the ACP, added sample placeholder data to the custom max-width field in the content-sizing settings.
- Minor code updates.
- Added and/or updated support for media sites:
	- Apple Music
	- x.com (aka Twitter)
	- kick.com
	- Threads
    - Bunny Stream

### 2.0.1 - 2023-04-15

- Refactored the ACP and added AJAX to its user interface.
- Added support for these media sites:
  - bilibili.com
  - mastodon.social
  - tenor.com
  - music.yandex.ru

### 2.0.0 - 2022-03-03

- Backwards incompatible changes require a new minimum phpBB version of 3.3.2.
- New ACP options for custom content sizing:
  - Added a new setting to enlarge most embedded content to fit 100% of the width of the post area.
  - Added a new setting to define custom maximum widths in pixels or percent for individual sites.
- Fixed issues for new installs that would fail if any of phpBB's default forum roles were missing.
- Added language packs:
  - Portuguese
  - Chinese (traditional)

### 1.1.2 - 2020-08-14

- Added a new caching option (to Media Embed Settings) which will cache the scraped content from some media sites, which can speed up performance when repeatedly loading content from some sites.
- Added more informative error messages when this extension cannot be installed successfully.

### 1.1.1 - 2019-06-13

- Fixed an issue that could cause some boards to have a fatal PHP error.
- Internal code updates and language pack corrections (Arabic, French, Italian).
- Updated site: clyp.it
- Added site: allocine.fr

### 1.1.0 - 2019-04-29

- Implemented a new feature allowing users to add new media sites or update 
existing site definitions by dropping simple YAML files into the extension. 
These will be available through our support forum, as well as documentation 
on adding and creating new site YAML files for MediaEmbed.
- Added new media sites using the new YAML implementation:
  - Clyp.it
  - CodePen
  - DotSub
  - Ebaum's World
  - ModDB
  - OK.ru
  - SchoolTube
  - Snotr
  - VideoPress
- Added language packs:
  - Arabic
  - Brazilian Portuguese
  - Chinese
  - Czech
  - Danish
  - Estonian
  - French
  - German
  - Italian
  - Polish
  - Spanish (casual)
  - Turkish

### 1.0.4 - 2019-03-12

- Fixed another issue that could break future versions of phpBB (3.2.6 or newer)

### 1.0.3 - 2019-01-03

- Fixed an issue that could break future versions of phpBB (3.2.6 or newer)

### 1.0.2 - 2018-06-25

- Added a global setting to enable or disable the conversion of plain URLs into embedded content.
- Added a forum based permission, allowing control over who can post embedded content in specific forums.
- Added a user based private messages permission, allowing control over who can post embedded content in their private messages.
- Fixed an issue where embedded content could still be posted by users who did not have permission to use BBCodes in a specific forum.
- Fixed an issue where embedded content could still be posted even though the Disable BBCode option was checked in the post editor.
- When users disable the option to automatically parse URLs in their post, plain URLs will no longer be converted to embedded content either.

### 1.0.1 - 2017-08-04

- Minor code improvements and updates
- Added Dutch language pack
- Added Russian language pack
- Added Spanish language pack

### 1.0.0 - 2017-01-14

- First release
