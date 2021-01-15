# Fansubs.cat

Website and backend services for Fansubs.cat, a site that aggregates all content produced by all Catalan fansubbing groups, and includes the following sites:
- [www.fansubs.cat](https://www.fansubs.cat/), a site where users can read news from all Catalan fansubs.
- [anime.fansubs.cat](https://anime.fansubs.cat/), a site where users can watch anime released by all Catalan fansubs.
- [manga.fansubs.cat](https://manga.fansubs.cat/), a site where users can read manga released by all Catalan fansubs.

## Why this site?

Initially, this site was only a news aggregator. There were several Catalan fansubs and we thought that it would be cool to have them all centralized on one site. With that site, you could take a quick look and see if there were any news on any of the fansub sites.<br/>
After that, we added a site to read manga released by any Catalan fansub. And later, another site to watch anime released by any Catalan fansub.<br/>
Anywhere on the site, attribution is provided and links are shown in order to not steal visitors from the original fansub pages. Of course, the site is completely non-profit and will always remain like that.

## Contributing

All contributions are welcome. This is a collaborative project! :)

## How does it work?

The site is run on a simple machine with Debian 10 (Buster), with an Apache 2.4 + PHP 7.3 + MariaDB 10.3 server.

The news site allows displaying and filtering of the news database, and the news are inserted into the database by web scraping the different fansub websites.<br/>
The anime and manga sites are managed by the fansub users, which upload content and links or files into it. The anime site also fetches links automatically when properly configured.

Take a look at the code if you want to know more, or create an issue if in doubt! :)

## License

This project is licensed under the [GNU Affero Public License 3.0](https://github.com/fansubscat/Fansubs.cat/blob/master/LICENSE). This basically means that you can do whatever you want, but if you use this on a website, you **must** release the modified code.

All content, assets and images used are property of their original authors and not subject to the license. If you want them removed, please contact us!
