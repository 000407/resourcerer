<?php


class TestController extends BaseController
{
    public function validate($value){
        OTPUtility::verify("0778007867", $value);
    }

    public function generate() {
        $otp = OTPUtility::generate("0778007867");
        echo($otp->value);
    }

    public function test($id) {
        echo "SERVER_RESPONSE " . $id;
    }
}