<?php
    namespace UTILS;

    function sanitizeInputString($string): string {
        $string = trim($string); //delete extra spaces
        $string = preg_replace("/\s+/"," ", $string); //delete more consecutive spaces
        $string = strip_tags($string,array("<strong>","<span>")); //delete HTML tags
        $string = htmlentities($string); //convert special entities

        return $string;
    }
?>