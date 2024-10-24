<?php 


define('ACCESS', true);

    include_once 'function.php';

    
        $title = 'Move Folder';

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

                if (empty($_POST['path']))
                    echo 'Not fully entered information';
                else if ($dir == processDirectory($_POST['path']))
                    echo 'The new path must be different from the current path';
                else if (!is_dir($_POST['path']))
                    echo 'The new path does not exist';
                else if (isPathNotPermission(processDirectory($_POST['path'])))
                    echo "You cannot Move Folder to File Manager's Path";
                else if (isPathNotPermission(processDirectory($_POST['path'] . '/' . $name)))
                    echo "You cannot Move a Folder with the same Name to a Folder containing File Manager's Folder";
                else if (!Copydir($dir . '/' . $name, processDirectory($_POST['path'])))
                    echo 'Move Folder failure';
                else
                    goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);

                echo '</div>';
            }

            echo '<div class="list">
                <span class="bull">&bull; </span><span>' . printPath($dir . '/' . $name, true) . '</span><hr/>
                <form action="folder_Copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
                    <span class="bull">&bull; </span>Path Folder mới:<br/>
                    <input type="text" name="path" value="' . (isset($_POST['path']) ? $_POST['path'] : $dir) . '" size="18"/><br/>
                    <input type="submit" name="submit" value="Move"/>
                </form>
            </div>
            <div class="title">Function</div>
            <ul class="list">
                
                <li><img src="icon/rename.png"/> <a href="folder_edit.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Rename</a></li>
                <li><img src="icon/copy.png"/> <a href="folder_copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Copy</a></li>
                <li><img src="icon/delete.png"/> <a href="folder_delete.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Delete</a></li>
                <li><img src="icon/access.png"/> <a href="folder_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Chmod</a></li>
                <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">List</a></li>
            </ul>';
        }

        include_once 'footer.php';
    

?>