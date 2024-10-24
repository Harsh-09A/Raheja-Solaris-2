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


    include_once 'function.php';
    
    
        $title = 'extract zip Folder';

        include_once 'header.php';

        echo '<div class="title">' . $title . '</div>';

        if ($dir == null || $name == null || !is_dir(processDirectory($dir . '/' . $name))) {
            echo '<div class="list"><span>The path does not exist</span></div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">List</a></li>
            </ul>';
        } else {
            $dir = processDirectory($dir);

            if (isset($_POST['submit'])) {
                echo '<div class="notice_failure">';

                if (empty($_POST['name']) || empty($_POST['path']))
                    echo 'Not fully entered information';
                else if (isset($_POST['is_delete']) && processDirectory($_POST['path']) == $dir . '/' . $name)
                    echo 'If you choose Delete Folder, you cannot save the Compressed source file there';
                else if (isPathNotPermission(processDirectory($_POST['path'])))
                    echo 'You cannot Compressed source File zip to File Manager s path';
                else if (isNameError($_POST['name']))
                    echo 'File name zip illegal';
                else if (!zipdir($dir . '/' . $name, processDirectory($_POST['path'] . '/' . processName($_POST['name'])), isset($_POST['is_delete']) == 1))
                    echo 'extract zip Folder failure';
                else
                    goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);

                echo '</div>';
            }

            echo '<div class="list">
                <span class="bull">&bull; </span><span>' . printPath($dir . '/' . $name, true) . '</span><hr/>
                <form action="folder_zip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
                    <span class="bull">&bull; </span>Compressed file name:<br/>
                    <input type="text" name="name" value="' . (isset($_POST['name']) ? $_POST['name'] : $name . '.zip') . '" size="18"/><br/>
                    <span class="bull">&bull; </span>Save path:<br/>
                    <input type="text" name="path" value="' . (isset($_POST['path']) ? $_POST['path'] : $dir) . '" size="18"/><br/>
                    <input type="checkbox" name="is_delete" value="1"/> Delete Folder<br/>
                    <input type="submit" name="submit" value="Compressed source"/>
                </form>
            </div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/rename.png"/> <a href="folder_edit.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Rename</a></li>
                <li><img src="icon/copy.png"/> <a href="folder_copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Copy</a></li>
                <li><img src="icon/Copy.png"/> <a href="folder_Copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Move</a></li>
                <li><img src="icon/delete.png"/> <a href="folder_delete.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Delete</a></li>
                <li><img src="icon/access.png"/> <a href="folder_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Chmod</a></li>
                <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">List</a></li>
            </ul>';
        }

        include_once 'footer.php';
    

?>