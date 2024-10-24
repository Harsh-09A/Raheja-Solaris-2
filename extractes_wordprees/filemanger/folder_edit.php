<?php 

define('ACCESS', true);

    include_once 'function.php';

    
        $title = 'Rename Folder';

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

                if (empty($_POST['name']))
                    echo 'Not fully entered information';
                else if (isNameError($_POST['name']))
                    echo 'File name illegal';
                else if (!@rename($dir . '/' . $name, $dir . '/' . $_POST['name']))
                    echo 'Rename Folder failure';
                else
                    goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);

                echo '</div>';
            }

            echo '<div class="list">
                <span class="bull">&bull; </span><span>' . printPath($dir . '/' . $name, true) . '</span><hr/>
                <form action="folder_edit.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
                    <span class="bull">&bull; </span>Name Folder:<br/>
                    <input type="text" name="name" value="' . (isset($_POST['name']) ? $_POST['name'] : $name) . '" size="18"/><br/>
                    <input type="submit" name="submit" value="Rename"/>
                </form>
            </div>
            <div class="title">Function</div>
            <ul class="list">
                
                <li><img src="icon/copy.png"/> <a href="folder_copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Copy</a></li>
                <li><img src="icon/Copy.png"/> <a href="folder_Copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Move</a></li>
                <li><img src="icon/delete.png"/> <a href="folder_delete.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Delete</a></li>
                <li><img src="icon/access.png"/> <a href="folder_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Chmod</a></li>
                <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">List</a></li>
            </ul>';
        }

        include_once 'footer.php';
    

?>