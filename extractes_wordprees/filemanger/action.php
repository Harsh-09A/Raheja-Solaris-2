<?php



    const ACCESS = true;

    include_once 'function.php';

    


    $title  = 'ACT';
    $entry  = $_POST['entry'] ?? [];
    $option = isset($_POST['option']) ? intval($_POST['option']) : -1;

    if ($dir == null || !is_dir(processDirectory($dir))) {
        include_once 'header.php';

        echo '<div class="title">' . $title . '</div>
            <div class="list"><span>The path does not exist</span></div>
            <div class="title">Function</div>
            <ul class="list">
                <li>
                    <img src="icon/list.png" alt="" />
                    <a href="index.php' . $pages['paramater_0'] . '">List</a>
                </li>
            </ul>';
    } elseif (!$_POST || ($option < 0 || $option > 5)) {
        include_once 'header.php';

        echo '<div class="title">' . $title . '</div>
            <div class="list"><span>No action</span></div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/list.png" alt=""/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">List</a></li>
            </ul>';
    } elseif (count($entry) <= 0) {
        include_once 'header.php';

        echo '<div class="title">' . $title . '</div>
            <div class="list"><span>No choice</span></div>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/list.png" alt=""/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">List</a></li>
            </ul>';
    } else {
        $dir           = processDirectory($dir);
        $entryCheckbox = null;
        $entryHtmlList = null;

        if ($option != 5)
            $entryHtmlList = '<ul class="list">';

        foreach ($entry as $e) {
            $isFolder = is_dir($dir . '/' . $e);

            $entryCheckbox .= '<input type="hidden" name="entry[]" value="' . $e . '" checked="checked"/>';

            if ($option != 5)
                $entryHtmlList .= '<li>
                    <img src="icon/' . ($isFolder ? 'folder' : 'file') . '.png" alt="" />'
                    . ($isFolder ? '<strong class="folder_name">' . $e . '</strong>' : '<span class="file_name">' . $e . '</span>') . '
                    </li>';
        }

        if ($option != 5)
            $entryHtmlList .= '</ul>';

        if ($option === 0) {
            $title = 'Copy';

            include_once 'header.php';

            echo '<div class="title">' . $title . '</div>';

            if (isset($_POST['submit']) && isset($_POST['is_action'])) {
                echo '<div class="notice_failure">';

                if (empty($_POST['path']))
                    echo 'Not fully entered information';
                elseif ($dir == processDirectory($_POST['path']))
                    echo 'The new path must be different from the current path';
                elseif (!is_dir($_POST['path']))
                    echo 'The new path does not exist';
                elseif (isPathNotPermission(processDirectory($_POST['path'])))
                    echo 'You cannot Copy to the File Manager path';
                elseif (!copys($entry, $dir, processDirectory($_POST['path'])))
                    echo 'Copy failure';
                else
                    goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);

                echo '</div>';
            }

            echo $entryHtmlList;
            echo '<div class="list">
                    <span>' . printPath($dir, true) . '</span><hr/>
                    <form action="action.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                        <span class="bull">&bull; </span>New file path:<br/>
                        <input type="text" name="path" value="' . ($_POST['path'] ?? $dir) . '" size="18"/><br/>
                        <input type="hidden" name="is_action" value="1"/>
                        <input type="hidden" name="option" value="' . $option . '"/>';

            echo $entryCheckbox;

            echo '<input type="submit" name="submit" value="Copy"/>
                    </form>
                </div>';
        } elseif ($option === 1) {
            $title = 'Move';

            include_once 'header.php';

            echo '<div class="title">' . $title . '</div>';

            if (isset($_POST['submit']) && isset($_POST['is_action'])) {
                echo '<div class="notice_failure">';

                if (empty($_POST['path']))
                    echo 'Not fully entered information';
                elseif ($dir == processDirectory($_POST['path']))
                    echo 'The new path must be different from the current path';
                elseif (!is_dir($_POST['path']))
                    echo 'The new path does not exist';
                elseif (isPathNotPermission(processDirectory($_POST['path'])))
                    echo 'You cannot Move to the File Manager path';
                elseif (!Copys($entry, $dir, processDirectory($_POST['path'])))
                    echo 'Move failure';
                else
                    goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);

                echo '</div>';
            }

            echo $entryHtmlList;
            echo '<div class="list">
                    <span>' . printPath($dir, true) . '</span><hr/>
                    <form action="action.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                        <span class="bull">&bull; </span>New file path:<br/>
                        <input type="text" name="path" value="' . ($_POST['path'] ?? $dir) . '" size="18"/><br/>
                        <input type="hidden" name="is_action" value="1"/>
                        <input type="hidden" name="option" value="' . $option . '"/>';

            echo $entryCheckbox;

            echo '<input type="submit" name="submit" value="Move"/>
                    </form>
                </div>';
        } elseif ($option === 2) {
            $title = 'Delete';

            include_once 'header.php';

            echo '<div class="title">' . $title . '</div>';

            if (isset($_POST['accept'])) {
                if (isPathNotPermission(processDirectory($dir)))
                    echo 'You cannot Delete File Manager items';
                elseif (!rrms($entry, $dir))
                    echo '<div class="notice_failure">Delete failure</div>';
                else
                    goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
            } elseif (isset($_POST['not_accept'])) {
                goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
            }

            echo $entryHtmlList;
            echo '<div class="list">
                    <span>' . printPath($dir, true) . '</span><hr/>
                    <span>Do you really want to Delete the selected items?</span><hr/><br/>
                    <center>
                        <form action="action.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                            <input type="hidden" name="is_action" value="1"/>
                            <input type="hidden" name="option" value="' . $option . '"/>';

            echo $entryCheckbox;

            echo '<input type="submit" name="accept" value="Agree "/>
                            <input type="submit" name="not_accept" value="Cancel"/>
                        </form>
                    </center>
                </div>';
        } elseif ($option === 3) {
            $title = 'extract zip';

            include_once 'header.php';

            echo '<div class="title">' . $title . '</div>';

            if (isset($_POST['submit']) && isset($_POST['is_action'])) {
                echo '<div class="notice_failure">';

                if (empty($_POST['name']) || empty($_POST['path']))
                    echo 'Not fully entered information';
                elseif (isset($_POST['is_delete']) && processDirectory($_POST['path']) == $dir . '/' . $name)
                    echo 'If you choose Delete Folder, you cannot save the Compressed source file there';
                elseif (isPathNotPermission(processDirectory($_POST['path'])))
                    echo 'You cannot Compressed source File zip to File Manager path';
                elseif (isNameError($_POST['name']))
                    echo 'File name zip illegal';
                elseif (!zips($dir, $entry, processDirectory($_POST['path'] . '/' . processName($_POST['name'])), isset($_POST['is_delete'])))
                    echo 'extract zip failure';
                else
                    goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);

                echo '</div>';
            }

            echo $entryHtmlList;
            echo '<div class="list">
                    <span>' . printPath($dir, true) . '</span><hr/>
                    <form action="action.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                        <span class="bull">&bull; </span>Compressed file name:<br/>
                        <input type="text" name="name" value="' . ($_POST['name'] ?? 'archive.zip') . '" size="18"/><br/>
                        <span class="bull">&bull; </span>Save path:<br/>
                        <input type="text" name="path" value="' . ($_POST['path'] ?? $dir) . '" size="18"/><br/>
                        <input type="checkbox" name="is_delete" value="1"' . (isset($_POST['is_delete']) ? ' checked="checked"' : null) . '/> Delete the<br/>
                        <input type="hidden" name="is_action" value="1"/>
                        <input type="hidden" name="option" value="' . $option . '"/>';

            echo $entryCheckbox;

            echo '<input type="submit" name="submit" value="Compressed source"/>
                    </form>
                </div>';
        } elseif ($option === 4) {
            $title = 'Chmod';

            include_once 'header.php';

            echo '<div class="title">' . $title . '</div>';

            if (isset($_POST['submit']) && isset($_POST['is_action'])) {
                echo '<div class="notice_failure">';

                if (empty($_POST['folder']) || empty($_POST['file']))
                    echo 'Not fully entered information';
                elseif (!chmods($dir, $entry, $_POST['folder'], $_POST['file']))
                    echo 'Chmod failure';
                else
                    goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);

                echo '</div>';
            }

            echo $entryHtmlList;
            echo '<div class="list">
                    <span>' . printPath($dir, true) . '</span><hr/>
                    <form action="action.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                        <span class="bull">&bull; </span>Folder:<br/>
                        <input type="text" name="folder" value="' . ($_POST['folder'] ?? '755') . '" size="18"/><br/>
                        <span class="bull">&bull; </span>File:<br/>
                        <input type="text" name="file" value="' . ($_POST['file'] ?? '644') . '" size="18"/><br/>
                        <input type="hidden" name="is_action" value="1"/>
                        <input type="hidden" name="option" value="' . $option . '"/>';

            echo $entryCheckbox;

            echo '<input type="submit" name="submit" value="Chmod"/>
                    </form>
                </div>';
        } elseif ($option === 5) {
            $title    = 'Rename';
            $modifier = $entry;

            include_once 'header.php';

            echo '<div class="title">' . $title . '</div>';

            if (isset($_POST['submit']) && isset($_POST['is_action'])) {
                $modifier  = $_POST['modifier'];
                $isFailed  = false;
                $isSucceed = true;

                foreach ($modifier as $k => $e) {
                    $entryPath = $dir . '/' . $entry[$k];

                    if (empty($e)) {
                        $isFailed = true;

                        echo '<div class="notice_failure">Do not leave any box blank</div>';
                        break;
                    } elseif (isNameError($e)) {
                        $isFailed   = true;
                        $entryLabel = is_dir($entryPath) ? 'Folder' : 'File';
                        $entryCss   = is_dir($entryPath) ? 'folder' : 'file';

                        echo '<div class="notice_failure">Name ' . $entryLabel . ' <strong class="' . $entryCss . '_name_rename_action">' . $entry[$k] . '</strong> <strong>=></strong> <strong class="' . $entryCss . '_name_rename_action">' . $e . '</strong> illegal</div>';
                        break;
                    } elseif (countStringArray($modifier, strtolower($e), true) > 1 && $e != $entry[$k]) {
                        $isFailed   = true;
                        $entryLabel = is_dir($entryPath) ? 'Folder' : 'File';
                        $entryCss   = is_dir($entryPath) ? 'folder' : 'file';

                        echo '<div class="notice_failure">Name ' . $entryLabel . ' <strong class="' . $entryCss . '_name_rename_action">' . $entry[$k] . '</strong> <strong>=></strong> <strong class="' . $entryCss . '_name_rename_action">' . $e . '</strong> This already exists in another input frame</div>';
                        break;
                    } elseif (!isInArray($entry, strtolower($e), true) && file_exists($dir . '/' . $e)) {
                        $isFailed   = true;
                        $entryLabel = is_dir($entryPath) ? 'Folder' : 'File';
                        $entryCss   = is_dir($entryPath) ? 'folder' : 'file';

                        echo '<div class="notice_failure">Name ' . $entryLabel . ' <strong class="' . $entryCss . '_name_rename_action">' . $entry[$k] . '</strong> <strong>=></strong> <strong class="' . $entryCss . '_name_rename_action">' . $e . '</strong> This already exists</div>';
                        break;
                    }
                }

                if (!$isFailed) {
                    $isSucceed = true;
                    $rand      = md5(rand(1000, 99999) . '-' . $dir);
                    $rand      = substr($rand, 0, strlen($rand) >> 1);

                    foreach ($entry as $e) {
                        $entryPath = $dir . '/' . $e;

                        @rename($entryPath, $entryPath . '-' . $rand);
                    }

                    foreach ($entry as $k => $e) {
                        $entryPath  = $dir . '/' . $e;
                        $entryLabel = is_dir($entryPath) ? 'Folder' : 'File';
                        $entryCss   = is_dir($entryPath) ? 'folder' : 'file';

                        if (!@rename($entryPath . '-' . $rand, $dir . '/' . processName($modifier[$k]))) {
                            $isSucceed = false;

                            echo '<div class="notice_failure">Rename ' . $entryLabel . ' <strong class="' . $entryCss . '_name_rename_action">' . $e . '</strong> <strong>=></strong> <strong class="' . $entryCss . '_name_rename_action">' . $modifier[$k] . '</strong> failure</div>';
                        } else {
                            $entry[$k] = $modifier[$k];

                            echo '<div class="notice_succeed">Rename ' . $entryLabel . ' <strong class="' . $entryCss . '_name_rename_action">' . $e . '</strong> <strong>=></strong> <strong class="' . $entryCss . '_name_rename_action">' . $modifier[$k] . '</strong> successfully</div>';
                        }
                    }
                }

                if (!$isFailed && $isSucceed)
                    goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
            }

            echo $entryHtmlList;
            echo '<div class="list ellipsis break-word">
                    <span>' . printPath($dir, true) . '</span><hr/>
                    <form action="action.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">';

            for ($i = 0; $i < count($entry); ++$i) {
                $entryPath = $dir . '/' . $entry[$i];
                $entryName = $entry[$i];

                if (is_dir($entryPath))
                    echo '<span class="bull">&bull; </span>Name Folder (<strong class="folder_name_rename_action">' . $entryName . '</strong>):<br/>';
                else
                    echo '<span class="bull">&bull; </span>File name (<strong class="file_name_rename_action">' . $entryName . '</strong>):<br/>';

                echo '<input type="text" name="modifier[]" value="' . $modifier[$i] . '" size="18"/><br/>';
            }

            echo '<input type="hidden" name="is_action" value="1"/>
                    <input type="hidden" name="option" value="' . $option . '"/>';

            echo $entryCheckbox;

            echo '<input type="submit" name="submit" value="Rename"/>
                    </form>
                </div>';
        }

        echo '<div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/list.png" alt=""/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">List</a></li>
            </ul>';
    }

    include_once 'footer.php';

