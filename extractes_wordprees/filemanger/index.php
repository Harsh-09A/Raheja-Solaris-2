<?php

const ACCESS = true;
const INDEX  = true;

include_once 'function.php';



$title   = !IS_INSTALL_ROOT_DIRECTORY ? 'List' : 'File Manager error';
$dir     = NOT_PERMISSION == false && isset($_GET['dir']) && empty($_GET['dir']) == false ? rawurldecode($_GET['dir']) : $_SERVER['DOCUMENT_ROOT'];
$dir     = processDirectory($dir);
$handler = null;

include_once 'header.php';

if (!IS_INSTALL_ROOT_DIRECTORY) {
    $handler = @scandir($dir);

    if ($handler === false) {
        $dir = $_SERVER['DOCUMENT_ROOT'];
        $dir = processDirectory($dir);

        $handler = @scandir($dir);
    }
}

if (!is_array($handler))
    $handler = array();

$dirEncode = rawurlencode($dir);
$count     = count($handler);
$lists     = array();

if (!IS_INSTALL_ROOT_DIRECTORY && $count > 0) {
    $folders = array();
    $files   = array();

    foreach ($handler as $entry) {
        if ($entry != '.' && $entry != '..') {
            if ($entry == DIRECTORY_FILE_MANAGER && IS_ACCESS_PARENT_PATH_FILE_MANAGER) ;
            /* Is hide directory File Manager */
            elseif (is_dir($dir . '/' . $entry))
                $folders[] = $entry;
            else
                $files[] = $entry;
        }
    }

    if (count($folders) > 0) {
        asort($folders);

        foreach ($folders as $entry)
            $lists[] = array('name' => $entry, 'is_directory' => true);
    }

    if (count($files) > 0) {
        asort($files);

        foreach ($files as $entry)
            $lists[] = array('name' => $entry, 'is_directory' => false);
    }
}

$count = count($lists);
$html  = null;

if (!IS_INSTALL_ROOT_DIRECTORY && $dir != '/' && strpos($dir, '/') !== false) {
    $array = explode('/', preg_replace('|^/(.*?)$|', '\1', $dir));
    $html  = null;
    $item  = null;
    $url   = null;

    foreach ($array as $key => $entry) {
        if ($key === 0) {
            $seperator = preg_match('|^\/(.*?)$|', $dir) ? '/' : null;
            $item      = $seperator . $entry;
        } else {
            $item = '/' . $entry;
        }

        if ($key < count($array) - 1)
            $html .= '/<a href="index.php?dir=' . rawurlencode($url . $item) . '">';
        else
            $html .= '/';

        $url  .= $item;
        $html .= substring($entry, 0, NAME_SUBSTR, NAME_SUBSTR_ELLIPSIS);

        if ($key < count($array) - 1)
            $html .= '</a>';
    }
}

if (!IS_INSTALL_ROOT_DIRECTORY) {
    echo '<script language="javascript" src="checkbox.js"></script>';
    echo '<div class="title">' . $html . '</div>';
}

if (NOT_PERMISSION) {
    if (IS_INSTALL_ROOT_DIRECTORY) {
        echo '<div class="title">File Manager error</div>
                <div class="list">You are Setting File Manager on the original Folder, please move it to a Folder</div>';
    } elseif (IS_ACCESS_FILE_IN_FILE_MANAGER) {
        echo '<div class="notice_info">You cannot See File of File Manager it has been blocked</div>';
    } else {
        echo '<div class="notice_info">You cannot view the folder of File Manager it has been blocked</div>';
    }
}

