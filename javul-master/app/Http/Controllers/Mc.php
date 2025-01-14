<?php namespace App\Http\Controllers;
/**
	* Name 			: Math Captcha
	* Description  	: Mc is very simple and lightweight captcha function. You can easily configure it in your php project
	* Make date 	: 29/08/2015
	* Author 		: Md.Harun-Ur-Rashid
	* Author website: http://learn24bd.com
	* Facebook		: http://facebook.com/haruncpi
	* Licence		: MIT
*/
use Illuminate\Support\Facades\Session;


class Mc
{
    private $num1;
    private $num2;
    private $actionNumber;
    private $arr;

    private function generate()
    {
        $this->num1 = rand(2, 5);
        $this->num2 = rand(6, 9);
        $this->actionNumber = rand(1, 3);
        $this->ar = [];
        $this->arr['num1'] = $this->num1;
        $this->arr['num2'] = $this->num2;

        switch ($this->actionNumber)
        {
            case 1:
                $this->arr['question'] = $this->num1 . " + " . $this->num2 . " = ? ";
                $this->arr['answer'] = $this->addition($this->num1, $this->num2);
                return $this->arr;
                break;

            case 2:
                $this->arr['question'] = $this->num2 . " - " . $this->num1 . " = ? ";
                $this->arr['answer'] = $this->substruction($this->num1, $this->num2);
                return $this->arr;
                break;

            case 3:
                $this->arr['question'] = $this->num1 . " * " . $this->num2 . " = ? ";
                $this->arr['answer'] = $this->multiplication($this->num1, $this->num2);
                return $this->arr;
                break;
        }
    }

    private function addition($num1, $num2)
    {
        return $num1 + $num2;
    }

    private function substruction($num1, $num2)
    {
        return $num2 - $num1;
    }

    private function multiplication($num1, $num2)
    {
        return $num1 * $num2;
    }


    public static function putMcData()
    {
        $instance = new self();
        Session::put('mcData',$instance->generate());
    }

    public static function getMcQuestion()
    {
        return Session::get('mcData')['question'];
    }

    public static function getMcAnswer()
    {
        return Session::get('mcData')['answer'];
    }
}







