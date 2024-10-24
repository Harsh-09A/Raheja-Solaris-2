<?php

    const ACCESS = true;

    include_once 'function.php';

    // login
    

    $title = 'Create new';

    include_once 'header.php';

    echo '<div class="title">' . $title . '</div>';

    if ($dir == null || !is_dir(processDirectory($dir))) {
        echo '<div class="list"><span>The path does not exist</span></div>
                <div class="title">Function</div>
                <ul class="list">
                    <li><img src="icon/list.png" alt=""/> <a href="index.php' . $pages['paramater_0'] . '">List</a></li>
                </ul>';
    } else {
        $dir = processDirectory($dir);

        if (isset($_POST['submit'])) {
            echo '<div class="notice_failure">';

            if (empty($_POST['name'])) {
                echo 'Not fully entered information';
            } else if (intval($_POST['type']) === 0 && file_exists($dir . '/' . $_POST['name'])) {
                echo 'Name already exists in the form of Folder or File';
            } else if (intval($_POST['type']) === 1 && file_exists($dir . '/' . $_POST['name'])) {
                echo 'Name already exists in the form of Folder or File';
            } else if (isNameError($_POST['name'])) {
                echo 'Name illegal';
            } else {
                if (intval($_POST['type']) === 0) {
                    if (!@mkdir($dir . '/' . $_POST['name']))
                        echo 'Create Folder failure';
                    else
                        goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
                } else if (intval($_POST['type']) === 1) {
                    if (!@file_put_contents($dir . '/' . $_POST['name'], '...'))
                        echo 'Create File failure';
                    else
                        goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
                } else {
                    echo 'Choose illegal';
                }
            }

            echo '</div>';
        }

        echo '<div class="list">
                <span>' . printPath($dir, true) . '</span><hr/>
                <form action="create.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                    <span class="bull">&bull; </span>Folder or File name:<br/>
                    <input type="text" name="name" value="' . ($_POST['name'] ?? null) . '" size="18"/><br/>
                    <input type="radio" name="type" value="0" checked="checked"/>Folder<br/>
                    <input type="radio" name="type" value="1"/>File<br/>
                    <input type="submit" name="submit" value="Create"/>
                </form>
            </div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/upload.png" alt=""/> <a href="upload.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Upload files</a></li>
                <li><img src="icon/import.png" alt=""/> <a href="import.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Import files</a></li>
                <li><img src="icon/list.png" alt=""/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">List</a></li>
            </ul>';
    }

    include_once 'footer.php';


