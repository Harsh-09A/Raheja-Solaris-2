<?php 



define('ACCESS', true);

    include_once 'function.php';

    
        $title = 'Edit File by line';
        $page = array('current' => 0, 'total' => 1, 'paramater_0' => null, 'paramater_1' => null);
        $page['current'] = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $page['current'] = $page['current'] <= 0 ? 1 : $page['current'];

        include_once 'header.php';

        echo '<div class="title">' . $title . '</div>';

        if ($dir == null || $name == null || !is_file(processDirectory($dir . '/' . $name))) {
            echo '<div class="list"><span>The path does not exist</span></div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">List</a></li>
            </ul>';
        } else if (!isFormatText($name) && !isFormatUnknown($name)) {
            echo '<div class="list"><span>This file is not in text format</span></div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">List</a></li>
            </ul>';
        } else {
            if ($page['current'] > 1 && $configs['page_file_edit_line'] > 0) {
                $page['paramater_0'] = '?page=' . $page['current'];
                $page['paramater_1'] = '&page=' . $page['current'];
            }

            $path = $dir . '/' . $name;
            $content = file_get_contents($path);
            $lines = array();
            $count = 0;
            $start = 0;
            $end = 0;

            if (strlen($content) > 0) {
                $content = str_replace("\r\n", "\n", $content);
                $content = str_replace("\r", "\n", $content);

                if (strpos($content, "\n") !== false) {
                    $lines = explode("\n", $content);
                    $count = count($lines);
					$configs = array('page_file_edit_line' => 500);
                    if ($configs['page_file_edit_line'] > 0)
                        $page['total'] = ceil($count / $configs['page_file_edit_line']);
                } else {
                    $lines[] = $content;
                    $count = 1;
                }
            } else {
                $lines[] = $content;
                $count = 1;
            }

            if ($configs['page_file_edit_line'] > 0) {
                $start = ($page['current'] * $configs['page_file_edit_line']) - $configs['page_file_edit_line'];
                $end = $start + $configs['page_file_edit_line'] > $count - 1 ? $count : $start + $configs['page_file_edit_line'];
            } else {
                $start = 0;
                $end = $count;
            }

            if ($page['current'] < 0 && $configs['page_file_edit_line'] > 0)
                goURL('edit_text_line.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1']);

            if ($page['current'] > $page['total'] && $configs['page_file_edit_line'] > 0)
                goURL('edit_text_line.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . ($page['total'] > 1 ? '&page=' . $page['total'] : null));

            echo '<div class="list">
                <span class="bull">&bull; </span><span>' . printPath($dir, true) . '</span><hr/>
                <div class="ellipsis break-word">
                    <span class="bull">&bull; </span>File: <strong class="file_name_edit">' . $name . '</strong>
                </div>
            </div>
            <div class="list_line">';

            for ($i = $start; $i < $end; ++$i) {
                echo '<div id="line">
                    <div id="line_number_' . $i . '">' . htmlspecialchars($lines[$i]) . '</div>
                    <div>
                        <span id="line_number">[<span>' . $i . '</span>]</span>
                        <a href="edit_line.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '&line=' . $i . $page['paramater_1'] . '">Fix</a>
                        <span> | </span>
                        <a href="delete_line.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '&line=' . $i . $page['paramater_1'] . '">Delete</a>
                    </div>
                </div>';
            }

            if ($page['total'] > 1 && $configs['page_file_edit_line'] > 0)
                echo page($page['current'], $page['total'], array(PAGE_URL_DEFAULT => 'edit_text_line.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'], PAGE_URL_START => 'edit_text_line.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '&page='));

            echo '</div>
            <div class="tips">
                <img src="icon/tips.png"/>
                <span>It is recommended that you Edit text format, this type of Fix handles quite a lot in one request</span>
            </div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/edit.png"/> <a href="edit_text.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Edit text</a></li>
                <li><img src="icon/download.png"/> <a href="file_download.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Download</a></li>
                <li><img src="icon/info.png"/> <a href="file.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Information</a></li>
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