if (!IS_INSTALL_ROOT_DIRECTORY) {
    echo '<form action="action.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post" name="form"><ul class="list_file">';

    if (preg_replace('|[a-zA-Z]+:|', '', str_replace('\\', '/', $dir)) != '/') {
        $path = strrchr($dir, '/');

        if ($path !== false)
            $path = 'index.php?dir=' . rawurlencode(substr($dir, 0, strlen($dir) - strlen($path)));
        else
            $path = 'index.php';

        echo '<li class="normal">
                    <img src="icon/back.png" style="margin-left: 5px; margin-right: 5px"/> 
                    <a href="' . $path . '">
                        <strong class="back">...</strong>
                    </a>
                </li>';
    }

    if ($count <= 0) {
        echo '<li class="normal"><img src="icon/empty.png"/> <span class="empty">No Folders or Files</span></li>';
    } else {
        $start = 0;
        $end   = $count;

        if (isset($configs['page_list']) && $configs['page_list'] > 0 && $count > $configs['page_list']) {
            $pages['total'] = ceil($count / $configs['page_list']);

            if ($pages['total'] <= 0 || $pages['current'] > $pages['total'])
                goURL('index.php?dir=' . $dirEncode . ($pages['total'] <= 0 ? null : '&page_list=' . $pages['total']));

            $start = ($pages['current'] * $configs['page_list']) - $configs['page_list'];
            $end   = $start + $configs['page_list'] >= $count ? $count : $start + $configs['page_list'];
        }

        for ($i = $start; $i < $end; ++$i) {
            $name  = $lists[$i]['name'];
            $path  = $dir . '/' . $name;
            $perms = getChmod($path);

            if ($lists[$i]['is_directory']) {
                echo '<li class="folder">
                            <div>
                                <input type="checkbox" name="entry[]" value="' . $name . '"/>
                                <a href="folder_edit.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">
                                    <img src="icon/folder.png"/>
                                </a>
                                <a href="index.php?dir=' . rawurlencode($path) . '">' . $name . '</a>
                                <div class="perms">
                                    <a href="folder_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" class="chmod">' . $perms . '</a>
                                </div>
                            </div>
                        </li>';
            } else {
                $edit   = array(null, '</a>');
                $icon   = 'unknown';
                $type   = getFormat($name);
                $isEdit = false;

                if (in_array($type, $formats['other'])) {
                    $icon = $type;
                } elseif (in_array($type, $formats['text'])) {
                    $icon   = $type;
                    $isEdit = true;
                } elseif (in_array($type, $formats['archive'])) {
                    $icon = $type;
                } elseif (in_array($type, $formats['audio'])) {
                    $icon = $type;
                } elseif (in_array($type, $formats['font'])) {
                    $icon = $type;
                } elseif (in_array($type, $formats['binary'])) {
                    $icon = $type;
                } elseif (in_array($type, $formats['document'])) {
                    $icon = $type;
                } elseif (in_array($type, $formats['image'])) {
                    $icon = 'image';
                } elseif (in_array(strtolower(strpos($name, '.') !== false ? substr($name, 0, strpos($name, '.')) : $name), $formats['source'])) {
                    $icon   = strtolower(strpos($name, '.') !== false ? substr($name, 0, strpos($name, '.')) : $name);
                    $isEdit = true;
                } elseif (isFormatUnknown($name)) {
                    $icon   = 'unknown';
                    $isEdit = true;
                }

                if (strtolower($name) == 'error_log' || $isEdit)
                    $edit[0] = '<a href="edit_text.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">';
                elseif (in_array($type, $formats['zip']))
                    $edit[0] = '<a href="file_unzip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">';
                else
                    $edit[0] = '<a href="file_rename.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">';

                echo '<li class="file">
                            <p>
                                <input type="checkbox" name="entry[]" value="' . $name . '"/>
                                ' . $edit[0] . '<img src="icon/mime/' . $icon . '.png"/>' . $edit[1] . '
                                <a href="file.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">' . $name . '</a>
                            </p>
                            <p>
                                <span class="size">' . size(filesize($dir . '/' . $name)) . '</span>,
                                <a href="file_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" class="chmod">' . $perms . '</a>
                            </p>
                        </li>';
            }
        }

        echo '<li class="normal"><input type="checkbox" name="all" value="1" onClick="javascript:onCheckItem();"/> <strong class="form_checkbox_all"> Select all</strong></li>';

        if (isset($configs['page_list']) && $configs['page_list'] > 0 && $pages['total'] > 1)
            echo '<li class="normal">' . page($pages['current'], $pages['total'], array(PAGE_URL_DEFAULT => 'index.php?dir=' . $dirEncode, PAGE_URL_START => 'index.php?dir=' . $dirEncode . '&page_list=')) . '</li>';
    }

    echo '</ul>';

    if ($count > 0) {
        echo '<div class="list">
                    <select name="option">
                        <option value="0">Copy</option>
                        <option value="1">Move</option>
                        <option value="2">Delete</option>
                        
                        <option value="4">Chmod</option>
                        <option value="5">Rename</option>
                    </select>
                    <input type="submit" name="submit" value="Go"/>
                </div>';
    }

    echo '</form>
            <div class="title">Function</div>
            <ul class="list">
                <li><img src="icon/create.png"/> <a href="create.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Create new</a></li>
                <li><img src="icon/upload.png"/> <a href="upload.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Upload files</a></li>
                <li><img src="icon/import.png"/> <a href="import.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Import files</a></li>
            </ul>';
}

include_once 'footer.php';
