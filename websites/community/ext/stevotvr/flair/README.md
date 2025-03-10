# phpBB Profile Flair Extension

This is a extension for phpBB 3.2 that allows you to add flair to user profiles. These are icons that show up in their profile page and next to each post.

[![Build Status](https://github.com/stevotvr/phpbb-flair/workflows/Tests/badge.svg?branch=master)](https://github.com/stevotvr/phpbb-flair/actions)
[![Code Climate](https://codeclimate.com/github/stevotvr/phpbb-flair/badges/gpa.svg)](https://codeclimate.com/github/stevotvr/phpbb-flair)

## Features

* Adds the ability to assign icons to users, which are displayed on their posts and profile pages
* Flair item features:
    * Name and description
    * Background color and/or Font Awesome icon, or custom image
    * Font color for the optional count
    * Assignment to groups which can be automatic or optionally self-assigned by group members
    * Auto-assignment based on post count or days registered
    * Custom auto-assignments can be added by extensions ([see wiki](https://github.com/stevotvr/phpbb-flair/wiki/Custom-triggers))
* Flair categories allow you to group items and control where they are displayed
* Settings to control where flair is displayed (overrides category settings)
* Legend page showing a categorized list of flair items with names and descriptions (accessible by clicking on a flair item)
* MCP panel and global moderator permission for managing users' profile flair
* UCP panel and user permission to allow user to add/remove optional items assigned to their group and to set favorites
* Support for custom images and ACP panel for uploading custom images

## Install

1. [Download the latest validated release](https://www.phpbb.com/customise/db/extension/profile_flair/).
2. Unzip the downloaded release and copy it to the `ext` directory of your phpBB board.
3. Navigate in the ACP to `Customise -> Manage extensions`.
4. Look for `Profile Flair` under the Disabled Extensions list, and click its `Enable` link.
5. Set up and configure Profile Flair by navigating in the ACP to `Extensions` -> `Profile Flair`.

## Uninstall

1. Navigate in the ACP to `Customise -> Extension Management -> Extensions`.
2. Look for `Profile Flair` under the Enabled Extensions list, and click its `Disable` link.
3. To permanently uninstall, click `Delete Data` and then delete the `/ext/stevotvr/flair` directory.

## Support

* **Important: Only official release versions validated by the phpBB Extensions Team should be installed on a live forum. Pre-release (beta, RC) versions downloaded from this repository are only to be used for testing on offline/development forums and are not officially supported.**
* Report bugs and other issues to our [Issue Tracker](https://github.com/stevotvr/phpbb-flair/issues).
* Support requests should be posted and discussed in the [Profile Flair topic at phpBB.com](https://www.phpbb.com/customise/db/extension/profile_flair/support).

## Translations

* Translations should be posted to the [Profile Flair topic at phpBB.com](https://www.phpbb.com/customise/db/extension/profile_flair/support/topic/184956).

## Donate

If you find this extension useful, please consider supporting the project by donating.

[![Donate via PayPal](https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2RWPKYQ98QT28)

## License
[GNU General Public License v2](http://opensource.org/licenses/GPL-2.0)
