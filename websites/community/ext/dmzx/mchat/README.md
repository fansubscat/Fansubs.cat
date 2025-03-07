phpBB Extension - mChat
=====================

[![Build Status](https://travis-ci.org/kasimi/mChat.svg?branch=master)](https://travis-ci.org/kasimi/mChat)

## Install

1. Download the [latest release](https://github.com/kasimi/mChat/releases).
2. Unzip the downloaded release, and change the name of the folder to `mchat`.
3. In the `ext` directory of your phpBB board, create a new directory named `dmzx` if it does not already exist.
4. Copy the `mchat` folder to `/ext/dmzx/`. If done correctly, the folder structure should look like this: `your forum root)/ext/dmzx/mchat/composer.json`.
5. Navigate in the ACP to `Customise -> Manage extensions`.
6. Look for `mChat` under the `Disabled Extensions` list, and click its `Enable` link.

## Uninstall

1. Navigate in the ACP to `Customise -> Extension Management -> Extensions`.
2. Look for `mChat` under the `Enabled Extensions` list, and click its `Disable` link.
3. To permanently uninstall, click `Delete Data` and then delete the `/ext/dmzx/mchat` folder.

## License

[GNU General Public License v2](http://opensource.org/licenses/GPL-2.0)
