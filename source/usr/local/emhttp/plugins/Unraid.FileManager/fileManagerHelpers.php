<?PHP
/*
 * Script that is run to carry out support tasks for the Unraid.FileManager plugin.
 *
 * Copyright 2019, Dave Walker (itimpi).
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * Limetech is given expliit permission to use this code in any way they like.
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */
 
require_once '/usr/local/emhttp/webGui/include/Helpers.php';

$version=parse_ini_file('/etc/unraid-version');
if (substr($version['version'],0,3) < 6.7) echo "<p class='notice'>Requires Unraid 6.7 or later</p>";

$plugin = 'Unraid.FileManager';
$dirBoot = "/boot/config/plugins/$plugin";
$dirRam = "/usr/local/emhttp/plugins/$plugin";
$fileSettings = "$dirBoot/fileManagerSettings.cfg";
$fileDefaults = "$dirRam/fileManagerDefaults";
// INFO:    Not sure why but this needs to be relative to /Tools/FileManager
$filePhp      = "../../plugins/$plugin/fileManagerSettings.php";
$fileCurrent  = '/usr/local/emhttp/filemanager/config.inc.php';

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
  $string = str_replace("'","",$string);
  shell_exec('logger -t "FileManager" "' . $string . '"');
}

# Write message to syslog if debug logging active
function fileManagerLoggerDebug($string) {
    // ***** Comment out next line if debug logging not required *****
    fileManagerLogger("DEBUG: " . $string);
}

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