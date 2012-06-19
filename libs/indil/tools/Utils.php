<?php

class Utils {

    public static function makePath(array $path) {
        return implode(DIRECTORY_SEPARATOR, $path);
    }
    
    public static function getUrl($path) {
        $url = str_replace(WWW_DIR, '', $path);
        return str_replace(DIRECTORY_SEPARATOR, '/', $url);
    }
    
    public static function getMediaUrl($path) {
        if ($path === MEDIA_DIR) {
            return '/';
        } else {
            $url = str_replace(MEDIA_DIR, '', $path);
            return str_replace(DIRECTORY_SEPARATOR, '/', $url);
        }
    }
    
    public static function getAbsPath($url) {
        $absPath = WWW_DIR . $url;
        return str_replace(DIRECTORY_SEPARATOR, '/', $absPath);
    }

    public static function czechDay($str) {
        $weekDays = array(
            'Monday' => 'Pondělí', 'Tuesday' => 'Úterý', 'Wednesday' => 'Středa', 'Thursday' => 'Čtvrtek',
            'Friday' => 'Pátek', 'Saturday' => 'Sobota', 'Sunday' => 'Neděle'
        );
        foreach ($weekDays as $en => $cs) {
            $str = str_replace($en, $cs, $str);
        }
        return $str;
    }

    public static function czechMonth($str) {
        $months = array(
            'January' => 'Leden', 'February' => 'Únor', 'March' => 'Březen', 'April' => 'Duben',
            'May' => 'Květen', 'June' => 'Červen', 'July' => 'Červenec', 'August' => 'Srpen',
            'September' => 'Září', 'October' => 'Říjen', 'November' => 'Listopad', 'December' => 'Prosince'
        );
        foreach ($months as $en => $cs) {
            $str = str_replace($en, $cs, $str);
        }
        return $str;
    }

}