<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

/**
 * Generates a human-friendly, CLEAR captcha image.
 * Karakter besar, tebal (blocky scale), kontras tinggi,
 * tanpa glyph membingungkan (0/O, 1/I/l, 2/Z, 5/S, 8/B).
 */
class CaptchaController extends Controller
{
    private const CHARS = 'ABCDEFGHJKLMNPQRTUVWXY34679';

    public function __invoke()
    {
        $length = 5;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= self::CHARS[random_int(0, strlen(self::CHARS) - 1)];
        }
        session(['captcha_code' => $code]);

        $width = 200;
        $height = 66;
        $img = imagecreatetruecolor($width, $height);

        // ---- Background: soft light gradient ----
        for ($y = 0; $y < $height; $y++) {
            $t = $y / $height;
            imageline($img, 0, $y, $width, $y, imagecolorallocate(
                $img,
                (int) (243 - 18 * $t),
                (int) (247 - 14 * $t),
                (int) (253 - 6 * $t)
            ));
        }

        // ---- Gentle decorative dots ----
        for ($i = 0; $i < 45; $i++) {
            imagesetpixel($img, random_int(0, $width - 1), random_int(0, $height - 1),
                imagecolorallocate($img, random_int(200, 225), random_int(208, 230), random_int(232, 248)));
        }

        // ---- Soft lines (tidak menutupi karakter) ----
        for ($l = 0; $l < 2; $l++) {
            $c = imagecolorallocate($img, 176, 192, 224);
            imagearc($img, random_int(-20, $width), random_int(10, $height - 10), 260, 60, 0, 25, $c);
        }

        // ---- Karakter: gambar kecil → scale besar (tebal) → rotate ringan ----
        $inkPalette = [
            [23, 43, 99],
            [29, 78, 155],
            [67, 56, 165],
            [21, 60, 109],
        ];

        $slot = $width / $length;

        for ($i = 0; $i < $length; $i++) {
            $char = $code[$i];
            $ink = $inkPalette[array_rand($inkPalette)];

            // 1) gambar glyph font bawaan di kanvas kecil
            $sw = imagefontwidth(5);   // 9
            $sh = imagefontheight(5);  // 15
            $small = imagecreatetruecolor($sw, $sh);
            imagefill($small, 0, 0, imagecolorallocate($small, 243, 247, 253));
            imagestring($small, 5, 0, 0, $char, imagecolorallocate($small, ...$ink));

            // 2) perbesar ~2.2x → karakter tebal & sangat jelas
            $bigW = (int) ($sw * 2.2);
            $bigH = (int) ($sh * 2.2);
            $big = imagecreatetruecolor($bigW, $bigH);
            imagecopyresized($big, $small, 0, 0, 0, 0, $bigW, $bigH, $sw, $sh);

            // 3) rotate ringan dengan background senada
            $angle = random_int(-11, 11);
            $bgColor = imagecolorallocate($img, 235, 240, 250);
            $rot = imagerotate($big, $angle, $bgColor);

            // 4) tempel di slot
            $x = (int) ($slot * $i + ($slot - imagesx($rot)) / 2 + random_int(-3, 3));
            $y = (int) (($height - imagesy($rot)) / 2 + random_int(-3, 3));
            imagecopy($img, $rot, $x, $y, 0, 0, imagesx($rot), imagesy($rot));

            imagedestroy($small);
            imagedestroy($big);
            imagedestroy($rot);
        }

        // ---- Frame tipis ----
        imagerectangle($img, 0, 0, $width - 1, $height - 1, imagecolorallocate($img, 205, 216, 238));

        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);

        return response($data, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }
}
