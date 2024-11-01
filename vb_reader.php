<?php
/*
Plugin Name: vBulletin Reader
Plugin URI: http://www.minimal-software.com.ar
Description: This plugin lists last updated threads of a vBulletin's DB into Wordpress.
Version: 0.1
Author: Esteban Rodriguez Nieto [PL|MAD666]
Author URI: http://www.minimal-software.com.ar
*/

function ShowThreads() {
	
	if (!is_home()) return; // Shows threads only in homepage
	
	// Options
	$vbrTitle = 'vbr_title';
	$vbrURL = 'vbr_url';
	$vbrVer = 'vbr_ver';
	$vbrHost = 'vbr_host';
	$vbrDB = 'vbr_db';
	$vbrUser = 'vbr_user';
	$vbrPassword = 'vbr_password';
	$vbrCharset = 'vbr_charset';
	$vbrThreads = 'vbr_threads';
	$vbrTrim = 'vbr_trim';
	
	// Reads options values from DB
	$vbrTitleVal = get_option($vbrTitle);
	$vbrURLVal = get_option($vbrURL);
	$vbrVerVal = get_option($vbrVer);
	$vbrHostVal = get_option($vbrHost);
	$vbrDBVal = get_option($vbrDB);
	$vbrUserVal = get_option($vbrUser);
	$vbrPasswordVal = get_option($vbrPassword);
	$vbrCharsetVal = get_option($vbrCharset);
	$vbrThreadsVal = get_option($vbrThreads);
	$vbrTrimVal = get_option($vbrTrim);

	$link = mysql_connect($vbrHostVal, $vbrUserVal, $vbrPasswordVal); // DB connection
	$db = mysql_select_db($vbrDBVal, $link);                          // Selects DB
	
	if (!$link || !$db) { // Something went wrong :(

		echo('<br />');
		_e('Can\'t connect to the database. MySQL error: ');
		
		echo(mysql_error() . '<br />'); // Bug: mysql_error() doesn't always work. I don't know why :(
		
		_e('Please review the configuration of vBulletin Reader plugin in the Control Panel.');
		echo('<br />');
	
		return;
		
		}

	echo('<div><h2>' . $vbrTitleVal . '</h2><br/><ul>'); // Main title

	mysql_query("set names '$vbrCharsetVal'", $link); // Sets DB's character set
		
	$threads = mysql_query('SELECT title, threadid, lastpost FROM thread ORDER BY lastpost DESC LIMIT ' . $vbrThreadsVal); // Gets threads

	while ($threadstop = mysql_fetch_array($threads)) { // List loooooop :)

		$maxlength = strlen($threadstop['title']); // Trims thread's title

		if ($maxlength > $vbrTrimVal) $threadstop['title'] = substr($threadstop['title'], 0, $vbrTrimVal - 3) . "..."; // Adds ...

		if ($vbrVerVal == "2") $parameters = "threadid=";      // vBulletin 2.x parameter
		if ($vbrVerVal == "3") $parameters = "got=newpost&t="; // vBulletin 3.x parameters

		echo('<li>&nbsp; <a href="' . $vbrURLVal . 'showthread.php?' . $parameters . $threadstop['threadid'] . '" target="blank">' . $threadstop['title'] . '</a></li>'); // Last $vbrTrimVal updated threads

		}

	echo('</ul></div>');

}

function vbReaderMenu() {
	
	add_options_page('vBulletin Reader Options', 'vBulletin Reader', 8, __FILE__, 'vbReaderOptions');

}

