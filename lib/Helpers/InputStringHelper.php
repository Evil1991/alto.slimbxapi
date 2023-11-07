<?php

namespace Alto\Slimbxapi\Helpers;

class InputStringHelper
{
    public static function formatPhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        if(!preg_match("/^[0-9]{10,11}+$/", $phoneNumber)) {
            return 'error';
        }

        $first = substr($phoneNumber, "0",1);

        if ($first == "8") {
            $phoneNumber = substr_replace($phoneNumber, "7", 0, 1);
        }

        return $phoneNumber;
    }

    public static function formatNumberTwoDecimals($number)
    {
        return number_format(floatval($number), 2, '.', '');
    }
}