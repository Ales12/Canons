<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}


function canons_info()
{
    return array(
        "name"			=> "Canons",
        "description"	=> "Mit diesen Plugin kannst du eine Übersicht an Canons erstellen, welche Gäste/User mit einen klick für sich Reservieren können.",
        "website"		=> "",
        "author"		=> "Ales",
        "authorsite"	=> "https://github.com/Ales12",
        "version"		=> "1.0",
        "guid" 			=> "",
        "codename"		=> "",
        "compatibility" => "*"
    );
}

function canons_install()
{
    global $db, $cache, $mybb;

    //Datenbank erstellen
    $collation = $db->build_create_table_collation();

    $db->write_query("
        CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."canons` (
          `canon_id` int(10) NOT NULL auto_increment,
          `canon_name` varchar(500) CHARACTER SET utf8 NOT NULL, 
          `canon_age` int(10) NOT NULL,
          `canon_gender` varchar(500) CHARACTER SET utf8 NOT NULL,
         `canon_blood` varchar(500) CHARACTER SET utf8 NOT NULL,
          `canon_group` varchar(500) CHARACTER SET utf8 NOT NULL,
          `canon_def` varchar(500) CHARACTER SET utf8 NOT NULL,
          `canon_desc` text CHARACTER SET utf8 NOT NULL,
          `canon_else` varchar(500) CHARACTER SET utf8 NOT NULL, 
           `canon_avatar` varchar(500) CHARACTER SET utf8 NOT NULL,  
           `canon_pic` varchar(500) CHARACTER SET utf8 NOT NULL,        
          `canon_reserved` int(10)  DEFAULT 0 NOT NULL,
          `canon_taken` int(10)  DEFAULT 0 NOT NULL,
           `canon_admin` int(10)  DEFAULT 0 NOT NULL,
           `canon_creator` int(10)  DEFAULT 0 NOT NULL,
          PRIMARY KEY (`canon_id`)
           ) ENGINE=MyISAM{$collation};
    ");


    $db->add_column("usergroups", "canaddcanon", "tinyint NOT NULL default '0'");
    $cache->update_usergroups();

    // Einstellung
    $setting_group = array(
        'name' => 'canonssettings',
        'title' => 'Einstellungen für Canons',
        'description' => 'Hier kannst du die Einstellungen für deine Canons vornehmen.',
        'disporder' => 3, // The order your setting group will display
        'isdefault' => 0
    );

    $gid = $db->insert_query("settinggroups", $setting_group);

    $setting_array = array(
        // A select box
        'canons_reserv' => array(
            'title' => 'Dürfen Gäste Canons reservieren?',
            'description' => 'Ist es Gästen erlaubt, dass sie ebenfalls Canons reservieren dürfen?',
            'optionscode' => 'yesno',
            'value' => 1,
            'disporder' => 2
        ),
        // A yes/no boolean box
        'canons_groups' => array(
            'title' => 'Mögliche Gruppen für Canons',
            'description' => 'Unter welche Gruppen fallen die Canons?',
            'optionscode' => 'text',
            'value' => "Schüler, Erwachsene, Aversio",
            'disporder' => 3
        ),
        // A yes/no boolean box
        'canons_userfid' => array(
            'title' => 'FID für Username',
            'description' => 'wie ist die FID für das Profilfeld, indem der Spielername gespeichert wird.',
            'optionscode' => 'text',
            'value' => "fid3",
            'disporder' => 4
        ),
        'canons_banned_groups' => array(
            'title' => 'Ausgeschlossene Gruppen',
            'description' => 'Aus welchen Gruppen sollen keine Accounts ausgelesen werden?.',
            'optionscode' => 'groupselect',
            'value' => 0,
            'disporder' => 5
        ),
    );

    foreach($setting_array as $name => $setting)
    {
        $setting['name'] = $name;
        $setting['gid'] = $gid;

        $db->insert_query('settings', $setting);
    }

    // Templates
    $insert_array = array(
        'title' => 'canons',
        'template' => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->canons_welcome}</title>
{$headerinclude}
</head>
<body>
{$header}

<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>{$lang->canons_welcome}</strong></td>
</tr>

<tr>
	<td class="trow1" valign="top" width="15%">
		{$canons_nav}
		<div class="tcat"><strong>Sortierung der Charaktere</strong></div>
		<form id="canon_new" methode="get" action="misc.php?action=canons">
				<input type="hidden" name="action" value="canons">
	<table style="margin: auto;" cellpadding="5">
		<tr>
			<td class="trow1">
		<div class="headline3">Sortiere nach</div>
				<select name="sort_chara">
					<option value="canon_name">Charaktername</option>
					<option value="canon_age">Charakteralter</option>
				</select>
		</td></tr>
		<tr>
		<td>
		<div class="headline3">Sortierrichtung</div>
				<select name="sort_way">
					<option value="ASC">Aufsteigend</option>
					<option value="DESC">Abestiegen</option>
				</select>
		</td>
		</tr>
	</table>
	<br />
			<div class="tcat"><strong>Filtern der Charaktere</strong></div>
		<table style="margin: auto;" cellpadding="5">
<tr><td>
				<div class="headline3">Filtern nach Altersklasse</div>
				<select name="agespan">
					<option value="canon_age!=\'\'">alle Altersklassen</option>
					<option value="canon_age between 0 and 9">0 - 9 Jahre</option>
					<option value="canon_age between 10 and 19">10 - 19 Jahre</option>
					<option value="canon_age between 20 and 29">20 - 29 Jahre</option>
					<option value="canon_age between 30 and 39">30 - 39 Jahre</option>
					<option value="canon_age between 40 and 49">40 - 49 Jahre</option>
					<option value="canon_age between 50 and 59">50 - 59 Jahre</option>
					<option value="canon_age between 60 and 69">60 - 69 Jahre</option>
					<option value="canon_age between 70 and 79">70 - 79 Jahre</option>
					<option value="canon_age between 80 and 89">80 - 89 Jahre</option>
					<option value="canon_age between 90 and 99">90 - 99 Jahre</option>
				</select>
				</td></tr>
		<tr>
				<td>
				<div class="headline3">Filtern nach Geschlecht</div>
				<select name="filter_gender">
					<option value="%">alle Geschlechter</option>
					<option value="männlich">Männlich</option>
					<option value="weiblich">Weiblich</option>
					<option value="Divers">Divers</option>
				</select>
			</td>	</tr>
			<tr>
							<td>
				<div class="headline3">Filtern nach Gruppe</div>
				<select name="filter_group">
					<option value="%">alle Gruppen</option>
					{$filter_group}
				</select>
				</td>	</tr>
					<tr>
							<td>
				<div class="headline3">Filtern nach Blutstatus</div>
				<select name="filter_blood">
					<option value="%">alle Blutstati</option>
					<option>Reinblut</option>
					<option>Halbblut</option>
					<option>Muggelstämmig</option>
					<option>Pretender</option>
					<option>Squib</option>
					<option>Muggel</option>
				</select>
				</td>	</tr>
				<tr><td align="center"><input type="submit" name="canon_new" value="Neu laden" id="submit" class="button">
					</td></tr>
		</table>
	</form>
	</td>
<td class="trow1" width="85%" valign="top">
	<div class="canon_flex">
{$canons_bit}
	</div></td>
</tr>
</table>
{$footer}
</body>
</html>
		'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'canons_add',
        'template' => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->canons_add}</title>
{$headerinclude}
</head>
<body>
{$header}

