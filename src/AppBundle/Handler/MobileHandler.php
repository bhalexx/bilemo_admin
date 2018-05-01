<?php

namespace AppBundle\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class MobileHandler
{
	public function handle($mobile, $manufacturers, $oss)
    {
    	//Set manufacturer object to mobile
        $manufacturer = null;
        foreach($manufacturers as $obj) {
            if ($obj['id'] == $mobile['manufacturer']) {
                $manufacturer = [
                    'id' => $obj['id'],
                    'name' => $obj['name']
                ];
                break;
            }
        }
        $mobile['manufacturer'] = $manufacturer;

        //Set OS object to mobile
        $os = null;
        foreach($oss as $obj) {
            if ($obj['id'] == $mobile['os']) {
                $os = [
                    'id' => $obj['id'],
                    'name' => $obj['name']
                ];
                break;
            }
        }
        $mobile['os'] = $os;

        return $mobile;
    }
}
