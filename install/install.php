<?php
// ----------------------------------------------------------------------
// Updated June 2014
// Copyright (c) 2007 by Tammy Keefer
// Based on eFiction 1.1
// Copyright (C) 2003 by Rebecca Smallwood.
// http://efiction.sourceforge.net/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------


/**
 * Revert PHP 8.1 mysqli default error mode
 * commented out by default - uncomment if you run into any db related issues with this file
 * @link https://github.com/php/php-src/blob/4025cf2875f895e9f7193cebb1c8efa4290d052e/UPGRADING#L101-L105
 */
// mysqli_report(MYSQLI_REPORT_OFF);


function random_char($string)
{
	$length = strlen($string);
	$position = mt_rand(0, $length - 1);
	return $string[$position];
}

function random_string($charset_string, $length)
{
	$return_string = random_char($charset_string);
	for ($x = 1; $x < $length; $x++)
		$return_string .= random_char($charset_string);
	return $return_string;
}

$randomcharset = '23456789' . 'abcdefghijkmnpqrstuvwxyz' . 'ABCDEFGHJKLMNPQRSTUVWXYZ';


// Strip out slashes if magic quotes isn't on.
if (ini_get("magic_quotes_gpc"))
{
	foreach ($_POST as $var => $val)
	{
		$val = is_array($val) ? array_map('stripslashes', $val) : stripslashes($val);
	}
	foreach ($_GET as $var => $val)
	{
		$val = is_array($val) ? array_map('stripslashes', $val) : stripslashes($val);
	}
	foreach ($_COOKIE as $var => $val)
	{
		$val = is_array($val) ? array_map('stripslashes', $val) : stripslashes($val);
	}
}

define("_BASEDIR", "../");

Header('Cache-Control: private, no-cache, must-revalidate, max_age=0, post-check=0, pre-check=0');
header("Pragma: no-cache");
header("Expires: 0");

$output = "";
$language = "";

include("../includes/class.TemplatePower.inc.php");

/* PHP 8 fix - other way is separated LANs to smaller files */
$allowed_tags = '';
$recentdays = 7;
$sitename = "";  // this breaks stuff in PHP 8.2 
$url = "";
$multiplecats = '';
$minwords = '';
$maxwords = '';
$action = '';
$pwdsetting = '';
$imagewidth = '';
$imageheight = '';
$version = '';
$user = array();
$user['penname'] = '';
$user['email'] = '';
$penname = '';

if (!isset($_GET['step']) || $_GET['step'] == 2)
{
	include("../languages/en.php");
	include("../languages/en_admin.php");
	include("languages/en.php");
}
else if (isset($_REQUEST['language']))
{
	$language = $_REQUEST['language'];
	if (file_exists("../languages/" . $language . ".php")) include("../languages/" . $language . ".php");
	else include("../languages/en.php");
	if (file_exists("../languages/" . $language . "_admin.php")) include("../languages/" . $language . "_admin.php");
	else include("../languages/en_admin.php");
	if (file_exists("languages/" . $language . ".php")) include("languages/" . $language . ".php");
	else include("languages/en.php");
}
else
{
	include("../config.php");

	$dbconnect = dbconnect($dbhost, $dbuser, $dbpass, $dbname);
	$settings = dbquery("SELECT tableprefix, language FROM " . $settingsprefix . "fanfiction_settings WHERE sitekey = '" . $sitekey . "'");
	list($tableprefix, $language) = dbrow($settings);

	define("TABLEPREFIX", $tableprefix);
	define("SITEKEY", $sitekey);
	if (file_exists("../languages/" . $language . ".php")) include("../languages/" . $language . ".php");
	else include("../languages/en.php");
	if (file_exists("../languages/" . $language . "_admin.php")) include("../languages/" . $language . "_admin.php");
	else include("../languages/en_admin.php");
	if (file_exists("languages/" . $language . ".php")) include("languages/" . $language . ".php");
	else include("languages/en.php");
}
include(_BASEDIR . "includes/corefunctions.php");

//  So I don't have to keep updating the version number in 3 different files.
include("../version.php");

//make a new TemplatePower object
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html><head><title>eFiction {$version} Install</title>
<style>
a.pophelp{
    position: relative; /* this is the key*/
    z-index:24;
    vertical-align: super;
    text-decoration: none;
}

a.pophelp:hover{z-index:100; border: none; text-decoration: none;}

a.pophelp span{display: none; position: absolute; text-decoration: none;}

a.pophelp:hover span{ /*the span will display just on :hover state*/
    display:block;
    position: absolute;
    top: 0; left: 8em; width: 225px;
    border:1px solid #000;
    background-color:#CCC; color:#000;
    text-decoration: none;
    text-align: left;
    padding: 5px;
    font-weight: normal;
}
.required { color: red; }
</style>
<link rel=\"stylesheet\" type=\"text/css\" href='../default_tpls/style.css'></head>";

// leftover data that would be right after the <style> tag above
// LABEL { float: left; display: block; width: 45%; text-align: right; padding-right: 10px; clear: left;}
// .row { float: left; width: 99%; }
// #settingsform FORM { width: 80%; margin; 0 auto; }
// #settingsform LABEL { float: left; display: block; width: 30%; text-align: right; padding-right: 10px; clear: left; }
// #settingsform .fieldset SPAN { float: left; display: block; width: 30%; text-align: right; padding-right: 10px; clear: left;}
// #settingsform .fieldset LABEL { float: none; width: auto; display: inline; text-align: left; clear: none; }
// #settingsform .tinytoggle { text-align: center; }
// #settingsform .tinytoggle LABEL { float: none; display: inline; width: auto; text-align: center; padding: 0; clear: none; }
// #settingsform #submit { display: block; margin: 1ex auto; }