<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>{$lang->canons_add}</strong></td>
</tr>

<tr>
	<td class="trow1" valign="top" width="15%">
		{$canons_nav}
	</td>
<td class="trow1" width="85%" valign="top">
<form id="add_canon" method="post" action="misc.php?action=add_canons">
<table width="95%" style="margin: auto;" border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" >
	<tr>
		<td class="trow1" width="50%">
			<strong>{$lang->canons_name}</strong>
		</td>
		<td class="trow2" width="50%">
			<input type="text" name="canon_name" id="canon_name" placeholder="Name des Charakters" class="textbox"  style="width: 95%;" required/>
		</td>
	</tr>
		<tr>
		<td class="trow1">
			<strong>{$lang->canons_age}</strong>
		</td>
		<td class="trow2">
			<input type="number" name="canon_age" id="canon_age" placeholder="00" class="textbox"  style="width: 15%;" required/>
		</td>
	</tr>
	<tr>
			<tr>
		<td class="trow1">
			<strong>{$lang->canons_gender}</strong>
		</td>
		<td class="trow2">
			<select name="canon_gender" required>
				<option>Männlich</option>
				<option>Weiblich</option>
				<option>Divers</option>
			</select>
		</td>
	</tr>
			<tr>
		<td class="trow1">
			<strong>{$lang->canons_blood}</strong>
		</td>
		<td class="trow2">
			<input type="text" name="canon_blood" id="canon_blutt" placeholder="Reinblut, Halbblut, Muggelstämmig, Squib?" class="textbox"  style="width: 95%;"  required/>
		</td>
	</tr>
	<tr>
		<td class="trow1">
			<strong>{$lang->canons_group}</strong>
		</td>
		<td class="trow2">
<select name="canon_group" required>
	{$group}
			</select>
		</td>
	</tr>
			<tr>
		<td class="trow1">
			<strong>{$lang->canons_else}</strong>
			<div class="smalltext">{$lang->canons_else_desc}</div>
		</td>
		<td class="trow2">
			<input type="text" name="canon_else" id="canon_else" placeholder="Beruf etc." class="textbox"  style="width: 95%;"  />
		</td>
	</tr>
				<tr>
		<td class="trow1">
			<strong>{$lang->canons_def}</strong>
			<div class="smalltext">{$lang->canons_def_desc}</div>
		</td>
							<td class="trow2">
			<input type="text" id="canon_def" name="canon_def" placeholder="Hier kannst du noch eine Unterkategorie angeben wie z.B. Gryffindor, Ravenclaw oder Orden des Phönix" class="textbox"  style="width: 95%;" />
		</td>
	</tr>
		
			<tr>
		<td class="trow1">
			<strong>{$lang->canons_desc}</strong>
			<div class="smalltext">{$lang->canons_else_desc}</div>
		</td>
		<td class="trow2">
<textarea id="canon_desc" name="canon_desc" placeholder="Hier kannst du den Charakter beschreiben. Charakterliche Eigenschaften und was ihn antreibt." class="textbox"  style="width: 95%; height: 100px;" required></textarea>
		</td>
	</tr>		
		<tr>
		<td class="trow1">
			<strong>{$lang->canons_avatar}</strong>
		</td>
							<td class="trow2">
			<input type="text" id="canon_avatar" name="canon_avatar" placeholder="Avatarperson" class="textbox"  style="width: 95%;" required/>
		</td>
	</tr>
	<tr>
		<td class="trow1">
			<strong>{$lang->canons_pic}</strong>
			<div class="smalltext">{$lang->canons_pic_desc}</div>
		</td>
							<td class="trow2">
			<input type="text" id="canon_pic" name="canon_pic" placeholder="https://" class="textbox"  style="width: 95%;" required/>
		</td>
	</tr>
			<tr class="trow2">
