<?php
namespace App\Http\Controllers;
class generate_UUID extends Controller
{
    public function generate_12($uuid) {
        return (explode("-",$uuid))[4];
    }
    public function my_uniqe_uuid_generate($digit){
        $charecters=array_merge(range('A', 'Z'), range('a', 'z'));
        $uuid = "";
        for ($i=0; $i < $digit; $i++) {
            $int_char = mt_rand(0, 1);
            if ($int_char === 1 ) {
                $uuid = $uuid . $charecters[mt_rand(0, 51)];
            }else{
                $uuid = $uuid . mt_rand(0, 9);
            }
        }
        return($uuid);
    }
}