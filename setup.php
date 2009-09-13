<?php
$_folders = array();
$_folders[_CACHEDIR . '/weather'] = 'Weather Cache Directory';

foreach($_folders as $folder => $name) {
    $res = Jojo::RecursiveMkdir($folder);
    if ($res === true) {
        echo "Created folder: $name ($folder)<br/>";
    } elseif($res === false) {
        echo "Could not automatically create $folder folder on the server. Please create this folder and assign 777 permissions.";
    }
}
