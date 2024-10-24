<?php 

define('ACCESS', true);

    include_once 'function.php';

    
        $title = 'Delete Folder';

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

            if (isset($_POST['accept'])) {
                if (!rrmdir($dir . '/' . $name))
                    echo '<div class="notice_failure">Delete Folder failure</div>';
                else
                    goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
            } else if (isset($_POST['not_accept'])) {
                goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
            }

            echo '<div class="list">
                <span>Bạn có thực sự muốn Delete Folder <strong class="folder_name_delete">' . $name . '</strong> Are not?</span><hr/><br/>
                <center>
                    <form action="folder_delete.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
                        <input type="submit" name="accept" value="Agree "/>
                        <input type="submit" name="not_accept" value="Cancel"/>
                    </form>
                </center>
            </div>
            <div class="title">Function</div>
            <ul class="list">
                
                <li><img src="icon/rename.png"/> <a href="folder_edit.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Rename</a></li>
                <li><img src="icon/copy.png"/> <a href="folder_copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Copy</a></li>
                <li><img src="icon/Copy.png"/> <a href="folder_Copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Move</a></li>
                <li><img src="icon/access.png"/> <a href="folder_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Chmod</a></li>
                <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">List</a></li>
            </ul>';
        }

        include_once 'footer.php';
    

?>