<td colspan="2" align="center"><input type="submit" name="add_canon" value="{$lang->canons_submit}" id="submit" class="button"></td>
		</tr>
	</table>
	</form>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);
    $insert_array = array(
        'title' => 'canons_alert',
        'template' => $db->escape_string('<div class="red_alert">
	{$lang->canon_alert}
</div>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);
    $insert_array = array(
        'title' => 'canons_bit',
        'template' => $db->escape_string('<div class="canon_box">
	<div class="canon_pic">
			<img src="{$canonpic}">
	</div>
	<div class="canon_info">
		<div class="canon_name">{$canonname}</div>
		<div class="canon_avatar">{$lang->canon_lookslike} <b>{$canonavatar}</b></div>
		<div class="canon_infos">
			{$canonage} {$canongender} {$canonblood} {$canonelse}  {$canondef} 
		</div>
	<div class="canon_desc">
		{$canondesc}
		</div>{$canons_options}
		<div class="canon_status">
			{$canon_reserv} 
		</div>
	</div>
</div>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);


    $insert_array = array(
        'title' => 'canons_edit',
        'template' => $db->escape_string('<form id="edit_canon" method="post" action="misc.php?action=canons">
	<input value="{$row[\'canon_id\']}" name="canon_id" type="hidden"> 
<table width="95%" style="margin: auto;" border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" >
	<tr><td class="thead" colspan="2"><strong>{$lang->canon_edit_form}</strong></td></tr>
	<tr>
		<td class="trow1" width="50%">
			<strong>{$lang->canons_name}</strong>
		</td>
		<td class="trow2" width="50%">
			<input type="text" name="canon_name" id="canon_name" value="{$row[\'canon_name\']}" class="textbox"  style="width: 95%;" required/>
		</td>
	</tr>
		<tr>
		<td class="trow1">
			<strong>{$lang->canons_age}</strong>
		</td>
		<td class="trow2">
			<input type="number" name="canon_age" id="canon_age" value="{$row[\'canon_age\']}"  class="textbox"  style="width: 25%;" required/>
		</td>
	</tr>
	<tr>
			<tr>
		<td class="trow1">
			<strong>{$lang->canons_gender}</strong>
		</td>
		<td class="trow2">
			<select name="canon_gender" required>
				<option value="{$row[\'canon_gender\']}">{$row[\'canon_gender\']}</option>
				<option>Männlich</option>
				<option>Weiblich</option>
				<option>Divers</option>
			</select>
		</td>
	</tr>
			<tr>
		<td class="trow1">
			<strong>{$lang->canons_blood}</strong>
		</td>
		<td class="trow2">
			<input type="text" name="canon_blood" id="canon_blood" value="{$row[\'canon_blood\']}"  class="textbox"  style="width: 95%;"  required/>
		</td>
	</tr>
	<tr>
		<td class="trow1">
			<strong>{$lang->canons_group}</strong>
		</td>
		<td class="trow2">
<select name="canon_group" required>
	<option value="{$row[\'canon_group\']}">{$row[\'canon_group\']}</option>
	{$group}
			</select>
		</td>
	</tr>
			<tr>
		<td class="trow1">
			<strong>{$lang->canons_else}</strong>
			<div class="smalltext">{$lang->canons_else_desc}</div>
		</td>
		<td class="trow2">
			<input type="text" name="canon_else" id="canon_else" value="{$row[\'canon_else\']}"  class="textbox"  style="width: 95%;"  />
		</td>
	</tr>
				<tr>
		<td class="trow1">
			<strong>{$lang->canons_def}</strong>
			<div class="smalltext">{$lang->canons_def_desc}</div>
		</td>
							<td class="trow2">
			<input type="text" id="canon_def" name="canon_def" value="{$row[\'canon_def\']}"  class="textbox"  style="width: 95%;" />
		</td>
	</tr>
		
			<tr>
		<td class="trow1">
			<strong>{$lang->canons_desc}</strong>
			<div class="smalltext">{$lang->canons_else_desc}</div>
		</td>
		<td class="trow2">
<textarea id="canon_desc" name="canon_desc" class="textbox"  style="width: 95%; height: 100px;" required>{$row[\'canon_desc\']}</textarea>
		</td>
	</tr>		
		<tr>
		<td class="trow1">
			<strong>{$lang->canons_avatar}</strong>
		</td>
							<td class="trow2">
			<input type="text" id="canon_avatar" name="canon_avatar" value="{$row[\'canon_avatar\']}"  class="textbox"  style="width: 95%;" required/>
		</td>
	</tr>
	<tr>
		<td class="trow1">
			<strong>{$lang->canons_pic}</strong>
			<div class="smalltext">{$lang->canons_pic_desc}</div>
		</td>
							<td class="trow2">
			<input type="text" id="canon_pic" name="canon_pic" value="{$row[\'canon_pic\']}"  class="textbox"  style="width: 95%;" required/>
		</td>
	</tr>
			<tr class="trow2">
<td colspan="2" align="center"><input type="submit" name="edit_canon" value="{$lang->canon_submitedit}" id="submit" class="button"></td>
		</tr>
	</table>
	</form>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'canons_guest_reserv',
        'template' => $db->escape_string('<form method="post" action="misc.php?action=canons" id="reserv_canon_guest">
	<input type="text" name="canon_id" value="{$row[\'canon_id\']}">
	<input type="text" name="action" value="canons">
	<table class="tborder" border="0" style="text-align: center;">
		<tr><td class="tcat"><strong>{$lang->canon_reserv_name}</strong></td></tr>
		<tr><td class="trow1"><input id="reserv_name" name="reserv_name" class="textbox" type="text" placeholder="Dein Name" required style="width: 80%;margin: auto;"  /></td></tr>
		<tr><td class="trow2"><input type="submit" name="reserv_canon_guest" value="{$lang->canon_submit_reserv}" id="submit" class="button"></td>
	</table>
</form>		'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'canons_modcp',
        'template' => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->modcp}</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$modcp_nav}
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" align="center"><strong>{$lang->canon_modcp}</strong></td>
</td>
</tr>
	<tr><td class="trow1"><div class="canon_flex">
		{$canons_modcp_bit}
		</div>
		</td>
	</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'canons_modcp_bit',
        'template' => $db->escape_string('<div class="canon_box">
	<div class="canon_pic">
			<img src="{$canonpic}">
	</div>
	<div class="canon_info">
		<div class="canon_name">{$canonname}</div>
		<div class="canon_avatar">{$lang->canon_lookslike} <b>{$canonavatar}</b></div>
		<div class="canon_infos">
			{$canonage} {$canongender} {$canonblood} {$canonelse}  {$canondef} 
		</div>
	<div class="canon_desc">
		{$canondesc}
		</div>
		<div class="canon_modcp_option">
			<div><a href="modcp.php?action=canons&accept_canon={$row[\'canon_id\']}">{$lang->canon_accept}</a></div>
				<div><a onclick="$(\'#reject_{$row[\'canon_id\']}\').modal({ fadeDuration: 250, keepelement: true, zIndex: (typeof modal_zindex !== \'undefined\' ? modal_zindex : 9999) }); return false;" style="cursor: pointer;">{$lang->canon_reject}</a>
                                            <div class=\'modal\' id="reject_{$row[\'canon_id\']}" style=\'display: none;\'>{$canon_reject}</div></div>
		</div>
	</div>
</div>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'canons_modcp_reject',
        'template' => $db->escape_string('<form id="reject_canon" method="post" action="modcp.php?action=canons">
	<input type="hidden" value="{$row[\'canon_id\']}" name="canon_id">
	<table style="margin: 10px auto; width: 100%;">
		<tr><td class="thead"><strong>{$lang->canon_reject}</strong></td></tr>
		<tr><td class="trow1" align="center"><strong>{$lang->canon_reason}</td></tr>
		<tr><td class="trow2" align="center"><textarea id="canon_reason" name="canon_reason" placeholder="Gib hier den Grund an, wieso der Canon abgelehnt wurde." class="textbox"  style="width: 95%; height: 100px;" required></textarea>
			</td>
		</tr>
		<tr><td class="trow1" align="center"><input type="submit" name="reject_canon" value="{$lang->canon_reject}" id="submit" class="button"></td></tr>
	</table>
</form>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);


    $insert_array = array(
        'title' => 'canons_nav',
        'template' => $db->escape_string('<div class="tcat"><strong>Navigation</strong></div>

		<div class="canon_nav">
		<a href="misc.php?action=canons">{$lang->canons_nav_all}</a>
	</div>
{$canon_add}'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'canons_options',
        'template' => $db->escape_string('<div class="canon_options">
<div>	<a href="misc.php?action=canons&delete_canon={$row[\'canon_id\']}">{$lang->canon_delete}</a></div>
<div><a onclick="$(\'#edit_{$row[\'canon_id\']}\').modal({ fadeDuration: 250, keepelement: true, zIndex: (typeof modal_zindex !== \'undefined\' ? modal_zindex : 9999) }); return false;" style="cursor: pointer;">{$lang->canon_edit}</a>
                                            <div class=\'modal\' id="edit_{$row[\'canon_id\']}" style=\'display: none;\'>{$canon_edit}</div></div>
</div>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'canons_taken',
        'template' => $db->escape_string('<form method="post" action="misc.php?action=canons">
		<input type="hidden" name="canon_id" value="{$row[\'canon_id\']}" id="canon_id" class="textbox">
	<table class="tborder" border="0" align="center" style="text-align: center;">
		<tr>
			<td class="tcat">{$lang->canon_submit_taken}</td>
		</tr>
		<tr>
			<td class="trow1"><select name="canon_chara">
				{$all_charas_options}
				</select>
			</td>
		</tr>
		<tr>
			<td class="trow2">
				<input type="submit" name="canon_taken" value="{$lang->canon_submit_taken}" id="submit" class="button">
			</td>
		</tr>
	</table>
</form>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);


    //CSS einfügen
    $css = array(
        'name' => 'canons.css',
        'tid' => 1,
        'attachedto' => '',
        "stylesheet" =>    '.canon_flex{
	display: flex;
	justify-content: center;
	flex-wrap: wrap;
}

.canon_nav{
	margin: 2px 10px;
	text-align:  center;
}

.canon_box{
display: flex;
box-sizing: border-box;
padding: 10px;
width: 50%;
align-items: center;
margin: 10px auto;
}

.canon_pic{
	text-align: center;
	padding: 0 10px;
	height: 150px;
	width: 120px;
}

.canon_info{
	margin: 0 10px;
	width: 75%;
box-sizing: border-box;
padding: 10px;
}

.canon_name{
	font-size: 15px;
	text-align: center;
	text-transform: uppercase;
}

.canon_avatar{
		font-size: 11px;
	text-align: center;
	text-transform: uppercase;
}

.canon_infos{
		font-size: 10px;
	text-align: center;
	text-transform: uppercase;
}

.canon_desc{
	width: 95%;
	margin: 5px auto 0 auto;
	overflow: auto;
	height: 100px;
	scrollbar-width: none !important;
	font-size: 12px;
	text-align: justify;
}

.canon_options{
	float: right;
	display: flex;
	margin:5px auto;
}

.canon_options > div{
	margin: 0 3px;	
}

.canon_status{
text-align: center;
	text-transform: uppercase;
}

.canon_modcp_option{
	display: flex;
	justify-content: center;
}

.canon_modcp_option > div{
	margin: 2px 10px;	
	text-align: center;
	text-transform: uppercase;
}
        ',
        'cachefile' => $db->escape_string(str_replace('/', '', 'residences.css')),
        'lastmodified' => time()
    );

    require_once MYBB_ADMIN_DIR . "inc/functions_themes.php";

    $sid = $db->insert_query("themestylesheets", $css);
    $db->update_query("themestylesheets", array("cachefile" => "css.php?stylesheet=" . $sid), "sid = '" . $sid . "'", 1);

    $tids = $db->simple_select("themes", "tid");
    while ($theme = $db->fetch_array($tids)) {
        update_theme_stylesheet_list($theme['tid']);
    }
// Don't forget this!
    rebuild_settings();

}

function canons_is_installed()
{
    global $db;
    if($db->table_exists("canons"))
    {
        return true;
    }
    return false;
}

function canons_uninstall()
{
    global $db, $cache;

    // Datenbanken löschen
    if ($db->table_exists("canons")) {
        $db->drop_table("canons");
    }
    if ($db->field_exists("canaddcanon", "usergroups")) {
        $db->drop_column("usergroups", "canaddcanon");
    }

    // Einstellungen löschen
    $db->query("DELETE FROM " . TABLE_PREFIX . "settinggroups WHERE name='canonssettings'");
    $db->query("DELETE FROM " . TABLE_PREFIX . "settings WHERE name='canons_reserv'");
    $db->query("DELETE FROM " . TABLE_PREFIX . "settings WHERE name='canons_groups'");
    $db->query("DELETE FROM " . TABLE_PREFIX . "settings WHERE name='canons_userfid'");
    $db->query("DELETE FROM " . TABLE_PREFIX . "settings WHERE name='canons_banned_groups'");

    $db->delete_query("templates", "title LIKE '%canons%'");

    require_once MYBB_ADMIN_DIR . "inc/functions_themes.php";
    $db->delete_query("themestylesheets", "name = 'canons.css'");
    $query = $db->simple_select("themes", "tid");
    while ($theme = $db->fetch_array($query)) {
        update_theme_stylesheet_list($theme['tid']);
        rebuild_settings();

        rebuild_settings();
    }
}

function canons_activate()
{
    require MYBB_ROOT . "/inc/adminfunctions_templates.php";
    find_replace_templatesets("header", "#".preg_quote('<navigation>')."#i", '{$alert_canons}<navigation>');
    find_replace_templatesets("modcp_nav", "#".preg_quote('{$modcp_nav_users}')."#i", '{$modcp_nav_users}{$canons_modcp}');
}

function canons_deactivate()
{
    require MYBB_ROOT . "/inc/adminfunctions_templates.php";
    find_replace_templatesets("header", "#".preg_quote('{$alert_canons}')."#i", '', 0);
    find_replace_templatesets("modcp_nav", "#".preg_quote('{$canons_modcp}')."#i", '', 0);
}

// Backend Hooks
$plugins->add_hook("admin_formcontainer_end", "canons_usergroup_permission");
$plugins->add_hook("admin_user_groups_edit_commit", "canonsn_usergroup_permission_commit");

// Usergruppen-Berechtigungen
function canons_usergroup_permission()
{
    global $mybb, $lang, $form, $form_container, $run_module;

    if($run_module == 'user' && !empty($form_container->_title) & !empty($lang->misc) & $form_container->_title == $lang->misc)
    {
        $canons_options = array(
            $form->generate_check_box('canaddcanon', 1, "Kann einen Canon hinzufügen?", array("checked" => $mybb->input['canaddcanon'])),
        );
        $form_container->output_row("Einstellung für Canons", "", "<div class=\"group_settings_bit\">".implode("</div><div class=\"group_settings_bit\">", $canons_options)."</div>");
    }
}

function canonsn_usergroup_permission_commit()
{
    global $db, $mybb, $updated_group;
    $updated_group['canaddcanon'] = $mybb->get_input('canaddcanon', MyBB::INPUT_INT);
}

$plugins->add_hook('misc_start', 'canons_misc');

// In the body of your plugin
function canons_misc()
{
    global $mybb, $templates, $lang, $header, $headerinclude, $theme, $footer,$group, $page, $db, $parser, $options, $canons_nav, $canon_reserv, $canontaken, $canon_add, $canons_options;
    $lang->load('canons');

    ///der Parser halt
    require_once MYBB_ROOT."inc/class_parser.php";;
    $parser = new postParser;
    // Do something, for example I'll create a page using the hello_world_template
    $options = array(
        "allow_html" => 1,
        "allow_mycode" => 1,
        "allow_smilies" => 1,
        "allow_imgcode" => 1,
        "filter_badwords" => 0,
        "nl2br" => 1,
        "allow_videocode" => 0
    );

    if($mybb->usergroup['canaddcanon'] == 1) {
        $canon_add = "	<div class='canon_nav'>
		<a href='misc.php?action=add_canons'>{$lang->canons_nav_add}</a>
	</div>";
    }
    eval("\$canons_nav = \"" . $templates->get("canons_nav") . "\";");
    // Canons hinzufügen :D
    if($mybb->get_input('action') == 'add_canons')
    {
        // Do something, for example I'll create a page using the hello_world_template

        // Add a breadcrumb
        add_breadcrumb($lang->canons_nav_add, "misc.php?action=add_canons");

        $canon_groups = explode(", ",$mybb->settings['canons_groups']);

        foreach ($canon_groups as $canon_group){
            $group .= "<option value='{$canon_group}'>{$canon_group}</option>";
        }

        if(isset($mybb->input['add_canon'])) {
            if ($mybb->usergroup['canmodcp'] == 1) {
                $admin_ok = 1;
            } else {
                $admin_ok = 0;
            }

            $newcanon = array(
                "canon_name" => $db->escape_string($mybb->input['canon_name']),
                "canon_age" => (int)$mybb->input['canon_age'],
                "canon_blood" => $db->escape_string($mybb->input['canon_blood']),
                "canon_gender" => $db->escape_string($mybb->input['canon_gender']),
                "canon_group" => $db->escape_string($mybb->input['canon_group']),
                "canon_def" => $db->escape_string($mybb->input['canon_def']),
                "canon_else" => $db->escape_string($mybb->input['canon_else']),
                "canon_avatar" => $db->escape_string($mybb->input['canon_avatar']),
                "canon_desc" => $db->escape_string($mybb->input['canon_desc']),
                "canon_pic" => $db->escape_string($mybb->input['canon_pic']),
                "canon_admin" => $admin_ok,
                "canon_creator" => $mybb->user['uid']
            );

            $db->insert_query("canons", $newcanon);
            redirect ("misc.php?action=add_canons");
        }



        // Using the misc_help template for the page wrapper
        eval("\$page = \"".$templates->get("canons_add")."\";");
        output_page($page);
    }

    // Canons ausgeben :D
    if($mybb->get_input('action') == 'canons')
    {
        // Do something, for example I'll create a page using the hello_world_template

        // Add a breadcrumb
        add_breadcrumb($lang->canons_nav_all, "misc.php?action=canons");

        $canon_groups = explode(", ",$mybb->settings['canons_groups']);

        foreach ($canon_groups as $canon_group){
            $filter_group .= "<option value='{$canon_group}'>{$canon_group}</option>";
        }

        $all_banned_groups = $mybb->settings['canons_banned_groups'];

        // welche User aus welchen Gruppen sollen ausgelesenw erden
        if(!empty($all_banned_groups)or $all_banned_groups != -1){
            $banned_groups = "where usergroup NOT IN ($all_banned_groups)";
        }



        $sort = "canon_name";
        $sortway = "ASC";

        $gender_filter = "%";
        $group_filter = "%";
        $blood_filter = "%";
        /*$filter_living = "%";*/
        $age_filter = "and canon_age != ''";

        if(isset($mybb->input['canon_new'])) {
            $sort = $mybb->input['sort_chara'];
            $sortway = $mybb->input['sort_way'];
            $gender_filter = $mybb->input['filter_gender'];
            $group_filter = $mybb->input['filter_group'];
            $age_filter = "and ".$mybb->input['agespan'];
            $blood_filter = $mybb->input['filter_blood'];
            $url_extra = "&sort_chara={$sort}&sort_way={$sortway}&agespan={$age_filter}&filter_group={$group_filter}&filter_gender={$gender_filter}&filter_bloond={$blood_filter}&chara_new=Neu+laden";
        }

        $select_canons = $db->query("SELECT COUNT(*) AS canons
            FROM ".TABLE_PREFIX."canons
            WHERE canon_group like '".$group_filter."'
            and canon_gender like '".$gender_filter."'
            and canon_blood like '".$blood_filter."'
            $age_filter
            ORDER BY $sort $sortway
        ");

        $count = $db->fetch_field($select_canons, "canons");;
        $perpage = 8;
        $page = intval($mybb->input['page']);

        if($page) {
            $start = ($page-1) *$perpage;
        }
        else {
            $start = 0;
            $page = 1;
        }
        $end = $start + $perpage;
        $lower = $start+1;
        $upper = $end;
        if($upper > $count) {
            $upper = $count;
        }

        $url = "{$mybb->settings['bburl']}/misc.php?action=canons{$url_extra}";

        $multipage = multipage($count, $perpage, $page, $url);


        $select_canons = $db->query("SELECT *
            FROM ".TABLE_PREFIX."canons
            WHERE canon_group like '".$group_filter."'
            and canon_gender like '".$gender_filter."'
            and canon_blood like '".$blood_filter."'
            $age_filter
            ORDER BY $sort $sortway
           LIMIT $start, $perpage 
        ");

        while($row = $db->fetch_array($select_canons)){
            $canonname = "";
            $canonage = "";
            $canondef = "";
            $canondesc = "";
            $canongender = "";
            $canongroup ="";
            $canonelse= "";
            $canonpic = "";
            $canonreserved = "";
            $canontaken = "";
            $canon_reserv = "";
            $canonblood = "";
            $canons_options = "";

            eval("\$canon_edit = \"".$templates->get("canons_edit")."\";");

            if($mybb->user['uid'] == $row['canon_creator']){
                eval("\$canons_options = \"".$templates->get("canons_options")."\";");
            }


            $all_user_query = $db->query("SELECT *
                       from ".TABLE_PREFIX."users
                       {$banned_groups}
                       order by username ASC 
                    ");


            while($all_charas = $db->fetch_array($all_user_query)){
                $all_charas_options .= "<option value='{$all_charas['uid']}'>{$all_charas['username']}</option>";
            }

            eval("\$canons_taken= \"".$templates->get("canons_taken")."\";");
            if($mybb->usergroup['canmodcp'] == 1) {


                eval("\$canons_options = \"".$templates->get("canons_options")."\";");

                if (empty($row['canon_taken'])) {
                    $canontaken = " <a onclick=\"$('#taken_{$row['canon_id']}').modal({ fadeDuration: 250, keepelement: true, zIndex: (typeof modal_zindex !== 'undefined' ? modal_zindex : 9999) }); return false;\" style=\"cursor: pointer;\">{$lang->canon_taken}</a>
                                            <div class='modal' id='taken_{$row['canon_id']}' style='display: none;'>{$canons_taken}</div>";
                    $canonname = $row['canon_name'].$canontaken;
                } else {
                    $taken_user_query = $db->simple_select("users", "*",
                        "uid = {$row['canon_taken']}");
                    $taken_user = $db->fetch_array($taken_user_query);

                    $canontaken = " <a href='misc.php?action=canons&nottaken_canon={$row['canon_id']}'>{$lang->canon_taken_off}</a>";
                    $username = format_name($taken_user['username'], $taken_user['usergroup'], $taken_user['displaygroup']);
                    $canonname = build_profile_link($username, $taken_user['uid']).$canontaken;

                }
            }




            $canonage = $row['canon_age']." Jahre";
            $canongender = $lang->canon_between.$row['canon_gender'];
            $canonblood = $lang->canon_between.$row['canon_blood'];
            if(!empty($row['canon_else'])) {
                $canonelse = $lang->canon_between.$row['canon_else'];
            }
            $canongroup = $lang->canon_between.$row['canon_group'];
            if(!empty($row['canon_def'])) {
                $canondef = $lang->canon_between.$row['canon_def'];
            }
            $canondesc = $parser->parse_message($row['canon_desc'], $options);
            $canonpic = $row['canon_pic'];
            $canonavatar = $row['canon_avatar'];



            eval("\$canons_guest_reserv = \"".$templates->get("canons_guest_reserv")."\";");
            if($row['canon_reserved'] == 0 and empty($row['canon_reserved_name'])){
                if($mybb->user['uid'] == 0){
                    $canon_reserv = "<div><a onclick=\"$('#reserv_{$row['canon_id']}').modal({ fadeDuration: 250, keepelement: true, zIndex: (typeof modal_zindex !== 'undefined' ? modal_zindex : 9999) }); return false;\" style=\"cursor: pointer;\">{$lang->canon_reserv}</a> </div>
                                            <div class='modal' id='reserv_{$row['canon_id']}' style='display: none;'>{$canons_guest_reserv}</div>";
                } else {
                    $canon_reserv = "<div><a href='misc.php?action=canons&reserv_canon={$row['canon_id']}'>{$lang->canon_reserv}</a></div>";
                }
            } else{
                if($mybb->user['uid'] != 0){
                    if($mybb->user['uid'] == $row['canon_reserved'] or $mybb->usergroup['canmodcp']  == 1){
                        $delete_reserv = "<a href='misc.php?action=canons&deletereserv_canon={$row['canon_id']}'>{$lang->canon_delete_reserv}</a>";
                    }
                }

                $get_date = date("d.m.Y", $row['canon_reserved_time']);

                $canon_reserv = "Seit {$get_date} von <b>{$row['canon_reserved_name']}</b> reserviert. {$delete_reserv}";
            }

            eval("\$canons_bit .= \"".$templates->get("canons_bit")."\";");
        }


        // Gästereservierung

        if(isset($mybb->input['reserv_canon_guest'])){
            $canon_id = $_POST['canon_id'];
            $guest_reserv_array = array(
                "canon_reserved_name" => $db->escape_string($_POST['reserv_name']),
                "canon_reserved_time" => TIME_NOW,
            );

            $db->update_query("canons", $guest_reserv_array, "canon_id = {$canon_id}");
            redirect ("misc.php?action=canons");
        }

        // userresrvierung
        $reserv_canon = $mybb->input['reserv_canon'];
        if($reserv_canon){
            $userfid = $mybb->settings['canons_userfid'];
            $user_reserv_array = array(
                "canon_reserved" => $mybb->user['uid'],
                "canon_reserved_name" => $db->escape_string($mybb->user[$userfid]),
                "canon_reserved_time" => TIME_NOW,
            );

            $db->update_query("canons", $user_reserv_array, "canon_id = {$reserv_canon}");
            redirect ("misc.php?action=canons");
        }

        // Reservierung freigeben
        $deletereserv_canon = $mybb->input['deletereserv_canon'];
        if($deletereserv_canon){
            $deletereserv = array(
                "canon_reserved" => 0,
                "canon_reserved_name" => "",
                "canon_reserved_time" => 0
            );
            $db->update_query("canons", $deletereserv, "canon_id = {$deletereserv_canon}");
            redirect ("misc.php?action=canons");
        }

        // Canon als vergeben makieren
        if(isset($mybb->input['canon_taken'])) {
            $taken_canon = (int)$mybb->input['canon_id'];
            $canon_taken = array(
                "canon_taken" => (int)$mybb->input['canon_chara']
            );
            $db->update_query("canons", $canon_taken, "canon_id = {$taken_canon}");
            redirect ("misc.php?action=canons");
        }

        // vergebenen Canon wieder freigeben
        $nottaken_canon = (int)$mybb->input['nottaken_canon'];
        if($nottaken_canon) {
            $canon_taken = array(
                "canon_taken" => 0
            );
            $db->update_query("canons", $canon_taken, "canon_id = {$nottaken_canon}");
            redirect ("misc.php?action=canons");
        }


        // Canon editieren
        if(isset($mybb->input['edit_canon'])) {

            $canon_id = $mybb->input['canon_id'];

            $editcanon = array(
                "canon_name" => $db->escape_string($mybb->input['canon_name']),
                "canon_age" => (int)$mybb->input['canon_age'],
                "canon_blood" => $db->escape_string($mybb->input['canon_blood']),
                "canon_gender" => $db->escape_string($mybb->input['canon_gender']),
                "canon_group" => $db->escape_string($mybb->input['canon_group']),
                "canon_def" => $db->escape_string($mybb->input['canon_def']),
                "canon_else" => $db->escape_string($mybb->input['canon_else']),
                "canon_avatar" => $db->escape_string($mybb->input['canon_avatar']),
                "canon_desc" => $db->escape_string($mybb->input['canon_desc']),
                "canon_pic" => $db->escape_string($mybb->input['canon_pic']),
            );

            $db->update_query("canons", $editcanon, "canon_id = '".$canon_id."'");
            redirect ("misc.php?action=canons");
        }

        // Canon löschen
        $deletecanon = $mybb->input['delete_canon'];
        if($deletecanon){
            $db->delete_query("canons" , "canon_id = '".$deletecanon."'");
            redirect ("misc.php?action=canons");
        }

        // Using the misc_help template for the page wrapper
        eval("\$page = \"".$templates->get("canons")."\";");
        output_page($page);
    }


}

// Team eine Alert anzeigen, wenn es neue Canons gibt, welche noch nicht abgesegnet sind

$plugins->add_hook('global_start', 'canons_alert');

// In the body of your plugin
function canons_alert()
{
    global $mybb, $templates, $lang, $db, $alert_canons;
    $lang->load('canons');

    $new_canons = $db->fetch_array($db->query("SELECT count(*) as count_canons
    FROM ".TABLE_PREFIX."canons
    where canon_admin = 0
"));


    if($new_canons['count_canons'] > 0 && $mybb->usergroup['canmodcp'] == 1){
        eval("\$alert_canons = \"".$templates->get("canons_alert")."\";");
    }
}

// modcp verwaltung


$plugins->add_hook("modcp_nav", "canons_modcp_nav");


function canons_modcp_nav(){
    global $canons_modcp, $lang;
    //Die Sprachdatei
    $lang->load('canons');
    $canons_modcp = "<tr><td class=\"trow1 smalltext\"><a href=\"modcp.php?action=canons\" class=\"modcp_nav_item modcp_nav_banning\">{$lang->canon_modcp_nav}</a></td></tr>";
}


$plugins->add_hook('modcp_start', 'canons_modcp');

// In the body of your plugin
function canons_modcp()
{
    global $mybb, $templates, $lang, $header, $headerinclude, $theme, $footer,$group, $page, $db, $parser, $options, $modcp_nav,$pmhandler;
    $lang->load('canons');
    $lang->load('modcp');
    require_once MYBB_ROOT."inc/datahandlers/pm.php";
    $pmhandler = new PMDataHandler();
    ///der Parser halt
    require_once MYBB_ROOT."inc/class_parser.php";;
    $parser = new postParser;
    // Do something, for example I'll create a page using the hello_world_template
    $options = array(
        "allow_html" => 1,
        "allow_mycode" => 1,
        "allow_smilies" => 1,
        "allow_imgcode" => 1,
        "filter_badwords" => 0,
        "nl2br" => 1,
        "allow_videocode" => 0
    );



    add_breadcrumb($lang->nav_modcp, "modcp.php");
    // Canons ausgeben :D
    if($mybb->get_input('action') == 'canons') {
        add_breadcrumb($lang->canons_nav_all, "modcp.php?action=canons");


        $all_new_canons = $db->query("SELECT *
        FROM " . TABLE_PREFIX . "canons
        where canon_admin = 0
        order by canon_name ASC
        ");

        while ($row = $db->fetch_array($all_new_canons)) {
            $canonname = "";
            $canonage = "";
            $canondef = "";
            $canondesc = "";
            $canongender = "";
            $canongroup = "";
            $canonelse = "";
            $canonpic = "";
            $canonreserved = "";
            $canontaken = "";
            $canon_reserv = "";
            $canonblood = "";

            $canonname = $row['canon_name'];
            $canonage = $row['canon_age'] . " Jahre";
            $canongender = $lang->canon_between . $row['canon_gender'];
            $canonblood = $lang->canon_between . $row['canon_blood'];
            if (!empty($row['canon_else'])) {
                $canonelse = $lang->canon_between . $row['canon_else'];
            }
            $canongroup = $lang->canon_between . $row['canon_group'];
            if (!empty($row['canon_def'])) {
                $canondef = $lang->canon_between . $row['canon_def'];
            }
            $canondesc = $parser->parse_message($row['canon_desc'], $options);
            $canonpic = $row['canon_pic'];
            $canonavatar = $row['canon_avatar'];

            eval("\$canon_reject = \"" . $templates->get("canons_modcp_reject") . "\";");
            eval("\$canons_modcp_bit .= \"" . $templates->get("canons_modcp_bit") . "\";");
        }


        // Canon accept
        $canon_accept = $mybb->input['accept_canon'];
        if ($canon_accept) {
            $accept_canon = array(
                "canon_admin" => 1
            );
            $db->update_query("canons", $accept_canon, "canon_id = '" . $canon_accept . "'");
            redirect("modcp.php?action=canons");
        }

        // Canon ablehnen

        if (isset($mybb->input['reject_canon'])) {
            $canon_id = $mybb->input['canon_id'];
            $canon_reject = $db->fetch_array($db->query("SELECT * FROM " . TABLE_PREFIX . "canons 
        where canon_id = '" .$canon_id . "' "));

            $touid = $canon_reject['canon_creator'];
            $fromuid = $mybb->user['uid'];

            $canonname = $canon_reject['canon_name'];
            $canonage = $canon_reject['canon_age'] . " Jahre";
            $canongender = $lang->canon_between . $canon_reject['canon_gender'];
            $canonblood = $lang->canon_between . $canon_reject['canon_blood'];
            if (!empty($row['canon_else'])) {
                $canonelse = $lang->canon_between . $canon_reject['canon_else'];
            }
            $canongroup = $lang->canon_between . $canon_reject['canon_group'];
            if (!empty($row['canon_def'])) {
                $canondef = $lang->canon_between . $canon_reject['canon_def'];
            }
            $canondesc = $parser->parse_message($canon_reject['canon_desc'], $options);
            $canonpic = $canon_reject['canon_pic'];
            $canonavatar = $canon_reject['canon_avatar'];


            $pm_change = array(
                "subject" => "{$lang->canon_reject_pm}",
                "message" => "Dein Canon wurde vom Team abgelehnt. Folgende Begründung wurde angegeben: <br />
                        {$mybb->input['canon_reason']}<br />
                        Dieser Canoneintrag wurde abgelehnt:<br />
                        <div class='canon_name'>{$canonname}</div>
		<div class='canon_avatar'>{$lang->canon_lookslike} <b>{$canonavatar}</b></div>
		<div class='canon_infos'>
			{$canonage} {$canongender} {$canonblood} {$canonelse}  {$canondef} {$canongroup}
		</div>
	<div class='canon_desc'>
		{$canondesc}
		</div>
                        ",
                //to: wer muss die anfrage bestätigen
                "fromid" => $fromuid,
                //from: wer hat die anfrage gestellt
                "toid" => $touid
            );
            // $pmhandler->admin_override = true;
            $pmhandler->set_data($pm_change);
            if (!$pmhandler->validate_pm())
                return false;
            else {
                $pmhandler->insert_pm();
            }

            $db->delete_query("canons", "canon_id = '".$canon_id."'");
            redirect ("modcp.php?action=canons");
        }
        // Using the misc_help template for the page wrapper
        eval("\$page = \"".$templates->get("canons_modcp")."\";");
        output_page($page);
    }


}