$tpl = new TemplatePower("../default_tpls/default.tpl");
$tpl->assignInclude("header", "./../default_tpls/header.tpl");
$tpl->assignInclude("footer", "./../default_tpls/footer.tpl");
$tpl->prepare();
$tpl->newBlock("header");
$tpl->assign("sitename", "eFiction $version Install");
$tpl->gotoBlock("_ROOT");
$tpl->newBlock("footer");
$tpl->assign("footer", "eFiction $version &copy; 2007. <a href='http://efiction.org/'>http://efiction.org/</a>");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['step']))  $_GET['step'] = 0;
switch ($_GET['step'])
{
	case "9":
		if (isset($_POST['submit']))
		{
			$penname = descript($_POST['newpenname']);
			if ((!$_POST['email']) && !isADMIN) $fail .= "<div style='text-align: center;'>" . _EMAILREQUIRED . " " . _TRYAGAIN . "</div>";
			if ($penname && !preg_match("!^[a-z0-9_ ]{3,30}$!i", $penname)) $fail = "<div style='text-align: center;'>" . _BADUSERNAME . " " . _TRYAGAIN . "</div>";
			if (!preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9.-]+$/", $_POST['email'])) $fail = "<div style='text-align: center;'>" . _INVALIDEMAIL . " " . _TRYAGAIN . "</div>";
			if ($_POST['password'] == $_POST['password2']) $encryptpassword = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 12]);
			else $fail =  write_message(_PASSWORDTWICE);
			if (!isset($fail))
			{
				$result = dbquery("INSERT INTO " . $tableprefix . "fanfiction_authors(penname, email, password, date) VALUES('$penname', '" . $_POST['email'] . "', '$encryptpassword', '" . time() . "') RETURNING 'uid'");
				// TODO Should check this in includes/pg_functions; sends uid_0
				//define("USERUID", dbinsertid());
				define("USERUID", intval($result));
				$skinquery = dbquery("SELECT skin FROM " . $settingsprefix . "fanfiction_settings WHERE sitekey = '" . SITEKEY . "'");
				list($skin) = dbrow($skinquery);
				$result2 = dbquery("INSERT INTO " . $tableprefix . "fanfiction_authorprefs(uid, userskin, level) VALUES('" . USERUID . "', '$skin', '1')");
				if ($result && $result2) $output .= write_message(_ACTIONSUCCESSFUL . "<br /><br />Installation complete!  <a href='../user.php?action=login'>Log in</a> to your site and go to the Admin area to configure your archive. <strong>Note:</strong> Please delete the install/ folder!");
			}
			else $output .= $fail;
		}
		else
		{
			$output .= "<div id='pagetitle'>" . _ADMINACCT . "</div><form method=\"POST\" class='tblborder' style='margin: 1em auto; width: 400px;' enctype=\"multipart/form-data\" action=\"install.php?step=9\">
			<table class='acp' style='margin: 0 auto;'>
				<tr>
					<td><label for='newpenname'>" . _PENNAME . ":</label></td>
					<td><input name=\"newpenname\" type=\"text\" class=\"textbox\" maxlength=\"200\" value=\"" . $user['penname'] . "\"></td>
				</tr>
				<tr>
					<td><label for='email'>" . _EMAIL . ":</label></td>
					<td><INPUT  type=\"text\" class=\"textbox=\" name=\"email\" value=\"" . $user['email'] . "\"maxlength=\"200\"></td>
				</tr>
				<tr>
					<td><label for='password'>" . _PASSWORD . ":</label></td>
					<td><INPUT name=\"password\" class=\"textbox\" value=\"\" type=\"password\"></td>
				</tr>
				<tr>
					<td><label for='password2'>" . _PASSWORD2 . ":</label></td>
					<td><INPUT name=\"password2\" class=\"textbox=\" value=\"\" type=\"password\"></td>
				</tr>
			</table>
			<div style='text-align: center; margin: 0.5em;'><INPUT type=\"submit\"class=\"button\" name=\"submit\" value=\"submit\"></div>
			</div></form>
			";
		}
		break;

	case "8":
		$output .= "<div id='pagetitle'>" . _AUTHORFIELDS . "</div>";
		if (isset($_GET['install']))
		{
			if ($_GET['install'] == "automatic")
			{
				$fields[] = array('4', 'lj', 'Live Journal', 'http://{info}.livejournal.com', '', '', '0');
				$fields[] = array('1', 'website', 'Web Site', '', '', '', '1');
				$fields[] = array('5', 'AOL', 'AIM', '', '$output .= "<div><label for=\'AOL\'>".$field[\'field_title\'].":</label><INPUT type=\'text\' class=\'textbox\'  name=\'af_".$field[\'field_name\']."\' maxlength=\'40\' value=\'".(!empty($user[\'af_\'.$field[\'field_id\']]) ? $user[\'af_\'.$field[\'field_id\']] : "")."\' size=\'20\'></div>";', '$thisfield = "<img src=\"http://big.oscar.aol.com/".$field[\'info\']."?on_url=$url/images/aim.gif&off_url=$url/images/aim.gif\"> <a href=\"aim:goim?{aol}ScreenName=".$field[\'info\']."\">".format_email($field[\'info\'])."</a>";', '1');
				$fields[] = array('5', 'ICQ', 'ICQ', '', '$output .= "<div><label for=\'AOL\'>".$field[\'field_title\'].":</label><INPUT type=\'text\' class=\'textbox\'  name=\'af_".$field[\'field_name\']."\' maxlength=\'40\' value=\'".(!empty($user[\'af_\'.$field[\'field_id\']]) ? $user[\'af_\'.$field[\'field_id\']] : "")."\' size=\'20\'></div>";', '$thisfield = "<img src=\"http://status.icq.com/online.gif?icq=".$field[\'info\']."&img=5\"> ".$field[\'info\'];', '1');
				$fields[] = array('5', 'MSN', 'MSN IM', '', '$output .= "<div><label for=\'AOL\'>".$field[\'field_title\'].":</label><INPUT type=\'text\' class=\'textbox\'  name=\'af_".$field[\'field_name\']."\' maxlength=\'40\' value=\'".(!empty($user[\'af_\'.$field[\'field_id\']]) ? $user[\'af_\'.$field[\'field_id\']] : "")."\' size=\'20\'></div>";', '$thisfield = "<img src=\"images/msntalk.gif\" alt=\""._MSN."\"> ".format_email($field[\'info\']);', '1');
				$fields[] = array('5', 'Yahoo', 'Yahoo IM', '', '$output .= "<div><label for=\'AOL\'>".$field[\'field_title\'].":</label><INPUT type=\'text\' class=\'textbox\'  name=\'af_".$field[\'field_name\']."\' maxlength=\'40\' value=\'".(!empty($user[\'af_\'.$field[\'field_id\']]) ? $user[\'af_\'.$field[\'field_id\']] : "")."\' size=\'20\'></div>";', '$thisfield = "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=".$field[\'info\']."&.src=pg\"><img border=\'0\' src=\"http://opi.yahoo.com/online?u=".$field[\'info\']."&m=g&t=1\"> ".format_email($field[\'info\'])."</a>";', '1');
				$fields[] = array('4', 'da', 'Deviant Art', 'http://{info}.deviantart.com', '', '', '0');
				$fields[] = array('3', 'betareader', 'Beta-reader', '', '', '', '1');
				$fields[] = array('4', 'dj', 'DeadJournal', 'http://{info}.deadjournal.com/', '', '', '0');
				$fields[] = array('4', 'xanga', 'Xanga', 'http://www.xanga.com/{info}', '', '', '0');
				$fields[] = array('2', 'gender', 'Gender', 'male|#|female|#|undisclosed', '', '', 0);
				$fields[] = array('4', 'myspace', 'MySpace', 'http://www.myspace.com/{info}', '', '', '0');

				$output .= "<table class='tblborder' style='margin: 1em auto; padding: 1em;' cellpadding='5'><tr><th>" . _FIELD . "</th><th>" . _RESULT . "</th></tr>";
				foreach ($fields as $field)
				{
					$f = dbquery("INSERT INTO " . $tableprefix . "fanfiction_authorfields (field_type, field_name, field_title, field_options, field_code_in, field_code_out, field_on) VALUES('" . $field[0] . "', '" . $field[1] . "','" . $field[2] . "','" . escapestring($field[3]) . "','" . escapestring($field[4]) . "','" . escapestring($field[5]) . "','" . $field[6] . "');");
					$output .= "<tr><td>" . $field[2] . "</td><td align='center'>" . ($f ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				}
				$output .= "</table>";
				$output .= write_message(_FIELDAUTOFAIL . "<br /><br /> <a href='install.php?step=9'>" . _CONTINUE . "</a>");
			}
			else
			{
				$output .= write_message(_FIELDMANUAL . "<br /><br /><a href='install.php?step=9'>" . _CONTINUE . "</a>");
			}
		}
		else $output .= write_message(_FIELDDATAINFO . "<br /><br /><a href='install.php?step=8&amp;install=automatic'>" . _AUTO . "</a> " . _OR . " <a href='install.php?step=8&amp;install=manual'>" . _MANUAL2 . "</a>");
		break;
	case "7":
		$output .= "<div id='pagetitle'>" . _MESSAGEDATA . "</div>";
		if (isset($_GET['install']))
		{
			if ($_GET['install'] == "automatic")
			{
				$output .= "<table class='tblborder' style='margin: 1em auto; padding: 1em;' cellpadding='5'><tr><th>" . _MESSAGE . "</th><th>" . _RESULT . "</th></tr>";
				$msg = dbquery("INSERT INTO " . $tableprefix . "fanfiction_messages (message_name, message_title, message_text) VALUES ('welcome', '', 'This is your welcome message. It appears on the index page. Include it in your .tpl files with {welcome}.');");
				$output .= "<tr><td>Welcome</td><td align='center'>" . ($msg ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$msg = dbquery("INSERT INTO " . $tableprefix . "fanfiction_messages (message_name, message_title, message_text) VALUES ('copyright', '', 'This is your sample copyright footer.  Include it in your footer.tpl with <b>{footer}</b><br />\r\n<u>Disclaimer:</u>  All publicly recognizable characters, settings, etc. are the property of their respective owners.  The original characters and plot are the property of the author.  No money is being made from this work.  No copyright infringement is intended.\r\n');");
				$output .= "<tr><td>Copyright</td><td align='center'>" . ($msg ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$msg = dbquery("INSERT INTO " . $tableprefix . "fanfiction_messages (message_name, message_title, message_text) VALUES ('help', 'Help', '<h3>FAQ</h3><p><strong>I forgot my password!&nbsp; What do I do now?</strong></p><p>To recover a lost password, <a href=\"user.php?action=lostpassword\">click here</a> and enter the e-mail address with which you registered.&nbsp; Your password will be sent to you shortly.</p><p><strong>What kinds of stories are allowed?</strong></p><p>See our <a href=\"submission.php\">Submission Rules.</a></p><p><strong>How do I contact the site administrators?</strong></p><p>You can e-mail us via our <a href=\"contact.php\">contact form.</a></p><p><strong>How do I submit stories?</strong></p><p>If you have not already done so, please <a href=\"user.php?action=newaccount\">register for an account</a>. Once you\'ve logged in, click on <a href=\"user.php\">Account Information</a> and choose <a href=\"stories.php?action=newstory\">Add Story</a>.&nbsp; The form presented there will allow you to submit your story.</p><p><strong>What are the ratings used on the site?</strong></p><p>We use the ratings system from <a href=\"http://www.fictionratings.com/\">www.fictionratings.com</a>.</p><p><strong>What are the story classifications?</strong></p><p>Stories are classified by categories, genres, and warnings.</p>');");
				$output .= "<tr><td>Help</td><td align='center'>" . ($msg ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$msg = dbquery("INSERT INTO " . $tableprefix . "fanfiction_messages (message_name, message_title, message_text) VALUES ('nothankyou', 'Submission Rejection', 'Your recent submission of \"{storytitle} : {chaptertitle}\" to {sitename} did not meet our requirements for submission.  Please review our {rules}.<br /><br />\r\n\r\n{adminname}');");
				$output .= "<tr><td>Rejection Letter</td><td align='center'>" . ($msg ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$msg = dbquery("INSERT INTO " . $tableprefix . "fanfiction_messages (message_name, message_title, message_text) VALUES ('printercopyright', '', '<u>Disclaimer:</u> All publicly recognizable characters and settings are the property of their respective owners. The original characters and plot are the property of the author. No money is being made from this work. No copyright infringement is intended.');");
				$output .= "<tr><td>Printable Copyright</td><td align='center'>" . ($msg ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$msg = dbquery("INSERT INTO " . $tableprefix . "fanfiction_messages (message_name, message_title, message_text) VALUES ('rules', 'Submission Rules', '<p align=\"center\"><strong>Submission Rules</strong></p>\r\n<ol>\r\n  <li>All submissions must be accompanied by a complete disclaimer. If a \r\n  suitable disclaimer is not included, the site administrators reserve the right \r\n  to add a disclaimer.&nbsp; Repeat offenders may be subject to further action \r\n  up to and including removal of stories and account.\r\n<div class=\"tblborder\" style=\"width: 400px; margin: 1em auto;\">\r\n<div style=\"background: #000; color: #FFF; padding: 5px; text-align: center; font-weight: bold;\">Sample Disclaimer</div>\r\n<div class=\"tblborder\" style=\"padding: 5px;\"><span style=\"text-decoration: underline;\">Disclaimer:</span>  All publicly recognizable characters, settings, etc. are the property of their respective owners.  The original characters and plot are the property of the author.&nbsp; \r\n        The author is in no way associated with the owners, creators, or producers of any media franchise.&nbsp;  No copyright infringement is intended.</div>\r\n  </div>\r\n  </li>\r\n  <li>Stories must be submitted to the proper category. &nbsp;If there is an appropriate sub-category, <strong>DO NOT</strong> add your story to the main category.&nbsp; The submission \r\n  form allows you to choose multiple categories for your story, and we worked very hard to add that functionality for you.&nbsp;&nbsp; <u><strong>So please \r\n  do NOT add your story multiple times!</strong></u></li>\r\n  <li>Titles and summaries must be kid friendly.&nbsp; No exceptions.&nbsp; </li>\r\n  <li>&quot;Please read&quot;, &quot;Untitled&quot;, etc. are not acceptable titles or summaries.</li>\r\n  <li>A number of authors have requested that fans refrain from writing fan \r\n  fiction based on their work.&nbsp; Therefore submissions will not be \r\n  accepted based on the works of P.N. Elrod, Raymond Feist, Terry Goodkind, \r\n  Laurell K. Hamilton, Anne McCaffrey, Robin McKinley, Irene Radford, Anne Rice, \r\n  and Nora Roberts/J.D. Robb.&nbsp; </li>\r\n  <li>Actor/actress stories are not permitted...not even if they\'re visiting an \r\n  alternate reality.</li>\r\n  <li>Correct grammar and spelling are expected of all stories submitted to this \r\n  site.&nbsp; The site administrators are not grammar Nazis.&nbsp; However, the \r\n  site administrators reserve the right to request corrections in submissions \r\n  with a multitude of grammar and/or spelling errors.&nbsp; If such a request is \r\n  ignored, the story will be deleted.</li>\r\n  <li>All stories must be rated correctly and have the appropriate warnings.&nbsp; \r\n  All adult rated stories are expected to have warnings.&nbsp; After all, they \r\n  wouldn\'t have that rating if there wasn\'t something to be warned about!&nbsp; The site administrators recognize \r\n  that there is an audience for these stories, but please respect those who do \r\n  not wish to read them by labeling them appropriately.&nbsp;\r\n  <u><strong>Please note: Stories containing adults having sex with minors are strictly forbidden.</strong></u>&nbsp; </li>\r\n  <li>Stories with multiple chapters should be archived as such and <span style=\"font-weight: bold; text-decoration: underline;\">NEVER</span> as \r\n  separate stories.&nbsp; Upload the first chapter of your story, then go to <a href=\"stories.php?action=viewstories\">Manage Stories</a> in \r\nyour account to add additional chapters.  If you have trouble with this, please contact the site administrator or ask a \r\n  friend to help you.</li>\r\n  <li>As much as possible, spoiler warnings are expected on all stories.  For categories with serialized content, such as series of books or television series, \r\n  spoilers are <strong>mandatory</strong> for the current season and/or most recent part.  An appropriate spoiler warning to place in your summary would be: Spoilers for <u>Star Trek II: The Wrath of Khan.</u> \r\n  <strong>DO NOT</strong> do anything like this: <u>Spoilers for the one where Spock dies.</u></li>\r\n</ol>\r\n  <p>Submissions found to be in violation of these rules may be removed and the \r\n  author\'s account suspended at the discretion of the site administrators and/or \r\n  moderators.&nbsp; The site administrators reserve the right to modify these \r\n  rules as needed.</p>');");
				$output .= "<tr><td>Submission Rules</td><td align='center'>" . ($msg ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$msg = dbquery("INSERT INTO " . $tableprefix . "fanfiction_messages (message_name, message_title, message_text) VALUES ('thankyou', '{sitename} Submission Acceptance', 'Thanks for your submission.');");
				$output .= "<tr><td>Acceptance Letter</td><td align='center'>" . ($msg ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$msg = dbquery("INSERT INTO " . $tableprefix . "fanfiction_messages (message_name, message_title, message_text) VALUES ('tos', 'Terms of Service', 'This is the Terms of Service for your site.  It will be displayed when a new member registers to the site.');");
				$output .= "<tr><td>Terms of Service</td><td align='center'>" . ($msg ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$msg = dbquery("INSERT INTO " . $tableprefix . "fanfiction_messages (message_name, message_title, message_text) VALUES ('maintenance', 'Site Maintenance', '<p style=\"text-align: center;\">This site is currently undergoing maintenance.  Please check back soon.  Thank you.</p>');");
				$output .= "<tr><td>Site Maintenance</td><td align='center'>" . ($msg ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$output .= "</table>";
				$output .= write_message(_MESSAGEAUTOFAILUPGRADE . "<br /><br /> <a href='install.php?step=8'>" . _CONTINUE . "</a>");
			}
			else
			{
				$output .= write_message(_MESSAGEMANUAL . "<br /><br /><a href='install.php?step=8'>" . _CONTINUE . "</a>");
			}
		}
		else $output .= write_message(_MESSAGEDATANEW . "<br /><br /><a href='install.php?step=7&amp;install=automatic'>" . _AUTO . "</a> " . _OR . " <a href='install.php?step=7&amp;install=manual'>" . _MANUAL2 . "</a>");

		break;
	case "6":
		$output .= "<div id='pagetitle'>" . _BLOCKDATA . "</div>";
		if (isset($_GET['install']))
		{
			if ($_GET['install'] == "automatic")
			{
				$blocklist = array(
					array("categories", "Categories", "categories/categories.php", "1", ""),
					array("featured", "Featured Stories", "featured/featured.php", "1", ""),
					array("info", "Site Info", "info/info.php", "2", ""),
					array("login", "Log In", "login/login.php", "1", ""),
					array("menu", "Main Menu", "menu/menu.php", "1", ""),
					array("random", "Random Story", "random/random.php", "2", ""),
					array("recent", "Most Recent", "recent/recent.php", "2", "a:1:{s:3:\"num\";s:1:\"1\";}"),
					array("skinchange", "Skin Change", "skinchange/skinchange.php", "1", ""),
					array("news", "Site News", "news/news.php", "1", "a:1:{s:3:\"num\";s:1:\"1\";}")
				);
				$output .= "<table class='tblborder' style='margin: 1em auto; padding: 1em;' cellpadding='5'><tr><th>" . _BLOCK . "</th><th>" . _RESULT . "</th></tr>";
				foreach ($blocklist as $block)
				{
					$b = dbquery("INSERT INTO " . $tableprefix . "fanfiction_blocks (block_name, block_title, block_file, block_status, block_variables) VALUES('" . $block[0] . "', '" . $block[1] . "', '" . $block[2] . "', '" . $block[3] . "', '" . escapestring($block[4]) . "');");
					$output .= "<tr><td>" . $block[1] . "</td><td align='center'>" . ($b ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				}
				$output .= "</table>";
				$output .= write_message(_BLOCKDATAFAILUPGRADE . "<br /><br /><a href='install.php?step=7'>" . _CONTINUE . "</a>");
			}
			else
			{
				$output .= write_message(_BLOCKDATAMANUAL . "<br /><br /><a href='install.php?step=6'>" . _CONTINUE . "</a>");
			}
		}
		else $output .= write_message(_BLOCKDATANEW . "<br /><br /><a href='install.php?step=6&amp;install=automatic'>" . _AUTO . "</a> " . _OR . " <a href='install.php?step=6&amp;install=manual'>" . _MANUAL2 . "</a>");
		break;
	case "5":
		$output .= "<div id='pagetitle'>" . _LINKDATA . "</div>";
		if (isset($_GET['install']))
		{
			if ($_GET['install'] == "automatic")
			{
				$pagelist = array(
					array("home", "Home", "index.php", "0", "0"),
					array("recent", "Most Recent", "browse.php?type=recent", "0", "0"),
					array("login", "Login", "user.php?action=login", "0", "0"),
					array("adminarea", "Admin", "admin.php", "0", "2"),
					array("logout", "Logout", "user.php?action=logout", "0", "1"),
					array("featured", "Featured Stories", "browse.php?type=featured", "0", "0"),
					array("catslink", "Categories", "browse.php?type=categories", "0", "0"),
					array("members", "Members", "authors.php?action=list", "0", "0"),
					array("authors", "Authors", "authors.php?list=authors", "0", "0"),
					array("help", "Help", "viewpage.php?page=help", "0", "0"),
					array("search", "Search", "search.php", "0", "0"),
					array("series", "Series", "browse.php?type=series", "0", "0"),
					array("tens", "Top Tens", "toplists.php", "0", "0"),
					array("challenges", "Challenges", "modules/challenges/challenges.php", "0", "0"),
					array("contactus", "Contact Us", "contact.php", "0", "0"),
					array("rules", "Rules", "viewpage.php?page=rules", "0", "0"),
					array("tos", "Terms of Service", "viewpage.php?page=tos", "0", "0"),
					array("rss", "<img src=\'images/xml.gif\' alt=\'RSS\' border=\'0\'>", "rss.php", "0", "0"),
					array("login", "Account Info", "user.php", "0", "1"),
					array("titles", "Titles", "browse.php?type=titles", "0", "0"),
					array("register", "Register", "user.php?action=register", "0", "0"),
					array("lostpassword", "Lost Password", "user.php?action=lostpassword", "0", "0"),
					array("newsarchive", "News Archive", "news.php", "0", "0"),
					array("browse", "Browse", "browse.php", "0", "0"),
					array("charslink", "Characters", "browse.php?type=characters", "0", "0"),
					array("ratings", "Ratings", "browse.php?type=ratings", "0", "0"),
				);
				$output .= "<table class='tblborder' style='margin: 1em auto; padding: 1em;' cellpadding='5'><tr><th>" . _PAGE . "</th><th>" . _RESULT . "</th></tr>";
				foreach ($pagelist as $page)
				{
					$pages = dbquery("INSERT INTO " . $tableprefix . "fanfiction_pagelinks (link_name, link_text, link_url, link_target, link_access) VALUES ('" . $page[0] . "', '" . $page[1] . "', '" . $page[2] . "', '" . $page[3] . "', '" . $page[4] . "');");
					$output .= "<tr><td>" . stripslashes($page[1]) . "</td><td align='center'>" . ($pages ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				}

				$output .= "</table>";
				$output .= write_message(_LINKAUTOFAIL . "<br /><br /> <a href='install.php?step=6'>" . _CONTINUE . "</a>");
			}
			else
			{
				$output .= write_message(_LINKMANUAL . "<br /><br /><a href='install.php?step=5'>" . _CONTINUE . "</a>");
			}
		}
		else $output .= write_message(_LINKDATAINFO . "<br /><br />" . _LINKSETUP . " <a href='install.php?step=5&amp;install=automatic'>" . _AUTO . "</a> " . _OR . " <a href='install.php?step=5&amp;install=manual'>" . _MANUAL2 . "</a>");
		break;
	case "4":
		$output .= "<div id='pagetitle'>" . _PANELDATA . "</div>";
		if (isset($_GET['install']))
		{
			if ($_GET['install'] == "automatic")
			{

				$panellist = array(
					array("submitted", "Submissions", "", "3", "5", "0", "A"),
					array("versioncheck", "Version Check", "", "3", "7", "0", "A"),
					array("newstory", "Add New Story", "stories.php?action=newstory&admin=1", "3", "3", "0", "A"),
					array("addseries", "Add New Series", "series.php?action=add", "3", "3", "0", "A"),
					array("news", "News", "", "3", "5", "0", "A"),
					array("featured", "Featured Stories", "", "3", "5", "0", "A"),
					array("characters", "Characters", "", "2", "2", "0", "A"),
					array("ratings", "Ratings", "", "2", "3", "0", "A"),
					array("members", "Members", "", "2", "5", "0", "A"),
					array("mailusers", "Mail Users", "", "2", "6", "0", "A"),
					array("settings", "Settings", "", "1", "2", "0", "A"),
					array("blocks", "Blocks", "", "1", "3", "0", "A"),
					array("censor", "Censor", "", "1", "0", "1", "A"),
					array("admins", "Admins", "", "1", "6", "0", "A"),
					array("classifications", "Classifications", "", "2", "4", "0", "A"),
					array("categories", "Categories", "", "2", "1", "0", "A"),
					array("custpages", "Custom Pages", "", "1", "4", "0", "A"),
					array("validate", "Validate Submission", "", "3", "0", "1", "A"),
					array("yesletter", "Validation Letter", "", "3", "0", "1", "A"),
					array("noletter", "Rejection Letter", "", "3", "0", "1", "A"),
					array("links", "Page Links", "", "1", "5", "0", "A"),
					array("messages", "Message Settings", "", "2", "0", "1", "A"),
					array("login", "Login", "", "0", "0", "1", "U"),
					array("logout", "Logout", "", "1", "5", "0", "U"),
					array("revreceived", "Reviews Received", "", "1", "0", "1", "U"),
					array("editprefs", "Edit Preferences", "", "1", "2", "0", "U"),
					array("lostpassword", "Lost Password", "", "0", "0", "1", "U"),
					array("editbio", "Edit Bio", "", "1", "1", "0", "U"),
					array("register", "Register", "", "0", "0", "1", "U"),
					array("manageimages", "Manage Images", "", "1", "5", "0", "S"),
					array("revres", "Review Response", "", "1", "0", "1", "U"),
					array("stats", "View Your Statistics", "", "1", "3", "0", "U"),
					array("newstory", "Add New Story", "stories.php?action=newstory", "1", "1", "0", "S"),
					array("newseries", "Add New Series", "series.php?action=add", "1", "3", "0", "S"),
					array("managestories", "Manage Stories", "stories.php?action=viewstories", "1", "2", "0", "S"),
					array("manageseries", "Manage Series", "series.php?action=manage", "1", "4", "0", "S"),
					array("reviewsby", "Your Reviews", "", "1", "0", "1", "U"),
					array("storiesby", "Stories by {author}", "", "0", "1", "0", "P"),
					array("seriesby", "Series by {author}", "", "0", "2", "0", "P"),
					array("reviewsby", "Reviews by {author}", "", "0", "3", "0", "P"),
					array("categories", "Categories", "", "0", "1", "0", "B"),
					array("characters", "Characters", "", "0", "2", "0", "B"),
					array("ratings", "Ratings", "", "0", "3", "0", "B"),
					array("titles", "Titles", "", "0", "5", "0", "B"),
					array("class", "Classes", "", "0", "0", "1", "B"),
					array("recent", "Most Recent", "", "0", "0", "1", "B"),
					array("featured", "Featured Stories", "", "0", "0", "1", "B"),
					array("panels", "Panels", "", "1", "1", "0", "A"),
					array("phpinfo", "PHP Info", "", "1", "7", "0", "A"),
					array("contact", "Contact", "", "0", "0", "1", "P"),
					array("series", "Series", "", "0", "4", "0", "B"),
					array("viewlog", "Action Log", "", "1", "8", "0", "A"),
					array("shortstories", "10 Shortest Stories", "toplists/default.php", "0", "6", "0", "L"),
					array("longstories", "10 Longest Stories", "toplists/default.php", "0", "5", "0", "L"),
					array("largeseries", "10 Largest Series", "toplists/default.php", "0", "1", "0", "L"),
					array("smallseries", "10 Smallest Series", "toplists/default.php", "0", "2", "0", "L"),
					array("reviewedseries", "10 Most Reviewed Series", "toplists/default.php", "0", "4", "0", "L"),
					array("prolificauthors", "10 Most Prolific Authors", "toplists/default.php", "0", "10", "0", "L"),
					array("prolificreviewers", "10 Most Prolific Reviewers", "toplists/default.php", "0", "12", "0", "L"),
					array("reviewedstories", "10 Most Reviewed Stories", "toplists/default.php", "0", "8", "0", "L"),
					array("readstories", "10 Most Read Stories", "toplists/default.php", "0", "9", "0", "L"),
					array("manfavs", "Manage Favorites", "", "1", "2", "0", "F"),
					array("favstories", "10 Most Favorite Stories", "toplists/default.php", "0", "7", "0", "L"),
					array("favauthors", "10 Most Favorite Authors", "toplists/default.php", "0", "11", "0", "L"),
					array("favseries", "10 Most Favorite Series", "toplists/default.php", "0", "3", "0", "L"),
					array("favst", "Favorite Stories", "", "0", "0", "1", "F"),
					array("favse", "Favorite Series", "", "0", "0", "1", "F"),
					array("favau", "Favorite Authors", "", "0", "0", "1", "F"),
					array("favst", "Favorite Stories", "", "0", "0", "1", "U"),
					array("favse", "Favorite Series", "", "0", "0", "1", "U"),
					array("favau", "Favorite Authors", "", "0", "0", "1", "U"),
					array("favlist", "{author}\'s Favorites", "viewuser.php?action=manfavs", "0", "5", "0", "F"),
					array("skins", "Skins", "", "3", "6", "0", "A"),
					array("authorfields", "Profile Information", "", "1", "9", "0", "A"),
					array("maintenance", "Archive Maintenance", "", "1", "10", "0", "A"),
					array("convert", "Archive Conversion", "", "1", "11", "0", "A"),
					array("manual", "Admin Manual", "", "3", "6", "0", "A"),
					array('modules', 'Modules', '', 1, "12", 0, 'A')
				);
				$output .= "<table class='tblborder' style='margin: 1em auto; padding: 1em;' cellpadding='5'><tr><th>Panel</th><th>" . _RESULT . "</th></tr>";
				foreach ($panellist as $panel)
				{
					unset($panels);
					$panels = dbquery("INSERT INTO " . $tableprefix . "fanfiction_panels (panel_name, panel_title, panel_url, panel_level, panel_order, panel_hidden, panel_type) VALUES ('" . $panel[0] . "', '" . $panel[1] . "', '" . $panel[2] . "', '" . $panel[3] . "', '" . $panel[4] . "', '" . $panel[5] . "', '" . $panel[6] . "');");
					$output .= "<tr><td>" . stripslashes($panel[1]) . "</td><td align='center'>" . ($panels ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				}
				$output .= "</table>";
				$output .= write_message(_PANELAUTOFAIL . "<br /><br /><a href='install.php?step=5'>" . _CONTINUE . "</a>");
			}
			else
			{
				$output .= write_message(_PANELMANUAL . "<br /><br /><a href='install.php?step=5'>" . _CONTINUE . "</a>");
			}
		}
		else $output .= write_message(_PANELDATAINFO . "<br /><br />" . _PANELSETUP . " <a href='install.php?step=4&amp;install=automatic'>" . _AUTO . "</a> " . _OR . " <a href='install.php?step=4&amp;install=manual'>" . _MANUAL2 . "</a>");
		break;
	case "3":
		$output .= "<div id='pagetitle'>" . _INSTALLTABLES . "</div>";
		if (isset($_GET['install']))
		{
			if ($_GET['install'] == "automatic")
			{
				$output .= "<table class='tblborder' style='margin: 1em auto; padding: 1em;' cellpadding='5'><tr><th>Table</th><th>" . _RESULT . "</th></tr>";
				$authorfields = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_authorfields (
  field_id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  field_type SMALLINT NOT NULL default '0',
  field_name VARCHAR(30) NOT NULL default ' ',
  field_title VARCHAR(255) NOT NULL default ' ',
  field_options TEXT,
  field_code_in TEXT,
  field_code_out TEXT,
  field_on SMALLINT NOT NULL default '0',
  PRIMARY KEY  (field_id)
);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_authorfields</td><td align='center'>" . ($authorfields ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$authorinfo = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_authorinfo (
  uid INTEGER NOT NULL default '0',
  field INTEGER NOT NULL default '0',
  info VARCHAR(255) NOT NULL default ' ',
  PRIMARY KEY  (uid,field)
);
				CREATE INDEX IF NOT EXISTS uid ON " . $tableprefix . "fanfiction_authorinfo (uid);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_authorinfo</td><td align='center'>" . ($authorinfo ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$authorprefs = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_authorprefs (
  uid INTEGER NOT NULL default '0',
  newreviews SMALLINT NOT NULL default '0',
  newrespond SMALLINT NOT NULL default '0',
  ageconsent SMALLINT NOT NULL default '0',
  alertson SMALLINT NOT NULL default '0',
  tinymce SMALLINT NOT NULL default '0',
  sortby SMALLINT NOT NULL default '0',
  storyindex SMALLINT NOT NULL default '0',
  validated SMALLINT NOT NULL default '0',
  userskin VARCHAR(60) NOT NULL default 'default',
  level SMALLINT NOT NULL default '0',
  categories VARCHAR(200) NOT NULL default '0',
  contact SMALLINT NOT NULL default '0',
  stories INTEGER NOT NULL default '0',
  PRIMARY KEY  (uid)
);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_authorprefs</td><td align='center'>" . ($authorprefs ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$authors = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_authors (
  uid INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  penname VARCHAR(200) NOT NULL default '',
  realname VARCHAR(200) NOT NULL default '',
  email VARCHAR(200) NOT NULL default '',
  bio TEXT NULL,
  image VARCHAR(200) NOT NULL default '',
  date CHAR(10) NOT NULL default '0',
  admincreated SMALLINT NOT NULL default '0',
  password VARCHAR(255) NOT NULL default '0',
  PRIMARY KEY  (uid)
);
				CREATE INDEX IF NOT EXISTS penname ON " . $tableprefix . "fanfiction_authors (penname);
				CREATE INDEX IF NOT EXISTS admincreated ON " . $tableprefix . "fanfiction_authors (admincreated);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_authors</td><td align='center'>" . ($authors ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$blocks = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_blocks (
  block_id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  block_name VARCHAR(30) NOT NULL default '',
  block_title VARCHAR(150) NOT NULL default '',
  block_file VARCHAR(200) NOT NULL default '',
  block_status SMALLINT NOT NULL default '0',
  block_variables TEXT NOT NULL,
  PRIMARY KEY  (block_id)
);
				CREATE INDEX IF NOT EXISTS block_name ON " . $tableprefix . "fanfiction_blocks (block_name);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_blocks</td><td align='center'>" . ($blocks ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$categories = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_categories (
  catid INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  parentcatid INTEGER NOT NULL default '-1',
  category VARCHAR(60) NOT NULL default '',
  description TEXT NOT NULL,
  image VARCHAR(100) NOT NULL default '',
  locked SMALLINT NOT NULL default '0',
  leveldown SMALLINT NOT NULL default '0',
  displayorder INTEGER NOT NULL default '0',
  numitems INTEGER NOT NULL default '0',
  PRIMARY KEY  (catid)
);
				CREATE INDEX IF NOT EXISTS byparent ON " . $tableprefix . "fanfiction_categories (parentcatid,displayorder);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_categories</td><td align='center'>" . ($categories ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$chapters = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_chapters (
  chapid INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  title VARCHAR(250) NOT NULL default '',
  inorder INTEGER NOT NULL default '0',
  notes TEXT NULL,
  storytext TEXT NULL,
  endnotes TEXT NULL,
  validated SMALLINT NOT NULL default '0',
  wordcount INTEGER NOT NULL default '0',
  rating SMALLINT NOT NULL default '0',
  reviews INTEGER NOT NULL default '0',
  sid INTEGER NOT NULL default '0',
  uid INTEGER NOT NULL default '0',
  count INTEGER NOT NULL default '0',
  PRIMARY KEY  (chapid)
);
				CREATE INDEX IF NOT EXISTS sid ON " . $tableprefix . "fanfiction_chapters (sid);
				CREATE INDEX IF NOT EXISTS uid ON " . $tableprefix . "fanfiction_chapters (uid);
				CREATE INDEX IF NOT EXISTS inorder ON " . $tableprefix . "fanfiction_chapters (inorder);
				CREATE INDEX IF NOT EXISTS title ON " . $tableprefix . "fanfiction_chapters (title);
				CREATE INDEX IF NOT EXISTS validated ON " . $tableprefix . "fanfiction_chapters (validated);
				CREATE INDEX IF NOT EXISTS forstoryblock ON " . $tableprefix . "fanfiction_chapters (sid,validated);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_chapters</td><td align='center'>" . ($chapters ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$characters = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_characters (
  charid INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  catid INTEGER NOT NULL default '0',
  charname VARCHAR(60) NOT NULL default '',
  bio TEXT NOT NULL,
  image VARCHAR(200) NOT NULL default '',
  PRIMARY KEY  (charid)
);
				CREATE INDEX IF NOT EXISTS catid ON " . $tableprefix . "fanfiction_characters (catid);
				CREATE INDEX IF NOT EXISTS charname ON " . $tableprefix . "fanfiction_characters (charname);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_characters</td><td align='center'>" . ($characters ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$classes = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_classes (
  class_id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  class_type INTEGER NOT NULL default '0',
  class_name VARCHAR(100) NOT NULL default '',
  PRIMARY KEY  (class_id)
);
				CREATE INDEX IF NOT EXISTS byname ON " . $tableprefix . "fanfiction_classes  (class_type,class_name,class_id)");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_classes</td><td align='center'>" . ($classes ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$classtypes = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_classtypes (
  classtype_id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  classtype_name VARCHAR(50) NOT NULL default '',
  classtype_title VARCHAR(50) NOT NULL default '',
  PRIMARY KEY  (classtype_id)
);
				CREATE UNIQUE INDEX IF NOT EXISTS classtype_name ON " . $tableprefix . "fanfiction_classtypes (classtype_name);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_classtypes</td><td align='center'>" . ($classtypes ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$coauthors = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_coauthors (
  sid INTEGER NOT NULL default '0',
  uid INTEGER NOT NULL default '0',
  PRIMARY KEY  (sid,uid)
);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_coauthors</td><td align='center'>" . ($coauthors ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$codeblocks = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_codeblocks (
  code_id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  code_text TEXT NOT NULL,
  code_type VARCHAR(20) default NULL,
  code_module VARCHAR(60) default NULL,
  PRIMARY KEY  (code_id)
);
				CREATE INDEX IF NOT EXISTS code_type ON " . $tableprefix . "fanfiction_codeblocks (code_type);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_codeblocks</td><td align='center'>" . ($codeblocks ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$comments = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_comments (
  cid INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  nid INTEGER NOT NULL default '0',
  uid INTEGER NOT NULL default '0',
  comment text NOT NULL,
  time CHAR(10) NOT NULL default '0',
  PRIMARY KEY  (cid)
);
				CREATE INDEX IF NOT EXISTS commentlist ON " . $tableprefix . "fanfiction_comments (nid,time);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_comments</td><td align='center'>" . ($comments ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$favorites = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_favorites (
  uid INTEGER NOT NULL default '0',
  item INTEGER NOT NULL default '0',
  type CHAR(2) NOT NULL default '',
  comments TEXT NOT NULL
);
				CREATE UNIQUE INDEX IF NOT EXISTS byitem ON " . $tableprefix . "fanfiction_favorites (item,type,uid);
				CREATE UNIQUE INDEX IF NOT EXISTS byuid ON " . $tableprefix . "fanfiction_favorites (uid,type,item);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_favorites</td><td align='center'>" . ($favorites ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$inseries = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_inseries (
  seriesid INTEGER NOT NULL default '0',
  sid INTEGER NOT NULL default '0',
  subseriesid INTEGER NOT NULL default '0',
  confirmed INTEGER NOT NULL default '0',
  inorder INTEGER NOT NULL default '0',
  PRIMARY KEY  (sid,seriesid,subseriesid)
);
				CREATE INDEX IF NOT EXISTS seriesid ON " . $tableprefix . "fanfiction_inseries (seriesid,inorder);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_inseries</td><td align='center'>" . ($inseries ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$logs = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_log (
  log_id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  log_action VARCHAR(255) default NULL,
  log_uid INTEGER NOT NULL,
  log_ip BYTEA DEFAULT NULL,
  log_timestamp CHAR(10) NOT NULL default '0',
  log_type VARCHAR(2) NOT NULL,
  PRIMARY KEY  (log_id)
);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_log</td><td align='center'>" . ($logs ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$messages = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_messages (
  message_id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  message_name VARCHAR(50) NOT NULL default '',
  message_title VARCHAR(200) NOT NULL default '',
  message_text TEXT NOT NULL,
  PRIMARY KEY  (message_id)
);
				CREATE INDEX IF NOT EXISTS message_name ON " . $tableprefix . "fanfiction_messages (message_name);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_messages</td><td align='center'>" . ($messages ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$modules = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_modules (
  id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  name VARCHAR(100) NOT NULL default 'Test Module',
  version VARCHAR(10) NOT NULL default '1.0',
  PRIMARY KEY  (id)
);
				CREATE INDEX IF NOT EXISTS name_version ON " . $tableprefix . "fanfiction_modules (name,version);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_modules</td><td align='center'>" . ($modules ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$news = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_news (
  nid INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  author VARCHAR(60) NOT NULL default '',
  title VARCHAR(255) NOT NULL default '',
  story TEXT NOT NULL,
  time CHAR(10) NOT NULL default '0',
  comments INT NOT NULL DEFAULT '0',
  PRIMARY KEY  (nid)
);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_news</td><td align='center'>" . ($news ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$pagelinks = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_pagelinks (
  link_id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  link_name VARCHAR(50) NOT NULL default '',
  link_text VARCHAR(100) NOT NULL default '',
  link_key CHAR( 1 ) NULL,
  link_url VARCHAR(250) NOT NULL default '',
  link_target CHAR(1) NOT NULL default '0',
  link_access SMALLINT NOT NULL default '0',
  PRIMARY KEY  (link_id)
);
				CREATE INDEX IF NOT EXISTS link_name ON " . $tableprefix . "fanfiction_pagelinks (link_name);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_pagelinks</td><td align='center'>" . ($pagelinks ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$panels = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_panels (
  panel_id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  panel_name VARCHAR(50) NOT NULL default 'unknown',
  panel_title VARCHAR(100) NOT NULL default 'Unnamed Panel',
  panel_url VARCHAR(100) default NULL,
  panel_level SMALLINT NOT NULL default '3',
  panel_order SMALLINT NOT NULL default '0',
  panel_hidden SMALLINT NOT NULL default '0',
  panel_type VARCHAR(20) NOT NULL default 'A',
  PRIMARY KEY  (panel_id)
);
				CREATE INDEX IF NOT EXISTS panel_type ON " . $tableprefix . "fanfiction_panels (panel_type,panel_name);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_panels</td><td align='center'>" . ($panels ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$ratings = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_ratings (
  rid INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  rating VARCHAR(60) NOT NULL default '',
  ratingwarning SMALLINT NOT NULL default '0',
  warningtext TEXT NOT NULL,
  PRIMARY KEY  (rid)
);
				CREATE INDEX IF NOT EXISTS rating ON " . $tableprefix . "fanfiction_ratings (rating);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_ratings</td><td align='center'>" . ($ratings ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$reviews = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_reviews (
  reviewid INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  item INTEGER NOT NULL default '0',
  chapid INTEGER NOT NULL default '0',
  reviewer VARCHAR(60) NOT NULL default '0',
  uid INTEGER NOT NULL default '0',
  review TEXT NOT NULL,
  date CHAR(10) NOT NULL default '0',
  rating INTEGER NOT NULL default '0',
  respond CHAR(1) NOT NULL default '0',
  type CHAR(2) NOT NULL default '',
  PRIMARY KEY  (reviewid)
);
				CREATE INDEX IF NOT EXISTS chapid ON " . $tableprefix . "fanfiction_reviews (chapid);
				CREATE INDEX IF NOT EXISTS rating ON " . $tableprefix . "fanfiction_reviews (rating);
				CREATE INDEX IF NOT EXISTS respond ON " . $tableprefix . "fanfiction_reviews (respond);
				CREATE INDEX IF NOT EXISTS avgrating ON " . $tableprefix . "fanfiction_reviews (type,item,rating);
				CREATE INDEX IF NOT EXISTS bychapter ON " . $tableprefix . "fanfiction_reviews (chapid,rating);
				CREATE INDEX IF NOT EXISTS byuid ON " . $tableprefix . "fanfiction_reviews (uid,item,type);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_reviews</td><td align='center'>" . ($reviews ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$series = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_series (
  seriesid INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  title VARCHAR(200) NOT NULL default '',
  summary TEXT NOT NULL,
  uid INTEGER NOT NULL default '0',
  isopen SMALLINT NOT NULL default '0',
  catid VARCHAR(200) NOT NULL default '0',
  rating SMALLINT NOT NULL default '0',
  classes VARCHAR(200) NOT NULL default '',
  characters VARCHAR(250) NOT NULL default '',
  reviews INTEGER NOT NULL default '0',
  numstories INTEGER NOT NULL DEFAULT '0',
  challenges VARCHAR(200) NOT NULL default '',
  PRIMARY KEY  (seriesid)
);
				CREATE INDEX IF NOT EXISTS catid ON " . $tableprefix . "fanfiction_series (catid);
				CREATE INDEX IF NOT EXISTS characters ON " . $tableprefix . "fanfiction_series (characters);
				CREATE INDEX IF NOT EXISTS classes ON " . $tableprefix . "fanfiction_series (classes);
				CREATE INDEX IF NOT EXISTS owner ON " . $tableprefix . "fanfiction_series (uid,title);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_series</td><td align='center'>" . ($series ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$stats = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_stats (
  sitekey VARCHAR(50) NOT NULL default '0',
  stories INTEGER NOT NULL default '0',
  chapters INTEGER NOT NULL default '0',
  series INTEGER NOT NULL default '0',
  reviews INTEGER NOT NULL default '0',
  wordcount INTEGER NOT NULL default '0',
  authors INTEGER NOT NULL default '0',
  members INTEGER NOT NULL default '0',
  reviewers INTEGER NOT NULL default '0',
  newestmember INTEGER NOT NULL default '0'
);");
				dbquery("INSERT INTO " . $tableprefix . "fanfiction_stats(sitekey, newestmember) VALUES('" . SITEKEY . "', '1')");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_stats</td><td align='center'>" . ($stats ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$stories = dbquery("CREATE TABLE IF NOT EXISTS " . $tableprefix . "fanfiction_stories (
  sid INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
  title VARCHAR(200) NOT NULL default 'Untitled',
  summary TEXT NULL,
  storynotes TEXT NULL,
  catid VARCHAR(100) NOT NULL default '0',
  classes VARCHAR(200) NOT NULL default '0',
  charid VARCHAR(250) NOT NULL default '0',
  rid VARCHAR(25) NOT NULL default '0',
  date CHAR(10) NOT NULL default '0',
  updated CHAR(10) NOT NULL default '0',
  uid INTEGER NOT NULL default '0',
  coauthors SMALLINT NOT NULL default '0',
  featured SMALLINT NOT NULL default '0',
  validated SMALLINT NOT NULL default '0',
  completed SMALLINT NOT NULL default '0',
  rr SMALLINT NOT NULL default '0',
  wordcount INTEGER NOT NULL default '0',
  rating SMALLINT NOT NULL default '0',
  reviews INTEGER NOT NULL default '0',
  count INTEGER NOT NULL default '0',
  PRIMARY KEY  (sid)
);
				CREATE INDEX IF NOT EXISTS title ON " . $tableprefix . "fanfiction_stories (title);
				CREATE INDEX IF NOT EXISTS catid ON " . $tableprefix . "fanfiction_stories (catid);
				CREATE INDEX IF NOT EXISTS charid ON " . $tableprefix . "fanfiction_stories (charid);
				CREATE INDEX IF NOT EXISTS rid ON " . $tableprefix . "fanfiction_stories (rid);
				CREATE INDEX IF NOT EXISTS uid ON " . $tableprefix . "fanfiction_stories (uid);
				CREATE INDEX IF NOT EXISTS featured ON " . $tableprefix . "fanfiction_stories (featured);
				CREATE INDEX IF NOT EXISTS completed ON " . $tableprefix . "fanfiction_stories (completed);
				CREATE INDEX IF NOT EXISTS rr ON " . $tableprefix . "fanfiction_stories (rr);
				CREATE INDEX IF NOT EXISTS validateduid ON " . $tableprefix . "fanfiction_stories (validated,uid);
				CREATE INDEX IF NOT EXISTS recent ON " . $tableprefix . "fanfiction_stories (updated,validated);");
				$output .= "<tr><td>" . $tableprefix . "fanfiction_stories</td><td align='center'>" . ($stories ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") . "</td></tr>";
				$output .= "</table>";
				$output .= write_message(_TABLEFAILED . "<br /><br /><a href='install.php?step=4'>" . _CONTINUE . "</a>");
			}
			else if ($_GET['install'] == "manual")
			{
				$output .= write_message(_TABLESMANUAL . "<br /><br /><a href='install.php?step=3'>" . _CONTINUE . "</a>");
			}
		}
		else
		{

			$output .= write_message(_TABLESINSTALL . "<br /><br /><a href='install.php?step=3&amp;install=automatic'>" . _AUTO . "</a> " . _OR . " <a href='install.php?step=3&amp;install=manual'>" . _MANUAL2 . "</a>");
		}

		break;

	case "2";
		include("../config.php");
		//if (dbnumrows(dbquery("SHOW TABLES LIKE " . $settingsprefix . "fanfiction_settings")))
		//if (dbnumrows(dbquery("SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = '$dbschema' AND table_name = '" . $settingsprefix . "fanfiction_settings') AS table_existence;")))
		if (dbnumrows(dbquery("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname != 'pg_catalog' AND schemaname != 'information_schema' AND tablename like 'fanfiction_settings';")))
		{
			$dbconnect = dbconnect($dbhost, $dbuser, $dbpass, $dbname);
			$settings = dbquery("SELECT tableprefix, language FROM " . $settingsprefix . "fanfiction_settings WHERE sitekey = '" . $sitekey . "'");
			list($tableprefix, $language) = dbrow($settings);

			if ($tableprefix == '') $tableprefix = $settingsprefix;

			define("TABLEPREFIX", $tableprefix);
			define("SITEKEY", $sitekey);

			$sect = isset($_GET['sect']) ? $_GET['sect'] : "main";
			$settingsresults = dbquery("SELECT * FROM " . $settingsprefix . "fanfiction_settings WHERE sitekey = '" . SITEKEY . "'");
			if (dbnumrows($settingsresults) == 0)
			{
				dbquery("INSERT INTO " . $settingsprefix . "fanfiction_settings (sitekey) VALUES('" . SITEKEY . "');");
				$settingsresults = dbquery("SELECT * FROM " . $settingsprefix . "fanfiction_settings WHERE sitekey = '" . SITEKEY . "'");
			}
			$settings = dbassoc($settingsresults);
			foreach ($settings as $var => $val)
			{
				$$var = $val;
			}
			if ($sect == "submissions")
			{
				if (isset($_POST['submit']))
				{
					$storiespath = descript($_POST['newstoriespath']);
					if (!file_exists($storiespath) && !file_exists("../" . $storiespath))
					{
						if (!strrchr($storiespath, "/") && !strrchr($storiespath, "\\")) $storiespath = "../$storiespath";
						mkdir("$storiespath", 0755);
						@chmod("$storiespath", 0777);
						if (substr(sprintf('%o', @fileperms("../" . $storiespath)), -4) != "0777") $output .= write_message(_STORIESPATHNOTWRITABLE);
					}
				}
			}
			if ($sect == "email" && isset($_POST['submit']))
			{
				$output .= "<div id=\"pagetitle\">" . _SETTINGS . "</div>";
				$smtp_host = $_POST['newsmtp_host'];
				$smtp_username = $_POST['newsmtp_username'];
				$smtp_password = $_POST['newsmtp_password'];
				$smtp_port = $_POST['newsmtp_port'];
				$smtp_secure = $_POST['newsmtp_secure'];
				$result = dbquery("UPDATE " . $settingsprefix . "fanfiction_settings SET smtp_host = '$smtp_host', smtp_username = '$smtp_username', smtp_password = '$smtp_password', smtp_port = '$smtp_port', smtp_secure = '$smtp_secure' WHERE sitekey = '" . SITEKEY . "'");
				if ($result)
				{
					$output .= write_message(_ACTIONSUCCESSFUL);
					$output .= write_message("<a href='install.php?step=3'>" . _CONTINUE . "</a>");
				}
				else include("../admin/settings.php");
			}
			else include("../admin/settings.php");
		}
		else
		{
			if (isset($_GET['install']))
			{
				if ($_GET['install'] == "automatic")
				{

					$settings = dbquery("CREATE TABLE IF NOT EXISTS " . $settingsprefix . "fanfiction_settings (
  sitekey VARCHAR(50) NOT NULL default '1',
  sitename VARCHAR(200) NOT NULL default 'Your Site',
  slogan VARCHAR(200) NOT NULL default 'It''s a cool site!',
  url VARCHAR(200) NOT NULL default 'http://www.yoursite.com',
  siteemail VARCHAR(200) NOT NULL default 'you@yoursite.com',
  tableprefix VARCHAR(50) NOT NULL default '',
  skin VARCHAR(50) NOT NULL default 'default',
  hiddenskins VARCHAR(255) default '',
  language VARCHAR(10) NOT NULL default 'en',
  submissionsoff SMALLINT NOT NULL default '0',
  storiespath VARCHAR(20) NOT NULL default 'stories',
  store VARCHAR(5) NOT NULL default 'files',
  autovalidate SMALLINT NOT NULL default '0',
  coauthallowed SMALLINT NOT NULL default '0',
  maxwords INTEGER NOT NULL default '0',
  minwords INTEGER NOT NULL default '0',
  imageupload SMALLINT NOT NULL default '0',
  imageheight INTEGER NOT NULL default '200',
  imagewidth INTEGER NOT NULL default '200',
  roundrobins SMALLINT NOT NULL default '0',
  allowseries SMALLINT NOT NULL default '2',
  tinymce SMALLINT NOT NULL default '0',
  allowed_tags VARCHAR(200) NOT NULL default '<b><i><u><center><hr><p><br /><br><blockquote><ol><ul><li><img><strong><em>',
  favorites SMALLINT NOT NULL default '0',
  multiplecats SMALLINT NOT NULL default '0',
  setreg SMALLINT NOT NULL default '1',
  newscomments SMALLINT NOT NULL default '0',
  logging SMALLINT NOT NULL default '0',
  maintenance SMALLINT NOT NULL default '0',
  debug SMALLINT NOT NULL default '0',
  captcha SMALLINT NOT NULL default '0',
  dateformat VARCHAR(20) NOT NULL default 'd/m/y',
  timeformat VARCHAR(20) NOT NULL default '- h:i a',
  recentdays SMALLINT NOT NULL default '7',
  displaycolumns SMALLINT NOT NULL default '1',
  itemsperpage SMALLINT NOT NULL default '25',
  extendcats SMALLINT NOT NULL default '0',
  displayindex SMALLINT NOT NULL default '0',
  defaultsort SMALLINT NOT NULL default '0',
  displayprofile SMALLINT NOT NULL default '0',
  linkstyle SMALLINT NOT NULL default '0',
  linkrange SMALLINT NOT NULL default '5',
  reviewsallowed SMALLINT NOT NULL default '0',
  ratings INTEGER NOT NULL default '0',
  anonreviews SMALLINT NOT NULL default '0',
  revdelete INTEGER NOT NULL default '0',
  rateonly SMALLINT NOT NULL default '0',
  pwdsetting SMALLINT NOT NULL default '0',
  alertson SMALLINT NOT NULL default '0',
  disablepopups SMALLINT NOT NULL default '0',
  agestatement SMALLINT NOT NULL default '0',
  words TEXT,
  version VARCHAR(10) NOT NULL default '" . $version . "',
  smtp_host VARCHAR(200) default NULL,
  smtp_username VARCHAR(50) default NULL,
  smtp_password VARCHAR(50) default NULL,
  smtp_port VARCHAR(5) NOT NULL default '',
  smtp_secure VARCHAR(3) NOT NULL default '',
  PRIMARY KEY  (sitekey)
);");
					if ($settings) $output .= write_message("<img src=\"../images/check.gif\"> " . _SETTINGSTABLESUCCESS . " <br /><a href='install.php?step=2'>" . _CONTINUE . "</a>");
					else $output .= write_message(_SETTINGSTABLEAUTOFAIL . "<br /><br /> <a href='install.php?step=2'>" . _CONTINUE . "</a>");
				}
				else
				{
					$output .= write_message(_SETTINGSTABLEMANUAL . "<br /><br /><a href='install.php?step=2'>" . _CONTINUE . "</a>");
				}
			}
			else
			{

				//$test = dbquery("SELECT * FROM pg_database;");
				$test = dbquery("SELECT * FROM pg_catalog.pg_tables WHERE schemaname = '$tableprefix'");
				if (!$test) $output .= write_message(_CONFIGFAILED. "(2)");
				else $output .= write_message(_SETTINGSTABLESETUP . " <a href='install.php?step=2&amp;install=automatic'>" . _AUTO . "</a> " . _OR . " <a href='install.php?step=2&amp;install=manual'>" . _MANUAL2 . "</a>");
			}
		}
		break;
	default:
		$output .= "<div id='pagetitle'>" . _CONFIGDATA . "</div>";

		if (isset($_POST['submit']))
		{
			$dbhost = descript($_POST['dbhost']);
			$dbname = descript($_POST['dbname']);
			$dbuser = descript($_POST['dbuser']);
			$dbpass = descript($_POST['dbpass']);
			$language = $_POST['language'];
			$sitekey = descript($_POST['sitekey']);
			$settingsprefix = descript($_POST['settingsprefix']);
			$pg_access = @pg_connect("host=$dbhost user=$dbuser password=$dbpass dbname=$dbname");
			 
			if (!$pg_access)
			{
				$output .= write_message(_CONFIGFAILED . "(1)");
			}
		}
		if (isset($_POST['submit']) && $pg_access)
		{
		
			$handle = fopen("../config.php", 'w');
			if (!$handle)
			{
				@chmod("../config.php", 0666);
				$handle = fopen("../config.php", 'w');
			}
			if ($handle)
			{
				$dbhost = descript($_POST['dbhost']);
				$dbname = descript($_POST['dbname']);
				$dbuser = descript($_POST['dbuser']);
				$dbpass = descript($_POST['dbpass']);
				$sitekey = descript($_POST['sitekey']);
				if (empty($sitekey)) $sitekey = random_string($randomcharset, 10);
				$language = $_POST['language'];
				$settingsprefix = descript($_POST['settingsprefix']);
				$text = "<?php 
\$dbhost = \"$dbhost\";
\$dbname = \"$dbname\";
\$dbuser= \"$dbuser\";
\$dbpass = \"$dbpass\";
\$sitekey = \"$sitekey\";
\$settingsprefix = \"$settingsprefix\";

include(\"includes/dbfunctions.php\");
?>";

				fwrite($handle, $text);
				fclose($handle);
				@chmod("../config.php", 0644);
				$output .= write_message(_CONFIGSUCCESS . "<br /><br /><a href='install.php?step=2&amp;language=$language'>" . _CONTINUE . "</a>");
			}
			else $output .= write_message(_ERROR_CONFIGWRITE);
		}
		else
		{
			if (file_exists("../config.php") && !isset($pg_access)) {
				include("../config.php");
			}
			if (isset($tinymce)) $output .= write_message(_CONFIG2DETECTED);
			else if (isset($sitename) && $sitename) $output .= write_message(_CONFIG1DETECTED);
			else
			{
				$output .=
					"<form method='POST' enctype='multipart/form-data' action='install.php?step=1' class='tblborder' style='width: 45%; margin: 1em auto;'>
						<table class='acp' style='margin: 0 auto;'>
							<tr>
								<td><label for='dbhost'>" . _DBHOST . "</label></td><td><input type='text'  name='dbhost' id='dbhost'" . (!empty($dbhost) ? "value='$dbhost'" : "value='localhost'") . "> <A HREF=\"#\" class=\"pophelp\">[?]<span>" . _HELP_DBHOST . "</span></A></td>
							</tr>
							<tr>
								<td><label for='dbname'>" . _DBNAME . "</label></td><td><input type='text' name='dbname' id='dbname'" . (!empty($dbname) ? "value='$dbname'" : "") . "> <A HREF=\"#\" class=\"pophelp\">[?]<span>" . _HELP_DBNAME . "</span></A></td>
							</tr>
							<tr>
								<td><label for='dbuser'>" . _DBUSER . "</label></td><td><input type='text' name='dbuser' id='dbuser'" . (!empty($dbuser) ? "value='$dbuser'" : "") . "> <A HREF=\"#\" class=\"pophelp\">[?]<span>" . _HELP_DBUSER . "</span></A></td>
							</tr>
							<tr>
								<td><label for='dbpass'>" . _DBPASS . "</label></td><td><input type='password' name='dbpass' id='dbpass'" . (!empty($dbpass) ? "value='$dbpass'" : "") . "> <A HREF=\"#\" class=\"pophelp\">[?]<span>" . _HELP_DBPASS . "</span></A></td>
							</tr>
							<tr>
								<td><label for='sitekey'>" . _SITEKEY . "</label></td><td><input type='text' name='sitekey' value='" . (!empty($sitekey) ? $sitekey : random_string($randomcharset, 10)) . "' id='sitekey'> <A HREF=\"#\" class=\"pophelp\">[?]<span>" . _HELP_INSTALL_SITEKEY . "</span></A></td>
							</tr>
							<tr>
								<td><label for=\"language\">" . _LANGUAGE . ":</label></td><td><select name=\"language\">";
				$directory = opendir("../languages");
				while ($filename = readdir($directory))
				{
					if ($filename == "." || $filename == ".." || $filename == "mailer" || substr($filename, 2) == "_admin.php") continue;
					$output .= "<option value=\"" . substr($filename, 0, 2) . "\"" .
						($language == substr($filename, 0, strpos($filename, ".php")) ? " selected" : "") . ">
					" . substr($filename, 0, strpos($filename, ".php")) . "</option>";
				}
				closedir($directory);
				$output .=
					"</select> <A HREF=\"#\" class=\"pophelp\">[?]<span>" . _HELP_LANGUAGE . "</span></A></td>
							</tr>
								<td><label for='settingsprefix'>" . _SETTINGSPREFIX . "</label></td><td><input type='text' name='settingsprefix' id='settingsprefix'" . (isset($settingsprefix) ? "value='$settingsprefix'" : "") . "> <A HREF=\"#\" class=\"pophelp\">[?]<span>" . _HELP_SETTINGSPREFIX . "</span></A></td>
							</tr>
							<tr><td colspan='2'><div style='text-align: center; margin: 1em;'><INPUT type=\"submit\"class=\"button\" name=\"submit\" value=\"submit\"></div></td></tr>
						</table>
					</form>";
			}
		}
}
$tpl->assign("output", $output);

$tpl->printToScreen();
if (isset($_GET['step']) && $_GET['step'] > 2) dbclose();
