# Fansubs.cat

[![Versió en català](https://img.shields.io/badge/Versi%C3%B3%20en%20catal%C3%A0%20disponible%20aqu%C3%AD-blue.svg)](https://github.com/fansubscat/Fansubs.cat/blob/master/README.md)

Website and backend services for Fansubs.cat, a site that aggregates all content produced by all Catalan fansubbing groups.

## Sites and services

This project includes the source code for the following sites:
- Main: The main landing page.
- Admin: An admin panel to manage all dynamic content on the site.
- Advent: A site that displays advent calendars set up by the fansubbing groups.
- API: An API that feeds both internal and external services, including the Tachiyomi/Mihon extension.
- Catalogue: A site that displays anime, manga or live action content generated by the fansubbing groups.
- News: A site that aggregates news from different URLs into a single page.
- Static: Directory structure for static content storage.
- Users: A site that allows managing the user profile.

And also includes the following extra pieces of code:
- Android app: Displays news and receives push notifications when new content is available. Currently unmaintained.
- Services: Internal services that keep the site running, normally via cron jobs.

## Why this site?

Initially, this site was only a news aggregator. There were several Catalan fansubs and we thought that it would be cool to have them all centralized on one site. With that site, you could take a quick look and see if there were any news on any of the fansub sites. After some time, we added a site to read all manga released by any Catalan fansub. And later, another site to watch anime released by any Catalan fansub. The code has been evolving constantly with new features.

Anywhere on the site, attribution is provided and links are shown in order to not steal visitors from the original fansub pages. Of course, the site is completely non-profit and will always remain like that.

## How does it work?

The site is run on a simple machine with Debian 12 (Bookworm), with an Apache 2.4 + PHP 8.2 + MariaDB 10.11 server.

There are many services and sites included, as stated above. Describing all of them in detail would take some time, so please take a look at the code if you want to know more, or create an issue if in doubt! :)

## Contributing

All reasonable contributions are welcome. When submitting pull requests, please describe the issue and changes applied in a clear way, and keep it in style with the current codebase.

## License

This project is licensed under the [GNU Affero Public License 3.0](https://github.com/fansubscat/Fansubs.cat/blob/master/LICENSE). This basically means that you can do whatever you want, but if you use this on a website, you **must** release the modified code.

Some assets and images used in the source code are property of their original authors and not subject to this license. If you want them removed, please contact us.