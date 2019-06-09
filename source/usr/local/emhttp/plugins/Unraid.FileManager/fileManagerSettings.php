<?PHP
/*
 * Script that is run to carry out support tasks for the Unraid.FileManader plugin.
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
 
require_once '/usr/local/emhttp/plugins/Unraid.FileManager/fileManagerHelpers.php';

$action = $_POST['action'];
fileManagerLoggerDebug("START:  action =$action");
switch ($action) {
  case 'undo':
    echo File_get_contents($fileSettings);
    fileManagerLogger('Current settings reloaded');
    break;
  case 'load':
    echo File_get_contents($fileSettings);
    fileManagerLogger('Current settings loaded');
    break;
  case 'save':
    $backupContents = file_get_contents($fileSettings);
    if (! $backupContents ) $backupContents = file_get_contents($fileDefaults);
    file_put_contents("$fileSettings.bak",$backupContents);
    fileManagerLoggerDebug("Backup of previous settings saved");
    $current = $_POST['filedata'];
    // $current = preg_replace(["/\r\n/","/\r/","/\n$/"],["\n","\n",""],$_POST['filedata']);
    fileManagerLoggerDebug('Data length:' . strlen($current));
    file_put_contents($fileSettings,$current);
    fileManagerLogger('Updated settings saved');
    copy ($fileSettings, $fileCurrent);
    fileManagerLoggerDebug('New settings activated');
    echo file_get_contents($fileSettings,$current);
    break;
  case 'defaults':   
    fileManagerLogger('Default settings loaded');
    echo File_get_contents($fileDefaults);
    break;
  default:
    fileManagerLogger("ERROR: action type '$action' not recognized");
    break;
}
fileManagerLoggerDebug("ENDED:  action =$action");
?>