<?php

if(!function_exists('generateRandomCharacter')){
    function generateRandomCharacter($length = 4, $charType = 0){
        $characterGroups    =	[
            '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            '0123456789',
            'abcdefghijklmnopqrstuvwxyz',
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        ];
        $charactersLength	=	strlen($characterGroups[$charType]);
        $randomCharacter    =	'';

        for ($i = 0; $i < $length; $i++) {
            $randomCharacter .=	$characterGroups[$charType][rand(0, $charactersLength - 1)];
        }

        return $randomCharacter;
    }
}

if(!function_exists('createIPay88Signature')){
    function createIPay88Signature($refNumber, $amount, $currency){
        $hashEncode =   hash('sha256', "||".getenv('IPAY88_MERCHANT_KEY')."||".getenv('IPAY88_MERCHANT_CODE')."||".$refNumber."||".$amount."||".$currency."||");

        return $hashEncode;
    }
}

if(!function_exists('urlsafe_b64encode')){
    function urlsafe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }
}

if(!function_exists('urlsafe_b64decode')){
    function urlsafe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
}

if(!function_exists('generateCaptchaImage')){
    function generateCaptchaImage($captchaCode, $codeLength) {
        try {
            $image_height   =   60;
            $image_width    =   (32*4)+30;
            $image          =   imagecreate($image_width, $image_height);

            imagecolorallocate($image, 255 ,255, 255);

            for ($i=1; $i<=$codeLength;$i++){
                $font_size  =   rand(22,27);
                $r          =   rand(0,255);
                $g          =   rand(0,255);
                $b          =   rand(0,255);
                $index      =   rand(1,10);
                $x          =   15+(30*($i-1));
                $x          =   rand($x-5,$x+5);
                $y          =   rand(35,45);
                $o          =   rand(-30,30);
                $font_color =   imagecolorallocate($image, $r ,$g, $b);
                imagettftext($image, $font_size, $o, $x, $y,  $font_color, APPPATH.'Helpers/font-captcha/'.$index.'.ttf', $captchaCode[$i-1]);
            }

            for($i=1; $i<=30;$i++){
                $x1         =   rand(1,150);
                $y1         =   rand(1,150);
                $x2         =   rand(1,150);
                $y2         =   rand(1,150);
                $r          =   rand(0,255);
                $g          =   rand(0,255);
                $b          =   rand(0,255);
                $font_color =   imagecolorallocate($image, $r ,$g, $b);
                imageline($image, $x1, $y1, $x2, $y2, $font_color);
            }

            ob_start();
            imagejpeg($image);
            $contents = ob_get_contents();
            ob_end_clean();

            $dataUri = "data:image/jpeg;base64," . base64_encode($contents);
            echo $dataUri;
        } catch (\Throwable $th) {
            var_dump($th);
        }
    }
}

if (!function_exists('detectDateFormat')) {
    /**
     * Detect date format and create DateTime object
     *
     * @param string $dateString
     * @return DateTime|false
     */
    function detectDateFormat(string $dateString){
        $formats    =   [
            'Y-m-d',    // 2024-12-07
            'd-m-Y',    // 07-12-2024
            'm/d/Y',    // 12/07/2024
            'd/m/Y',    // 07/12/2024
            'Y/m/d',    // 2024/12/07
            'd.m.Y',    // 07.12.2024
            'd M Y',    // 07 Dec 2024
            'd F Y',    // 07 December 2024
            'j/n/Y',    // 30/12/2024
            'j-n-Y',    // 30-12-2024
            'n/j/Y',    // 30/12/2024
            'n-j-Y',    // 30-12-2024
        ];

        foreach ($formats as $format) {
            $dateTime = DateTime::createFromFormat($format, $dateString);
            if ($dateTime && $dateTime->format($format) === $dateString) {
                return $dateTime;
            }
        }
        return false;
    }
}