function vbReaderOptions() {
	
	// Options
	$vbrTitle = 'vbr_title';
	$vbrURL = 'vbr_url';
	$vbrVer = 'vbr_ver';
	$vbrHost = 'vbr_host';
	$vbrDB = 'vbr_db';
	$vbrUser = 'vbr_user';
	$vbrPassword = 'vbr_password';
	$vbrCharset = 'vbr_charset';
	$vbrThreads = 'vbr_threads';
	$vbrTrim = 'vbr_trim';
	
	// Form fields
	$vbrTitleFieldName = 'vbr_title_field';
	$vbrURLFieldName = 'vbr_url_field';
	$vbrVerFieldName = 'vbr_ver_field';
	$vbrHostFieldName = 'vbr_host_field';
	$vbrDBFieldName = 'vbr_db_field';
	$vbrUserFieldName = 'vbr_user_field';
	$vbrPasswordFieldName = 'vbr_password_field';
	$vbrCharsetFieldName = 'vbr_charset_field';
	$vbrThreadsFieldName = 'vbr_threads_field';
	$vbrTrimFieldName = 'vbr_trim_field';
	$vbrHiddenField = 'vbr_hidden';

	if ($_POST[$vbrHiddenField] == '1') { // Updates options

    	update_option($vbrTitle, $_POST[$vbrTitleFieldName]);
    	update_option($vbrURL, $_POST[$vbrURLFieldName]);
    	update_option($vbrVer, $_POST[$vbrVerFieldName]);
		update_option($vbrHost, $_POST[$vbrHostFieldName]);
		update_option($vbrDB, $_POST[$vbrDBFieldName]);
		update_option($vbrUser, $_POST[$vbrUserFieldName]);
		update_option($vbrPassword, $_POST[$vbrPasswordFieldName]);
		update_option($vbrCharset, $_POST[$vbrCharsetFieldName]);
		update_option($vbrThreads, $_POST[$vbrThreadsFieldName]);
		update_option($vbrTrim, $_POST[$vbrTrimFieldName]);
       
		// Shows success message :)

?>

<div class="updated"><p><strong><?php _e('Options saved.'); ?></strong></p></div>
	
<?php

    	} // If

	// Reads options values from DB
	$vbrTitleVal = get_option($vbrTitle);
	$vbrURLVal = get_option($vbrURL);
	$vbrVerVal = get_option($vbrVer);
	$vbrHostVal = get_option($vbrHost);
	$vbrDBVal = get_option($vbrDB);
	$vbrUserVal = get_option($vbrUser);
	$vbrPasswordVal = get_option($vbrPassword);
	$vbrCharsetVal = get_option($vbrCharset);
	$vbrThreadsVal = get_option($vbrThreads);
	$vbrTrimVal = get_option($vbrTrim);

    echo('<div class="wrap">');
    echo('<h2>' . __('vBulletin Reader options') . '</h2>'); // Title in the Control Panel

    // Rest of the form comes below...
    
    ?>

<form name="form1" method="post" action="<?php echo(str_replace( '%7E', '~', $_SERVER['REQUEST_URI'])); ?>">
<input type="hidden" name="<?php echo $vbrHiddenField; ?>" value="1">
<p>
<table witdh="100%" border="0">
<tr>
<td><?php _e('Main title (shown in home page)'); ?>&nbsp;</td>
<td><input type="text" name="<?php echo $vbrTitleFieldName; ?>" value="<?php echo($vbrTitleVal); ?>" class="regular-text code" /></td>
</tr>
<tr>
<td><?php _e('vBulletin\'s URL (remember to add the last /)'); ?>&nbsp;</td>
<td><input type="text" name="<?php echo $vbrURLFieldName; ?>" value="<?php echo($vbrURLVal); ?>" class="regular-text code" /></td>
</tr>
<tr>
<td><?php _e('vBulletin\'s version'); ?>&nbsp;</td>
<td> 
  <select name="<?php echo $vbrVerFieldName; ?>">
    <option value="2" <?php if ($vbrVerVal == "2") echo('selected="selected"'); ?>>2.x</option>
    <option value="3" <?php if ($vbrVerVal == "3") echo('selected="selected"'); ?>>3.x</option>
  </select>
</td>
</tr>
<tr>
<td><?php _e('MySQL host (in most cases this value is localhost)'); ?> &nbsp;</td>
<td><input type="text" name="<?php echo $vbrHostFieldName; ?>" value="<?php echo($vbrHostVal); ?>" class="regular-text code" /></td>
</tr>
<tr>
<td><?php _e('vBulletin\'s database name'); ?>&nbsp;</td>
<td><input type="text" name="<?php echo $vbrDBFieldName; ?>" value="<?php echo($vbrDBVal); ?>" class="regular-text code" /></td>
</tr>
<tr>
<td><?php _e('vBulletin\'s database username'); ?>&nbsp;</td>
<td><input type="text" name="<?php echo $vbrUserFieldName; ?>" value="<?php echo($vbrUserVal); ?>" class="regular-text code" /></td>
</tr>
<tr>
<td><?php _e('vBulletin\'s database password'); ?>&nbsp;</td>
<td><input type="password" name="<?php echo $vbrPasswordFieldName; ?>" value="<?php echo($vbrPasswordVal); ?>" class="regular-text code" /></td>
</tr>
<tr>
<td><?php _e('vBulletin\'s database charset (for example utf8)'); ?>&nbsp;</td>
<td><input type="text" name="<?php echo $vbrCharsetFieldName; ?>" value="<?php echo($vbrCharsetVal); ?>" class="regular-text code" /></td>
</tr>
<tr>
<td><?php _e('Number of threads to list'); ?>&nbsp;</td>
<td><input type="text" name="<?php echo $vbrThreadsFieldName; ?>" value="<?php echo($vbrThreadsVal); ?>" maxlength="3" class="regular-text code" /></td>
</tr>
<tr>
<td><?php _e('Max length of threads\' titles'); ?>&nbsp;</td>
<td><input type="text" name="<?php echo $vbrTrimFieldName; ?>" value="<?php echo($vbrTrimVal); ?>" maxlength="3" class="regular-text code" /></td>
</tr>
</table>
</p>
<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update options') ?>" class="button-primary" />
</p>
</form>
</div>

<?php
 
} // function vbReaderOptions()
		
add_action('loop_start', 'ShowThreads');
add_action('admin_menu', 'vbReaderMenu');

?>
