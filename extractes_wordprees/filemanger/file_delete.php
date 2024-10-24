<?php 


define('ACCESS', true);

    include_once 'function.php';

    
        $title = 'Delete File';

        include_once 'header.php';

        echo '<div class="title">' . $title . '</div>';

        if ($dir == null || $name == null || !is_file(processDirectory($dir . '/' . $name))) {
            echo '<div class="list"><span>The path does not exist</span></div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">List</a></li>
            </ul>';
        } else {
            $dir = processDirectory($dir);
            $format = getFormat($name);

            if (isset($_POST['accept'])) {
                if (!@unlink($dir . '/' . $name))
                    echo '<div class="notice_failure">Delete File failure</div>';
                else
                    goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
            } else if (isset($_POST['not_accept'])) {
                goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
            }

            echo '<div class="list">
                <span>' . printPath($dir . '/' . $name) . '</span><hr/>
                <span>Do you really want to Delete File <strong class="file_name_delete">' . $name . '</strong> Are not?</span><hr/><br/>
                <center>
                    <form action="file_delete.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
                        <input type="submit" name="accept" value="Agree "/>
                        <input type="submit" name="not_accept" value="Cancel"/>
                    </form>
                </center>
            </div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/info.png"/> <a href="file.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Information</a></li>';

                if (isFormatText($name)) {
                    echo '<li><img src="icon/edit.png"/> <a href="edit_text.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Edit text</a></li>';
                    echo '<li><img src="icon/edit_text_line.png"/> <a href="edit_text_line.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Edit by line</a></li>';
                } else if (in_array($format, $formats['zip'])) {
                    echo '<li><img src="icon/unzip.png"/> <a href="file_viewzip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">See</a></li>';
                    echo '<li><img src="icon/unzip.png"/> <a href="file_unzip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Solve Compressed source</a></li>';
                } else if (isFormatUnknown($name)) {
                    echo '<li><img src="icon/edit.png"/> <a href="edit_text.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Edit text format</a></li>';
                }

                echo '<li><img src="icon/download.png"/> <a href="file_download.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Download</a></li>
                <li><img src="icon/rename.png"/> <a href="file_rename.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Rename</a></li>
                <li><img src="icon/copy.png"/> <a href="file_copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Copy</a></li>
                <li><img src="icon/Copy.png"/> <a href="file_Copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Move</a></li>
                <li><img src="icon/access.png"/> <a href="file_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Chmod</a></li>
                <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">List</a></li>
            </ul>';
        }

        include_once 'footer.php';
    

?>