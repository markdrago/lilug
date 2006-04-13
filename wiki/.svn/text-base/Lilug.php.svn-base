<?php
/**
 * Lilug
 *
 * This skin was adapted from the monobook theme.  A lot of the stuff in here
 * is just left over from that skin.  It wasn't removed in order to avoid
 * disturbing things too much.  That's all.
 *
 * February 2006: Adapted from Monobook by Mark Drago
 *
 * @package Lilug
 * @subpackage Skins
 */

if( !defined( 'MEDIAWIKI' ) )
	die();

/** */
require_once('includes/SkinTemplate.php');

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @todo document
 * @package MediaWiki
 * @subpackage Skins
 */
class SkinLilug extends SkinTemplate {
	/** Using lilug. */
	function initPage( &$out ) {
		SkinTemplate::initPage( $out );
		$this->skinname  = 'lilug';
		$this->stylename = 'lilug';
		$this->template  = 'LilugTemplate';
	}
}

/**
 * @todo document
 * @package MediaWiki
 * @subpackage Skins
 */
class LilugTemplate extends QuickTemplate {
	/**
	 * Template filter callback for Lilug skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 *
	 * @access private
	 */
	function execute() {
		// Suppress warnings to prevent notices about missing indexes in $this->data
		wfSuppressWarnings();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php $this->text('lang') ?>" lang="<?php $this->text('lang') ?>" dir="<?php $this->text('dir') ?>">
  <head>
    <meta http-equiv="Content-Type" content="<?php $this->text('mimetype') ?>; charset=<?php $this->text('charset') ?>" />
    <?php $this->html('headlinks') ?>
    <title><?php $this->text('pagetitle') ?></title>
    <style type="text/css" media="screen,projection">/*<![CDATA[*/ @import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/main.css"; /*]]>*/</style>
    <link rel="stylesheet" type="text/css" <?php if(empty($this->data['printable']) ) { ?>media="print"<?php } ?> href="<?php $this->text('stylepath') ?>/common/commonPrint.css" />
    <!--[if lt IE 5.5000]><style type="text/css">@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/IE50Fixes.css";</style><![endif]-->
    <!--[if IE 5.5000]><style type="text/css">@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/IE55Fixes.css";</style><![endif]-->
    <!--[if gte IE 6]><style type="text/css">@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/IE60Fixes.css";</style><![endif]-->
    <!--[if IE]><script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath') ?>/common/IEFixes.js"></script>
    <meta http-equiv="imagetoolbar" content="no" /><![endif]-->
    <?php if($this->data['jsvarurl']) { ?><script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('jsvarurl') ?>"></script><?php } ?>
    <script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath') ?>/common/wikibits.js"></script>
    <?php if($this->data['usercss']) { ?><style type="text/css"><?php $this->html('usercss') ?></style><?php } ?>
    <?php if($this->data['userjs']) { ?><script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('userjs') ?>"></script><?php } ?>
    <?php if($this->data['userjsprev']) { ?><script type="<?php $this->text('jsmimetype') ?>"><?php $this->html('userjsprev') ?></script><?php   } ?>
    <?php if($this->data['trackbackhtml']) print $this->data['trackbackhtml']; ?>
  </head>
  <body <?php if($this->data['body_ondblclick']) { ?>ondblclick="<?php $this->text('body_ondblclick') ?>"<?php } ?>
        <?php if($this->data['body_onload'    ]) { ?>onload="<?php     $this->text('body_onload')     ?>"<?php } ?>
        <?php if($this->data['nsclass'        ]) { ?>class="<?php      $this->text('nsclass')         ?>"<?php } ?>>
    <div id="header" class="noprint"><a href="/index.php"><img src="../skins/lilug/lilug_logo.gif" alt="Lilug Logo" /></a></div>
    <div id="globalWrapper">
      <div id="column-content">
        <table id="topnav" class="noprint"><tr>
        <td><a href="/index.php">Home</a></td>
        <td><a href="/index.php/About">About</a></td>
        <td><a href="/index.php/Meetings">Meetings</a></td>
        <td><a href="/index.php/Directions">Directions</a></td>
        <td><a href="/index.php/Mailing_Lists">Mailing Lists</a></td>
        </tr></table>
	<div id="content">
	  <a name="top" id="top"></a>
	  <?php if($this->data['sitenotice']) { ?><div id="siteNotice"><?php $this->html('sitenotice') ?></div><?php } ?>
	  <h1 class="firstHeading"><?php $this->text('title') ?></h1>
	  <div id="bodyContent">
	    <h3 id="siteSub"><?php $this->msg('tagline') ?></h3>
	    <div id="contentSub"><?php $this->html('subtitle') ?></div>
	    <?php if($this->data['undelete']) { ?><div id="contentSub"><?php     $this->html('undelete') ?></div><?php } ?>
	    <?php if($this->data['newtalk'] ) { ?><div class="usermessage"><?php $this->html('newtalk')  ?></div><?php } ?>
	    <!-- start content -->
	    <?php $this->html('bodytext') ?>
	    <?php if($this->data['catlinks']) { ?><div id="catlinks"><?php       $this->html('catlinks') ?></div><?php } ?>
	    <!-- end content -->
	    <div class="visualClear"></div>
	  </div>
	</div>
      <table id="meta" class="noprint"><tr id="toprow"><td>
      <div class="wiki_links">
        <?php
        foreach($this->data['content_actions'] as $key => $action) {
	    echo "<a href=\"";
	    echo htmlspecialchars($action['href']) . "\">";
	    echo htmlspecialchars($action['text']) . "</a><br />\n";
	}
	?>
      </div>
      </td><td>
      <div class="wiki_links">
        <?php
	foreach($this->data['personal_urls'] as $key => $item) {
	    echo "<a href=\"" . htmlspecialchars($item['href']) . "\"";
	    if(!empty($item['class'])) {
	      echo "class=\"" . htmlspecialchars($item['class']) . "\"";
	    }
	    echo ">";
	    echo htmlspecialchars($item['text']);
	    echo "</a><br />\n";
	}
	?>
      </div>
      </td><td>
    <div class="wiki_links">
    <?php
    if ($this->data['notspecialpage']) {
        foreach(array('whatlinkshere', 'recentchangeslinked') as $special ) {
	  echo "<a href=\"" . htmlspecialchars($this->data['nav_urls'][$special]['href']) . "\">";
	  echo $this->msg($special) . "</a><br />\n";
	}
    }

    if (isset($this->data['nav_urls']['trackbacklink'])) {
	echo "<a href=\"" . htmlspecialchars($this->data['nav_urls']['trackbacklink']['href']) . "\">";
	echo $this->msg('trackbacklink') . "</a><br />\n";
    }

    if ($this->data['feeds']) {
	foreach($this->data['feeds'] as $key => $feed) {
	    echo "<a href=\"" . htmlspecialchars($feed['href']) . "\">";
	    echo htmlspecialchars($feed['text']) . "</a><br />\n";
	}
    }
    
    foreach(array('contributions', 'emailuser', 'upload', 'specialpages') as $special ) {
	if($this->data['nav_urls'][$special]) {
	    echo "<a href=\"" . htmlspecialchars($this->data['nav_urls'][$special]['href']) . "\">";
	    echo $this->msg($special) . "</a><br />\n";
	}
    }
    
    if(!empty($this->data['nav_urls']['print']['href'])) {
	echo "<a href=\"" . htmlspecialchars($this->data['nav_urls']['print']['href']) . "\">";
	echo $this->msg('printableversion') . "</a><br />\n";
    }
    ?>
    </div>
    </td></tr><tr><td colspan="3">
    <div id="search">
      <form name="searchform" action="<?php $this->text('searchaction') ?>" id="searchform">
      <?php
      if( isset( $this->data['search'] ) ) {
	$searchval = "value=\"" . $this->text('search') . "\" ";
      }
      ?>
        <input id="searchInput" name="search" type="text" <?=$searchval?>/>
        <input type='submit' name="go" class="searchButton" id="searchGoButton" value="Go" />&nbsp;<input type='submit' name="fulltext" class="searchButton" value="Search" />
      </form>
    </div>
    </td></tr></table>
    </div>
    </div>
    <?php $this->html('reporttime') ?>
  </body>
</html>
<?php
	wfRestoreWarnings();
	}
}
?>
