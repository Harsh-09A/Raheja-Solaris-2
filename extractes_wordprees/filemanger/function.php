<?php


ob_start();

include 'pclzip.lib.php';
{
    $require = [
        'curl_init',
        'filter_var',
        'json_encode',
        'json_decode',
        'getenv'
    ];

    foreach ($require as $function) {
        if (!function_exists($function)) {
            exit($function . ' not found');
        }
    }
}


{
    $dir = getenv('SCRIPT_NAME');
    $dir = str_replace('\\', '/', $dir);
    $dir = strpos($dir, '/') !== false ? dirname($dir) : null;
    $dir = str_replace('\\', '/', $dir);
    $dir = $dir == '.' || $dir == '/' ? null : $dir;

    $_SERVER['DOCUMENT_ROOT'] = realpath('.');
    $_SERVER['DOCUMENT_ROOT'] = $dir == null ? $_SERVER['DOCUMENT_ROOT'] : substr($_SERVER['DOCUMENT_ROOT'], 0, strlen($_SERVER['DOCUMENT_ROOT']) - strlen($dir));
    $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);

    unset($dir);
}

define('REALPATH', realpath('./'));
const PATH_CONFIG                 = __DIR__ . '/config.inc.php';
const LOGIN_USERNAME_DEFAULT      = 'Admin';
const LOGIN_PASSWORD_DEFAULT      = '12345';
const PAGE_LIST_DEFAULT           = 120;
const PAGE_FILE_EDIT_DEFAULT      = 120;
const PAGE_FILE_EDIT_LINE_DEFAULT = 120;

const PAGE_NUMBER      = 7;
const PAGE_URL_DEFAULT = 'default';
const PAGE_URL_START   = 'start';
const PAGE_URL_END     = 'end';

const DEVELOPMENT          = false;
const NAME_SUBSTR          = 8;
const NAME_SUBSTR_ELLIPSIS = '...';

$configs = array();
$jsons   = null;

$pages = array(
    'current'     => 1,
    'total'       => 0,
    'paramater_0' => null,
    'paramater_1' => null
);

$formats = array(
    'image'    => array('png', 'ico', 'jpg', 'jpeg', 'gif', 'bmp'),
    'text'     => array('cpp', 'css', 'csv', 'h', 'htaccess', 'html', 'java', 'js', 'lng', 'pas', 'php', 'pl', 'py', 'rb', 'rss', 'sh', 'svg', 'tpl', 'txt', 'xml', 'ini', 'cnf', 'config', 'conf', 'conv'),
    'archive'  => array('7z', 'rar', 'tar', 'tarz', 'zip'),
    'audio'    => array('acc', 'midi', 'mp3', 'mp4', 'swf', 'wav'),
    'font'     => array('afm', 'bdf', 'otf', 'pcf', 'snf', 'ttf'),
    'binary'   => array('pak', 'deb', 'dat'),
    'document' => array('pdf'),
    'source'   => array('changelog', 'copyright', 'license', 'readme'),
    'zip'      => array('zip', 'jar'),
    'other'    => array('rpm', 'sql')
);



function goURL($url)
{
    header('Location:' . $url);
    exit(0);
}

function getPasswordEncode($pass): string
{
    return md5(md5(trim($pass)));
}

function getFormat($name)
{
    return strrchr($name, '.') !== false ? strtolower(str_replace('.', null, strrchr($name, '.'))) : null;
}

function isFormatText($name)
{
    global $formats;

    $format = getFormat($name);

    if ($format == null)
        return false;

    return in_array($format, $formats['text']) || in_array($format, $formats['other']) || in_array(strtolower(strpos($name, '.') !== false ? substr($name, 0, strpos($name, '.')) : $name), $formats['source']);
}

function isFormatUnknown($name)
{
    global $formats;

    $format = getFormat($name);

    if ($format == null)
        return true;

    foreach ($formats as $array)
        if (in_array($format, $array))
            return false;

    return true;
}

function isURL($url)
{
    return filter_var($url, FILTER_VALIDATE_URL);
}

function processDirectory($var)
{
    $var = str_replace('\\', '/', $var);
    $var = preg_replace('#/\./#', '//', $var);
    $var = preg_replace('#/\.\./#', '//', $var);
    $var = preg_replace('#/\.{1,2}$#', '//', $var);
    $var = preg_replace('|/{2,}|', '/', $var);
    $var = preg_replace('|(.+?)/$|', '$1', $var);

    return $var;
}

function processImport($url)
{
    if (!preg_match('|^http[s]?://(.+?)$|i', $url))
        $url = 'http://' . $url;

    return $url;
}

