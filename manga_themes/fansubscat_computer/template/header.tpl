<!DOCTYPE html>
<html lang="{$lang_info.code}" dir="{$lang_info.direction}">
<head>
<meta charset="{$CONTENT_ENCODING}">
<meta name="generator" content="Piwigo (aka PWG), see piwigo.org">

{if isset($meta_ref)} 
{if isset($INFO_AUTHOR)}
<meta name="author" content="{$INFO_AUTHOR|strip_tags:false|replace:'"':' '}">
{/if}
{if isset($related_tags)}
<meta name="keywords" content="{foreach from=$related_tags item=tag name=tag_loop}{if !$smarty.foreach.tag_loop.first}, {/if}{$tag.name}{/foreach}">
{/if}
{if isset($COMMENT_IMG)}
<meta name="description" content="{$COMMENT_IMG|strip_tags:false|replace:'"':' '}{if isset($INFO_FILE)} - {$INFO_FILE}{/if}">
{else}
<meta name="description" content="{$PAGE_TITLE}{if isset($INFO_FILE)} - {$INFO_FILE}{/if}">
{/if}
{/if}

<title>{if $PAGE_TITLE!=l10n('Home') && $PAGE_TITLE!=$GALLERY_TITLE}{$PAGE_TITLE} | {/if}{$GALLERY_TITLE}</title>
<link rel="shortcut icon" type="image/x-icon" href="/local/favicon.ico">

<link rel="start" title="{'Home'|translate}" href="{$U_HOME}" >
<link rel="search" title="{'Search'|translate}" href="{$ROOT_URL}search.php" >

{if isset($first.U_IMG)   }<link rel="first" title="{'First'|translate}" href="{$first.U_IMG}" >{/if}
{if isset($previous.U_IMG)}<link rel="prev" title="{'Previous'|translate}" href="{$previous.U_IMG}" >{/if}
{if isset($next.U_IMG)    }<link rel="next" title="{'Next'|translate}" href="{$next.U_IMG}" >{/if}
{if isset($last.U_IMG)    }<link rel="last" title="{'Last'|translate}" href="{$last.U_IMG}" >{/if}
{if isset($U_UP)          }<link rel="up" title="{'Thumbnails'|translate}" href="{$U_UP}" >{/if}

{if isset($U_PREFETCH)    }<link rel="prefetch" href="{$U_PREFETCH}">{/if}
{if isset($U_CANONICAL)   }<link rel="canonical" href="{$U_CANONICAL}">{/if}

{if not empty($page_refresh)}<meta http-equiv="refresh" content="{$page_refresh.TIME};url={$page_refresh.U_REFRESH}">{/if}

{strip}
{foreach from=$themes item=theme}
  {if $theme.load_css}
  {combine_css path="themes/`$theme.id`/theme.css" order=-10}
  {/if}
  {if !empty($theme.local_head)}
  {include file=$theme.local_head load_css=$theme.load_css}
  {/if}
{/foreach}

{combine_script id="jquery" load="footer"}
{/strip}

<!-- BEGIN get_combined -->
{get_combined_css}

{literal}
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-628107-15"></script>
<script src="/themes/fansubscat_computer/imageMapResizer.js"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-628107-15');

  window.onload = function () {
    imageMapResize();
  }
</script>
{/literal}

{get_combined_scripts load='header'}
<!-- END get_combined -->

<!--[if lt IE 7]>
<script type="text/javascript" src="{$ROOT_URL}themes/default/js/pngfix.js"></script>
<![endif]-->

{if not empty($head_elements)}
{foreach from=$head_elements item=elt}
  {$elt}
{/foreach}
{/if}
</head>

<body id="{$BODY_ID}">

<div id="the_page">

{if not empty($header_msgs)}
<div class="header_msgs">
  {foreach from=$header_msgs item=elt}
  {$elt}<br>
  {/foreach}
</div>
{/if}

<div id="theHeader">{* This is the only change made to the template *}
	<div class="page-title-block">
		<a class="page-title" href="/">Fansubs.cat</a>
		<div class="page-links">
			<a href="https://anime.fansubs.cat/">Anime</a> | <b>Manga</b> | <a href="https://www.fansubs.cat/">Notícies</a>
		</div>
	</div>
	En aquesta galeria pots llegir tot el manga en català que han editat els diferents fansubs i que no ha estat llicenciat en la nostra llengua.
</div>

{if not empty($header_notes)}
<div class="header_notes">
  {foreach from=$header_notes item=elt}
  <p>{$elt}</p>
  {/foreach}
</div>
{/if}
