<?php	

define('ACCESS', true);

    include_once 'function.php';

    
        $title = 'Download File';

        if ($dir == null || $name == null || !is_file(processDirectory($dir . '/' . $name))) {
            include_once 'header.php';

            echo '<div class="title">' . $title . '</div>';
            echo '<div class="list"><span>The path does not exist</span></div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/list.png"/> <a href="index.php">List</a></li>
            </ul>';

            include_once 'footer.php';
        } else {
            $dir = processDirectory($dir);
            $path = $dir . '/' . $name;

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: inline; filename=' . $name);
            header('Content-Length: ' . filesize($path));
            readfile($path);
        }
    

?>