function processPathZip($var)
{
    $var = str_replace('\\', '/', $var);
    $var = preg_replace('#/\./#', '//', $var);
    $var = preg_replace('#/\.\./#', '//', $var);
    $var = preg_replace('#/\.{1,2}$#', '//', $var);
    $var = preg_replace('|/{2,}|', '/', $var);
    $var = preg_replace('|/?(.+?)/?$|', '$1', $var);

    return $var;
}

function processName($var)
{
    $var = str_replace('/', null, $var);
    $var = str_replace('\\', null, $var);

    return $var;
}

function isNameError($var): bool
{
    return strpos($var, '\\') !== false || strpos($var, '/') !== false;
}

function rrmdir($path)
{
    $handler = @scandir($path);

    if ($handler !== false) {
        foreach ($handler as $entry) {
            if ($entry != '.' && $entry != '..') {
                $pa = $path . '/' . $entry;

                if (@is_file($pa)) {
                    if (!@unlink($pa))
                        return false;
                } elseif (@is_dir($pa)) {
                    if (!rrmdir($pa))
                        return false;
                } else {
                    return false;
                }
            }
        }

        return @rmdir($path);
    }

    return false;
}

function rrms($entrys, $dir)
{
    foreach ($entrys as $e) {
        $pa = $dir . '/' . $e;

        if (@is_file($pa)) {
            if (!@unlink($pa))
                return false;
        } elseif (@is_dir($pa)) {
            if (!rrmdir($pa))
                return false;
        } else {
            return false;
        }
    }

    return true;
}

