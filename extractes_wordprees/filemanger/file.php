<?php 


define('ACCESS', true);

    include_once 'function.php';

    
        $title = 'Information File';

        include_once 'header.php';

        echo '<div class="title">' . $title . '</div>';

        if ($dir == null || $name == null || !is_file(processDirectory($dir . '/' . $name))) {
            echo '<div class="list"><span>The path does not exist</span></div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_1'] . '">List</a></li>
            </ul>';
        } else {
            $dir = processDirectory($dir);
            $path = $dir . '/' . $name;
            $format = getFormat($name);
            $isImage = false;
            $pixel = null;

            echo '<ul class="info">';
            echo '<li class="not_ellipsis"><span class="bull">&bull; </span><strong>Path</strong>: <span>' . printPath($dir, true) . '</span></li>';

                if ($format != null && in_array($format, array('png', 'ico', 'jpg', 'jpeg', 'gif', 'bmp'))) {
                    $pixel = getimagesize($path);
                    $isImage = true;

                    echo '<li><center><img src="read_image.php?path=' . rawurlencode($path) . '" width="' . ($pixel[0] > 200 ? 200 : $pixel[0]) . 'px"/></center><br/></li>';
                }

                echo '<li><span class="bull">&bull; </span><strong>Name</strong>: <span>' . $name . '</span></li>
                <li><span class="bull">&bull; </span><strong>Size</strong>: <span>' . size(filesize($path)) . '</span></li>
                <li><span class="bull">&bull; </span><strong>Chmod</strong>: <span>' . getChmod($path) . '</span></li>';

                if ($isImage)
                    echo '<li><span class="bull">&bull; </span><strong>Resolution</strong>: <span>' . $pixel[0] . 'x' . $pixel[1] . '</span></li>';

                echo '<li><span class="bull">&bull; </span><strong>Format</strong>: <span>' . ($format == null ? 'Unclear' : $format) . '</span></li>
                <li><span class="bull">&bull; </span><strong>Edit date</strong>: <span>' . @date('d.m.Y - H:i', filemtime($path)) . '</span></li>
            </ul>
            <div class="title">Function</div>
            <ul class="list">';

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
                <li><img src="icon/delete.png"/> <a href="file_delete.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Delete</a></li>
                <li><img src="icon/access.png"/> <a href="file_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Chmod</a></li>
                <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">List</a></li>
            </ul>';
        }

        include_once 'footer.php';
    

?>