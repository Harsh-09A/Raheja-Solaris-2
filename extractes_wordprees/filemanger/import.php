<?php

const ACCESS = true;

include_once 'function.php';



$title = 'Upload files';

include_once 'header.php';

echo '<div class="title">' . $title . '</div>';

if ($dir == null || !is_dir(processDirectory($dir))) {
    echo '<div class="list"><span>The path does not exist</span></div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">List</a></li>
            </ul>';
} else {
    $dir = processDirectory($dir);

    if (isset($_POST['submit'])) {
        $isEmpty = true;

        foreach ($_POST['url'] as $entry) {
            if (!empty($entry)) {
                $isEmpty = false;
                break;
            }
        }

        if ($isEmpty) {
            echo '<div class="notice_failure">No url has been entered yet</div>';
        } else {
            for ($i = 0; $i < count($_POST['url']); ++$i) {
                if (!empty($_POST['url'][$i])) {
                    $_POST['url'][$i] = processImport($_POST['url'][$i]);

                    if (!isURL($_POST['url'][$i]))
                        echo '<div class="notice_failure">URL <strong class="url_import">' . $_POST['url'][$i] . '</strong> illegal</div>';
                    elseif (import($_POST['url'][$i], $dir . '/' . basename($_POST['url'][$i])))
                        echo '<div class="notice_succeed">Import files <strong class="file_name_import">' . basename($_POST['url'][$i]) . '</strong>, <span class="file_size_import">' . size(filesize($dir . '/' . basename($_POST['url'][$i]))) . '</span> successfully</div>';
                    else
                        echo '<div class="notice_failure">Import files <strong class="file_name_import">' . basename($_POST['url'][$i]) . '</strong> failure</div>';
                }
            }
        }
    }

    echo '<div class="list">
                <span>' . printPath($dir, true) . '</span><hr/>
                <form action="import.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                    <span class="bull">&bull; </span>URL 1:<br/>
                    <input type="text" name="url[]" size="18"/><br/>
                    <span class="bull">&bull; </span>URL:<br/>
                    <input type="text" name="url[]" size="18"/><br/>
                    <span class="bull">&bull; </span>URL 3:<br/>
                    <input type="text" name="url[]" size="18"/><br/>
                    <span class="bull">&bull; </span>URL 4:<br/>
                    <input type="text" name="url[]" size="18"/><br/>
                    <span class="bull">&bull; </span>URL 5:<br/>
                    <input type="text" name="url[]" size="18"/><br/>
                    <input type="submit" name="submit" value="import"/>
                </form>
            </div>

            <div class="tips"><img src="icon/tips.png"/> It s okay to not have http:// in front. If there is https://, you must enter it</div>

            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/create.png"/> <a href="create.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Create new</a></li>
                <li><img src="icon/upload.png"/> <a href="upload.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Upload files</a></li>
                <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">List</a></li>
            </ul>';
}

include_once 'footer.php';
