<?php 
function customExceptionHandler($exception) {
    
    echo '<div style="font-family: Arial, sans-serif; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24;">';
    echo '<strong>ERROR:</strong> This feature is not available';
    echo '</div>';

}

function customErrorHandler($errno, $errstr, $errfile, $errline) {

    echo '<div style="font-family: Arial, sans-serif; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24;">';
    echo '<strong>ERROR:</strong> This feature is not available';
    echo '</div>';

}


set_exception_handler('customExceptionHandler');
set_error_handler('customErrorHandler');
define('ACCESS', true);

    include_once 'function.php';

    
        $title = 'Solve Compressed source File';
        $format = $name == null ? null : getFormat($name);

        include_once 'header.php';

        echo '<div class="title">' . $title . '</div>';

        if ($dir == null || $name == null || !is_file(processDirectory($dir . '/' . $name))) {
            echo '<div class="list"><span>The path does not exist</span></div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">List</a></li>
            </ul>';
        } else if (!in_array($format, array('zip', 'jar'))) {
            echo '<div class="list"><span>File không phải zip</span></div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">List</a></li>
            </ul>';
        } else {
            $dir = processDirectory($dir);
            $format = getFormat($name);

            if (isset($_POST['submit'])) {
                echo '<div class="notice_failure">';

                if (empty($_POST['path'])) {
                    echo 'Not fully entered information';
                } else if (!is_dir(processDirectory($_POST['path']))) {
                    echo 'Path Solve Compressed source does not exist';
                } else if (isPathNotPermission(processDirectory($_POST['path']))) {
                    echo "You cannot Solve Compressed source File zip to File Manager's Path";
                } else {
                    

                    $zip = new PclZip($dir . '/' . $name);

                    function callback_pre_extract($event, $header)
                    {
                        return isPathNotPermission($header['filename']) == false ? 1 : 0;
                    }

                    if ($zip->extract(PCLZIP_OPT_PATH, processDirectory($_POST['path']), PCLZIP_CB_PRE_EXTRACT, 'callback_pre_extract') != false) {
                        if (isset($_POST['is_delete']))
                            @unlink($dir . '/' . $name);

                        goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
                    } else {
                        echo 'Solve Compressed source File error';
                    }
                }

                echo '</div>';
            }

            echo '<div class="list">
                <span class="bull">&bull;</span><span>' . printPath($dir . '/' . $name) . '</span><hr/>
                <form action="file_unzip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
                    <span class="bull">&bull;</span>Path Solve Compressed source:<br/>
                    <input type="text" name="path" value="' . (isset($_POST['path']) ? $_POST['path'] : $dir) . '" size="18"/><br/>
                    <input type="checkbox" name="is_delete" value="1"' . (isset($_POST['is_delete']) ? ' checked="checked"' : null) . '/> Delete File zip<br/>
                    <input type="submit" name="submit" value="Solve Compressed source"/>
                </form>
            </div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/info.png"/> <a href="file.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Information</a></li>
                <li><img src="icon/unzip.png"/> <a href="file_viewzip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">See</a></li>
                <li><img src="icon/download.png"/> <a href="file_download.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Download</a></li>
                <li><img src="icon/rename.png"/> <a href="file_rename.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Rename</a></li>
                <li><img src="icon/copy.png"/> <a href="file_copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Copy</a></li>
                <li><img src="icon/Copy.png"/> <a href="file_Copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Move</a></li>
                <li><img src="icon/delete.png"/> <a href="file_delete.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Delete</a></li>
                <li><img src="icon/access.png"/> <a href="file_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Chmod</a></li>
                <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">List</a></li>
            </ul>';
        }

        include_once 'footer.php';
    

?>