<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Hand-maintained compatibility patches for generated MediaEmbed definitions.
 *
 * Keep this file PHP 7.1 compatible. Generated data lives in generated/upstream_sites.php.
 * Dot-path patches may remove, replace, or append only fields that differ from upstream.
 *
 */

namespace phpbb\mediaembed\collection;

class compatibility_sites
{
	public const PATCHES = [
		'bluesky' => [
			'unset' => ['helper'],
			'replace' => [
				'attributes.embedder.filterChain' => [],
				'attributes.url.filterChain' => ['urldecode'],
			],
		],
		'mastodon' => [
			'unset' => ['helper'],
		],
		'pastebin' => [
			'append' => [
				'example' => ['https://pastebin.com/9jEf44nc?theme=dark'],
			],
			'replace' => [
				'extract' => [
					"#pastebin\\.com/(?!u/)(?:\\w+(?:\\.php\\?i=|/))?(?'id'\\w+)(?'dark'\\?theme=dark)?#",
				],
				'iframe.src' => '//pastebin.com/embed_iframe/{@id}{@dark}',
			],
		],
		'peertube' => [
			'unset' => ['helper'],
		],
		'vk' => [
			'append' => [
				'extract' => ["!hd=(?'hd'\\d)!"],
			],
			'replace' => [
				'iframe.src' => '//vk.com/video_ext.php?oid={@oid}&id={@vid}&hash={@hash}&hd={@hd}',
			],
		],
		'xenforo' => [
			'unset' => ['helper'],
			'replace' => [
				'host' => ['xenforo.com'],
			],
		],
		'youtube' => [
			'unset' => ['iframe.referrerpolicy'],
		],
	];
}
