# Changelog

## 1.2.5 (1/21/23)

* PHP8 compability by [@markussoeth](https://github.com/markussoeth) (Thank you!)

## 1.2.4 (6/8/20)

* Fixed image uploads failing on Windows servers

## 1.2.3 (2/3/20)

* Added more explanatory text in the ACP
* Fixed a PHP warning on some ACP pages

## 1.2.2 (12/23/19)

* Fix fatal error when disabling notifications in the ACP
* Fix invalid HTML
* Update description of the user permission

## 1.2.1 (10/7/18)

* Added more instructions to the flair control panels
* Fixed board administrators not having access to the MCP panel
* Fixed layout issues in the flair XCP tiles
* Fixed a typo in the email template
* Added missing language keys for button titles
* Fixed image listing failing on some system
* Fixed PHP 5.4 support

## 1.2.0 (8/23/18)

* Fixed errors when reordering categories
* Changed tile layout to accommodate longer item names

## 1.2.0-beta1 (8/2/18)

* Moved the ACP user flair module to the MCP
* Added option to send notifications when users receive flair
* Added option to limit the number of items shown per category on posts
* Added option to control whether group assignments are automatic
* Added UCP module to allow users to self-assign non-automatic group flair
* Added user permission to control access to the UCP module (defaults to signature access)
* Added favorites system to allow users to select flair items to be shown first
* Fixed display option defaults being ignored

## 1.1.1 (7/10/18)

* Fixed error when multiple triggers have the same value
* Fixed some HTML syntax errors

## 1.1.0 (6/11/18)

* No changes

## 1.1.0-rc1 (5/24/18)

* Fixed size inconsistencies between Font Awesome icons
* Fixed spaces not being accepted in the icon field
* Added Hebrew translation

## 1.1.0-beta3 (5/4/18)

* Added support for SVG images
* Fixed broken image links in the legend

## 1.1.0-beta2 (4/26/18)

* Fixed incorrect permissions being applied to the image directory
* Fixed flair being auto assigned to the guest account

## 1.1.0-beta (4/21/18)

* Added the option to use images for flair items
* Added the ability to upload images from the ACP
* Fixed layout bugs caused by some wide icons
* Fixed error caused by using the extension with an unsupported style
* Updated the link to the Font Awesome icon list
* The fa- prefix will now automatically be added if it is not included
* Fixed list items in the ACP not being clickable
* Improved accessibility markup

## 1.0.3 (1/19/18)

* Fixed unapproved group members being assigned group flair
* Added link to the Font Awesome CSS for styles that don't
* Fixed line breaks in the Font Awesome icon list link

## 1.0.2 (1/5/17)

* Fixed minor error in the user flair management page when there are no categories
* Improved error handling

## 1.0.1 (1/5/17)

* Fixed error caused by users with no group memberships
* Removed anonymous user selector

## 1.0.0 (11/9/17)

* Initial stable release
* Fixed migration reversal leaving behind the categories table

## 0.3.0 (10/30/17)

* Fixed error when installing on phpBB 3.2.0 caused by long key names

## 0.2.2 (9/30/17)

* Fixed errors caused by deleting categories

## 0.2.1 (9/27/17)

* Fixed fatal error when viewing a user profile
* Fixed bad formatting of some error strings
* Fixed the flair item editing form losing state when an error occurs

## 0.2.0 (9/26/17)

* Fixed undefined index notices
* Added ability to assign flair items to groups
* Added automatic assignment based on post count and registration date
* Added `stevotvr.flair.load_triggers` event to allow adding custom auto-assignments

## 0.1.0 (9/7/17)

* Initial beta release