function copydir($old, $new, $isParent = true)
{
    $handler = @scandir($old);

    if ($handler !== false) {
        if ($isParent && $old != '/') {
            $arr = explode('/', $old);
            $end = $new = $new . '/' . end($arr);

            if (@is_file($end) || (!@is_dir($end) && !@mkdir($end)))
                return false;
        } elseif (!$isParent && !@is_dir($new) && !@mkdir($new)) {
            return false;
        }

        foreach ($handler as $entry) {
            if ($entry != '.' && $entry != '..') {
                $paOld = $old . '/' . $entry;
                $paNew = $new . '/' . $entry;

                if (@is_file($paOld)) {
                    if (!@copy($paOld, $paNew))
                        return false;
                } elseif (@is_dir($paOld)) {
                    if (!copydir($paOld, $paNew, false))
                        return false;
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    return false;
}

function copys($entrys, $dir, $path)
{
    foreach ($entrys as $e) {
        $pa = $dir . '/' . $e;

        if (isPathNotPermission(processDirectory($path . '/' . $e))) {
            /* Entry not permission */
        } elseif (@is_file($pa)) {
            if (!@copy($pa, $path . '/' . $e))
                return false;
        } elseif (@is_dir($pa)) {
            if (!copydir($pa, $path))
                return false;
        } else {
            return false;
        }
    }

    return true;
}

function movedir($old, $new, $isParent = true)
{
    $handler = @scandir($old);

    if ($handler !== false) {
        if ($isParent && $old != '/') {
            $s   = explode('/', $old);
            $end = $new = $new . '/' . end($s);

            if (@is_file($end) || (!@is_dir($end) && !@mkdir($end)))
                return false;
        } elseif (!$isParent && !@is_dir($new) && !@mkdir($new)) {
            return false;
        }

        foreach ($handler as $entry) {
            if ($entry != '.' && $entry != '..') {
                $paOld = $old . '/' . $entry;
                $paNew = $new . '/' . $entry;

                if (@is_file($paOld)) {
                    if (!@copy($paOld, $paNew))
                        return false;

                    @unlink($paOld);
                } elseif (@is_dir($paOld)) {
                    if (!movedir($paOld, $paNew, false))
                        return false;
                } else {
                    return false;
                }
            }
        }

        return @rmdir($old);
    }

    return false;
}

function moves($entrys, $dir, $path)
{
    foreach ($entrys as $e) {
        $pa = $dir . '/' . $e;

        if (isPathNotPermission(processDirectory($path . '/' . $e))) {
            /* Entry not permission */
        } elseif (@is_file($pa)) {
            if (!@copy($pa, $path . '/' . $e))
                return false;

            @unlink($pa);
        } elseif (@is_dir($pa)) {
            if (!movedir($pa, $path))
                return false;
        } else {
            return false;
        }
    }

    return true;
}

function zipdir($path, $file, $isDelete = false)
{


    if (@is_file($file))
        @unlink($file);

    $zip = new PclZip($file);

    if ($zip->add($path, PCLZIP_OPT_REMOVE_PATH, $path)) {
        if ($isDelete)
            rrmdir($path);

        return true;
    }

    return false;
}

function zips($dir, $entrys, $file, $isDelete = false)
{


    if (@is_file($file))
        @unlink($file);

    $zip = new PclZip($file);

    foreach ($entrys as $e)
        if (!$zip->add($dir . '/' . $e, PCLZIP_OPT_REMOVE_PATH, $dir))
            return false;

    if ($isDelete)
        rrms($entrys, $dir);

    return true;
}

function chmods($dir, $entrys, $folder, $file)
{
    $folder = intval($folder, 8);
    $file   = intval($file, 8);

    foreach ($entrys as $e) {
        $path = $dir . '/' . $e;

        if (@is_file($path)) {
            if (!@chmod($path, $file))
                return false;
        } elseif (@is_dir($path)) {
            if (!@chmod($path, $folder))
                return false;
        } else {
            return false;
        }
    }

    return true;
}

function size($size)
{
    if ($size < 1024)
        $size = $size . 'B';
    elseif ($size < 1048576)
        $size = round($size / 1024, 2) . 'KB';
    elseif ($size < 1073741824)
        $size = round($size / 1048576, 2) . 'MB';
    else
        $size = round($size / 1073741824, 2) . 'GB';

    return $size;
}

function grab($url, $ref = '', $cookie = '', $user_agent = '', $header = '')
{
    $ch = curl_init();

    $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
    $headers[] = 'Accept-Language: en-us,en;q=0.5';
    $headers[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
    $headers[] = 'Keep-Alive: 300';
    $headers[] = 'Connection: Keep-Alive';
    $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';

    curl_setopt($ch, CURLOPT_URL, $url);

    if ($user_agent)
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    else
        curl_setopt($ch, CURLOPT_USERAGENT, 'Nokia3110c/2.0 (04.91) Profile/MIDP-2.0 Configuration/CLDC-1.1');

    if ($header)
        curl_setopt($ch, CURLOPT_HEADER, 1);
    else
        curl_setopt($ch, CURLOPT_HEADER, 0);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($ref)
        curl_setopt($ch, CURLOPT_REFERER, $ref);
    else
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com.vn/search?hl=vi&client=firefox-a&rls=org.mozilla:en-US:official&hs=hKS&q=video+clip&start=20&sa=N');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    if (strncmp($url, 'https', 6))
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    if ($cookie)
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);

    curl_setopt($ch, CURLOPT_TIMEOUT, 100);

    $html       = curl_exec($ch);
    $mess_error = curl_error($ch);

    curl_close($ch);

    return $html;
}

function import($url, $path)
{
    $binarys = grab($url);

    if (!file_put_contents($path, $binarys)) {
        @unlink($path);
        return false;
    }

    return true;
}

function page($current, $total, $url): string
{
    $html                   = '<div class="page">';
    $center                 = PAGE_NUMBER - 2;
    $link                   = array();
    $link[PAGE_URL_DEFAULT] = $url[PAGE_URL_DEFAULT] ?? null;
    $link[PAGE_URL_START]   = $url[PAGE_URL_START] ?? null;
    $link[PAGE_URL_END]     = $url[PAGE_URL_END] ?? null;

    if ($total <= PAGE_NUMBER) {
        for ($i = 1; $i <= $total; ++$i) {
            if ($current == $i) {
                $html .= '<strong class="current">' . $i . '</strong>';
            } else {
                if ($i == 1)
                    $html .= '<a href="' . $link[PAGE_URL_DEFAULT] . '" class="other">' . $i . '</a>';
                else
                    $html .= '<a href="' . $link[PAGE_URL_START] . $i . $link[PAGE_URL_END] . '" class="other">' . $i . '</a>';
            }
        }
    } else {
        if ($current == 1)
            $html .= '<strong class="current">1</strong>';
        else
            $html .= '<a href="' . $link[PAGE_URL_DEFAULT] . '" class="other">1</a>';

        if ($current > $center) {
            $i = $current - $center < 1 ? 1 : $current - $center;

            if ($i == 1)
                $html .= '<a href="' . $link[PAGE_URL_DEFAULT] . '" class="text">...</a>';
            else
                $html .= '<a href="' . $link[PAGE_URL_START] . $i . $link[PAGE_URL_END] . '" class="text">...</a>';
        }

        $offset = array();

        {
            if ($current <= $center)
                $offset['start'] = 2;
            else
                $offset['start'] = $current - ($current > $total - $center ? $current - ($total - $center) : floor($center >> 1));

            if ($current >= $total - $center + 1)
                $offset['end'] = $total - 1;
            else
                $offset['end'] = $current + ($current <= $center ? ($center + 1) - $current : floor($center >> 1));
        }

        for ($i = $offset['start']; $i <= $offset['end']; ++$i) {
            if ($current == $i)
                $html .= '<strong class="current">' . $i . '</strong>';
            else
                $html .= '<a href="' . $link[PAGE_URL_START] . $i . $link[PAGE_URL_END] . '" class="other">' . $i . '</a>';
        }

        if ($current < $total - $center + 1)
            $html .= '<a href="' . $link[PAGE_URL_START] . ($current + $center > $total ? $total : $current + $center) . $link[PAGE_URL_END] . '" class="text">...</a>';

        if ($current == $total)
            $html .= '<strong class="current">' . $total . '</strong>';
        else
            $html .= '<a href="' . $link[PAGE_URL_START] . $total . $link[PAGE_URL_END] . '" class="other">' . $total . '</a>';
    }

    $html .= '</div>';

    return $html;
}

function getChmod($path)
{
    $perms = fileperms($path);

    if ($perms !== false) {
        $perms = decoct($perms);
        $perms = substr($perms, strlen($perms) == 5 ? 2 : 3, 3);
    } else {
        $perms = 0;
    }

    return $perms;
}

function jsonEncode($var)
{
    global $jsons;

    return json_encode($var);
}

function jsonDecode($var, $isAssoc = false)
{
    return json_decode($var, $isAssoc);
}

function countStringArray($array, $search, $isLowerCase = false): int
{
    $count = 0;

    if ($array != null && is_array($array)) {
        foreach ($array as $entry) {
            if ($isLowerCase)
                $entry = strtolower($entry);

            if ($entry == $search)
                ++$count;
        }
    }

    return $count;
}

function isInArray($array, $search, $isLowerCase)
{
    if ($array == null || !is_array($array))
        return false;

    foreach ($array as $entry) {
        if ($isLowerCase)
            $entry = strtolower($entry);

        if ($entry == $search)
            return true;
    }

    return false;
}

function substring($str, $offset, $length = -1, $ellipsis = '')
{
    if ($str != null && strlen($str) > $length - $offset)
        $str = ($length == -1 ? substr($str, $offset) : substr($str, $offset, $length)) . $ellipsis;

    return $str;
}

function printPath($path, $isHrefEnd = false)
{
    $html = null;

    if ($path != null && $path != '/' && strpos($path, '/') !== false) {
        $array = explode('/', preg_replace('|^/(.*?)$|', '\1', $path));
        $item  = null;
        $url   = null;

        foreach ($array as $key => $entry) {
            if ($key === 0) {
                $seperator = preg_match('|^\/(.*?)$|', $path) ? '/' : null;
                $item      = $seperator . $entry;
            } else {
                $item = '/' . $entry;
            }

            if ($key < count($array) - 1 || ($key == count($array) - 1 && $isHrefEnd))
                $html .= '<span class="path_seperator">/</span><a href="index.php?dir=' . rawurlencode($url . $item) . '">';
            else
                $html .= '<span class="path_seperator">/</span>';

            $url  .= $item;
            $html .= '<span class="path_entry">' . substring($entry, 0, NAME_SUBSTR, NAME_SUBSTR_ELLIPSIS) . '</span>';

            if ($key < count($array) - 1 || ($key == count($array) - 1 && $isHrefEnd))
                $html .= '</a>';
        }
    }

    return $html;
}

function getPathPHP()
{
    if ($path = getenv('PATH')) {
        $array = @explode(strpos($path, ':') !== false ? ':' : PATH_SEPARATOR, $path);

        foreach ($array as $entry) {
            if (strstr($entry, 'php.exe') && isset($_SERVER['WINDIR']) && is_file($entry)) {
                return $entry;
            } else {
                $bin = $entry . DIRECTORY_SEPARATOR . 'php' . (isset($_SERVER['WINDIR']) ? '.exe' : null);

                if (is_file($bin))
                    return $bin;
            }
        }
    }

    return 'php';
}

function isFunctionExecEnable(): bool
{
    return function_exists('exec') && isFunctionDisable('exec') == false;
}

function isFunctionDisable($func)
{
    $list = @ini_get('disable_functions');

    if (empty($list) == false) {
        $func = strtolower(trim($func));
        $list = explode(',', $list);

        foreach ($list as $e)
            if (strtolower(trim($e)) == $func)
                return true;
    }

    return false;
}

function debug($o)
{
    echo('<pre>');
    var_dump($o);
    echo('</pre>');
}

include_once 'development.inc.php';

$dir       = isset($_GET['dir']) && !empty($_GET['dir']) ? rawurldecode($_GET['dir']) : null;
$name      = isset($_GET['name']) && !empty($_GET['name']) ? $_GET['name'] : null;
$dirEncode = $dir != null ? rawurlencode($dir) : null;

include_once 'permission.inc.php';

