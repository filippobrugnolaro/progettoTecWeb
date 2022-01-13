<?php
    namespace UTILS;

    function sanitizeInputString($string): string {
        $string = trim($string); //delete extra spaces
        $string = preg_replace("/\s+/"," ", $string); //delete more consecutive spaces
        $string = strip_tags($string,array("<strong>","<span>")); //delete HTML tags
        $string = htmlentities($string); //convert special entities

        return $string;
    }

    function checkInputValidity(string $string, ?string $pattern = null): int {
        if(strlen($string) == 0)
            return 1;

        if($pattern !== null && !preg_match($pattern,$string))
            return 2;

        return 0;
    }
?>