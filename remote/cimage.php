<?php

//jadro
define('_indexroot', '../');
define('_tmp_customheader', '');
require (_indexroot . "core.php");

//kontrola GD
_checkGD("jpg");

//inicializace obrazku, nacteni kodu
$imgw = 65;
$imgh = 22;
$img = imagecreate($imgw, $imgh);
if (isset($_GET['n']) and isset($_SESSION[_sessionprefix . 'captcha_code'][(int) $_GET['n']])) {
    list($code, $drawn) = $_SESSION[_sessionprefix . 'captcha_code'][(int) $_GET['n']];
    if ($drawn)
	die;
    $_SESSION[_sessionprefix . 'captcha_code'][(int) $_GET['n']][1] = true;
} else
    die;

/**
 * Linear perspective class
 */
class linear_perspective {

    public $cam_location = array('x' => -30, 'y' => 0, 'z' => -250);
    public $cam_rotation = array('x' => -1, 'y' => 0, 'z' => 0);
    public $viewer_position = array('x' => 450, 'y' => -500, 'z' => -80);

    public function getProjection($point) {

	$translation = array();
	$projection = array();

	$translation['x'] = cos($this->cam_rotation['y']) * (sin($this->cam_rotation['z']) * ($point['y'] - $this->cam_location['y']) + cos($this->cam_rotation['z']) * ($point['x'] - $this->cam_location['x'])) - sin($this->cam_rotation['y']) * ($point['z'] - $this->cam_location['z']);
	$translation['y'] = sin($this->cam_rotation['x']) * (cos($this->cam_rotation['y']) * ($point['z'] - $this->cam_location['z']) + sin($this->cam_rotation['y']) * (sin($this->cam_rotation['z']) * ($point['y'] - $this->cam_location['y']) + cos($this->cam_rotation['z']) * ($point['x'] - $this->cam_location['x']))) + cos($this->cam_rotation['z']) * (cos($this->cam_rotation['z']) * ($point['y'] - $this->cam_location['y']) - sin($this->cam_rotation['z']) * ($point['x'] - $this->cam_location['x']));
	$translation['z'] = cos($this->cam_rotation['x']) * (cos($this->cam_rotation['y']) * ($point['z'] - $this->cam_location['z']) + sin($this->cam_rotation['y']) * (sin($this->cam_rotation['z']) * ($point['y'] - $this->cam_location['y']) + cos($this->cam_rotation['z']) * ($point['x'] - $this->cam_location['x']))) - sin($this->cam_rotation['z']) * (cos($this->cam_rotation['z']) * ($point['y'] - $this->cam_location['y']) - sin($this->cam_rotation['z']) * ($point['x'] - $this->cam_location['x']));

	$projection['x'] = ($translation['x'] - $this->viewer_position['x']) * ($this->viewer_position['z'] / $translation['z']);
	$projection['y'] = ($translation['y'] - $this->viewer_position['y']) * ($this->viewer_position['z'] / $translation['z']);

	return $projection;
    }

}

function imagelightnessat($img, $x, $y) {

    if (!is_resource($img))
	return 0.0;

    $c = @imagecolorat($img, $x, $y);
    if ($c === false)
	return false;

    if (imageistruecolor($img)) {
	$red = ($c >> 16) & 0xFF;
	$green = ($c >> 8) & 0xFF;
	$blue = $c & 0xFF;
    } else {
	$i = imagecolorsforindex($img, $c);
	$red = $i['red'];
	$green = $i['green'];
	$blue = $i['blue'];
    }

    $m = min($red, $green, $blue);
    $n = max($red, $green, $blue);
    $lightness = (double) (($m + $n) / 510.0);
    return ($lightness);
}

$perspective = new linear_perspective();

// sizes and offsets
$matrix_dim = array('x' => 85, 'y' => 30);
$captcha_dim = array('x' => 410, 'y' => 120);
$distance = array('x' => 1, 'y' => 1, 'z' => 1);
$metric = array('x' => 10, 'y' => 25, 'z' => 5);
$offset = array('x' => 198, 'y' => -60);

// matrix
$matrix = imagecreatetruecolor($matrix_dim['x'], $matrix_dim['y']);
$black = imagecolorexact($matrix, 0, 0, 0);
$white = imagecolorexact($matrix, 255, 255, 255);
$gray = imagecolorexact($matrix, 200, 200, 200);
imagefill($matrix, 0, 0, $white);

// random pixels
for ($i = 0; $i < 300; ++$i)
    imagesetpixel($matrix, mt_rand(5, ($matrix_dim['x'] - 6)), mt_rand(5, ($matrix_dim['y'] - 6)), $gray);

// text
imagefttext($matrix, 19, 0, 4, 25, $black, 'cimage.ttf', $code);

// rotate
$matrix = imagerotate($matrix, mt_rand(-1, 1), $white);

// compute 3D points
$point = array();
for ($x = 0; $x < $matrix_dim['x']; $x++) {
    for ($y = 0; $y < $matrix_dim['y']; $y++) {
	$lightness = imagelightnessat($matrix, $x, $y);
	$point[$x][$y] = $perspective->getProjection(array('x' => $x * $metric['x'] + $distance['x'], 'y' => $lightness * $metric['y'] + $distance['y'], 'z' => ($matrix_dim['y'] - $y) * $metric['z'] + $distance['z']));
    }
}
imagedestroy($matrix);

// captcha image
$captcha = imagecreatetruecolor($captcha_dim['x'], $captcha_dim['y']);
$black = imagecolorexact($captcha, 0, 0, 0);
$white = imagecolorexact($captcha, 255, 255, 255);
imagefill($captcha, 0, 0, $white);

// draw countour lines
imageantialias($captcha, true);
for ($x = 1; $x < $matrix_dim['x']; $x++)
    for ($y = 1; $y < $matrix_dim['y']; $y++)
	imageline($captcha, -$point[$x - 1][$y - 1]['x'] + $offset['x'], -$point[$x - 1][$y - 1]['y'] + $offset['y'], -$point[$x][$y]['x'] + $offset['x'], -$point[$x][$y]['y'] + $offset['y'], $black);

// output
//imagepng($captcha); die;
//imagefilledrectangle($captcha, 0, 0, 90, 25, $white);
//imagefttext($captcha, 20, 0, 8, 20, $black, k::classPath('iCaptcha').k::DATA_DIRNAME.'/font.ttf', $code);
$width = 160;
$height = floor($width / ($captcha_dim['x'] / $captcha_dim['y']));
$rcaptcha = imagecreatetruecolor($width, $height);
imagecopyresampled($rcaptcha, $captcha, 0, 0, 0, 0, $width, $height, $captcha_dim['x'], $captcha_dim['y']);
header('Content-Type: image/png');
imagepng($rcaptcha);
?>