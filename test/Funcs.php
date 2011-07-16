<?php

require '../AppLoader.php';


/**
 *
 * @param string $dirName
 * @return bool
 */
function delDirectory($dirName) {
    if ( !is_dir($dirName) ) {
        return false;
    }

    $h = opendir($dirName);
    if ( !$h ) {
        return false;
    }

    while($file = readdir($h)) {
        if ($file != "." && $file != "..") {
            if (!is_dir($dirName."/".$file)) {
                unlink($dirName."/".$file);
            }else {
                delDirectory($dirName.'/'.$file);
            }
        }
    }

    closedir($h);
    rmdir($dirName);
    return true;
}

function resetLucene() {
    $dataPath = APPLICATION_PATH.'/../data';

    //SearchIndex::_release();
    delDirectory($dataPath.'/lucene_testing/OB');
    delDirectory($dataPath.'/lucene_testing/MW');
}

function resetAll() {
    resetLucene();
}

?>
