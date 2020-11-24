<?PHP
/*
 * Script that is run to carry out support tasks for the Unraid.FileManager plugin.
 *
 * Copyright 2019-2020, Dave Walker (itimpi).
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * Limetech is given explicit permission to use this code in any way they like.
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

// useful for testing outside Gui
$docroot = $docroot ?? $_SERVER['DOCUMENT_ROOT'] ?: '/usr/local/emhttp';

require_once '/usr/local/emhttp/webGui/include/Helpers.php';

$plugin = 'Unraid.FileManager';
$dirBoot = "/boot/config/plugins/$plugin";
$dirRam = "/usr/local/emhttp/plugins/$plugin";
$fileSettings = "$dirBoot/fileManagerSettings.cfg";
$fileDefaults = "$dirRam/fileManagerDefaults.cfg";
// INFO:    Not sure why but this needs to be relative to /Tools/FileManager
$filePhp      = "$dirRam/plugins/$plugin/fileManagerSettings.php";
$fileCurrent  = '/usr/local/emhttp/filemanager/config.inc.php';
$var = parse_ini_file('/var/local/emhttp/var.ini');

if (! file_exists($fileSettings))  {
    copy($fileDefaults, $fileSettings);
    fileManagerLogger("No saved settings, so set to plugin defaults");
} else {
    fileManagerLoggerDebug("Using saved settings");
}

$current = file_get_contents($fileSettings);
// $current = preg_replace(["/\r\n/","/\r/","/\n$/"],["\n","\n",""],$current);
// Ensure current settings are starting point for fileManager
copy ($fileSettings, $fileCurrent);

# Write message to syslog
function fileManagerLogger($string) {
  global $argv;
  $string = str_replace("'","",$string);
  shell_exec('logger -t "FileManager" "' . $string . '"');
}

# Write message to syslog if debug logging active
function fileManagerLoggerDebug($string) {
    // ***** Comment out next line if debug logging not required *****
    fileManagerLogger("DEBUG: " . $string);
}

# Write message to syslog if testing logging active
function fileManagerLoggerTesting($string) {
    // ***** Comment out next line if testing logging not required *****
    fileManagerLogger("TESTING: " . $string);
}

/**
 * Create an ini string suitable for writing to a configuration file
 *
 * @param array  $array
 * @return string or bool
 */
function create_ini_string($array = []) {
	// check argument is array
	if (!is_array($array)) {
		throw new \InvalidArgumentException('Function argument must be an array.');
	}
	// process array
	$data = array();
	foreach ($array as $key => $val) {
		if (is_array($val)) {
			$data[] = "[$key]";
			foreach ($val as $skey => $sval) {
				if (is_array($sval)) {
					foreach ($sval as $_skey => $_sval) {
						if (is_numeric($_skey)) {
							$data[] = $skey.'[] = '.(is_numeric($_sval) ? $_sval : (ctype_upper($_sval) ? $_sval : '"'.$_sval.'"'));
						} else {
							$data[] = $skey.'['.$_skey.'] = '.(is_numeric($_sval) ? $_sval : (ctype_upper($_sval) ? $_sval : '"'.$_sval.'"'));
						}
					}
				} else {
					$data[] = $skey.' = '.(is_numeric($sval) ? $sval : (ctype_upper($sval) ? $sval : '"'.$sval.'"'));
				}
			}
		} else {
			$data[] = $key.' = '.(is_numeric($val) ? $val : (ctype_upper($val) ? $val : '"'.$val.'"'));
		}
		// empty line
		$data[] = null;
	}
	return (implode(PHP_EOL, $data).PHP_EOL);
}

// Useful comparison functions

function startsWith($haystack, $beginning, $caseInsensitivity = false){
    if ($caseInsensitivity)
        return strncasecmp($haystack, $beginning, strlen($beginning)) === 0;
    else
        return strncmp($haystack, $beginning, strlen($beginning)) === 0;
}

function endsWith($haystack, $ending, $caseInsensitivity = false){
    if ($caseInsensitivity)
        return strcasecmp(substr($haystack, strlen($haystack) - strlen($ending)), $haystack) === 0;
    else
        return strpos($haystack, $ending, strlen($haystack) - strlen($ending)) !== false;
}
?>
