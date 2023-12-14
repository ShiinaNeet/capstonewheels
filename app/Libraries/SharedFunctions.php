<?php

namespace App\Libraries;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

class SharedFunctions
{
    public static function default_msg()
    {
        return [
            'status'    => 0,
            'title'     => "Oops!",
            'text'      => "Something went wrong",
            'type'      => 'error'
        ];
    }

    public static function success_msg($message = "Success")
    {
        return [
            'status'    => 1,
            'title'     => "Success!",
            'text'      => $message,
            'type'      => 'success'
        ];
    }

    public static function prompt_msg($message = "Invalid")
    {
        return[
            'status' => 0,
            'title' => 'Oops!',
            'text' => $message,
            'type' => 'error'
        ];
    }

    public static function array_contains(array $arr, $str)
    {
        foreach($arr as $a) {
            if (stripos($a, $str) !== false) return true;
        }
        return false;
    }

    public static function create_audit_log($category, $action_type, $description)
    {
        $query                     = new AuditTrail();
        $query->action_user_id     = Auth::id() ? Auth::id() : AuditTrail::USER_SYSTEM;
        $query->category           = $category;
        $query->action_type        = $action_type;
        $query->action_description = $description;
        $query->save();
    }

    public static function generate_random_string($length = 10, $uppercase_chars = false, $lowercase_chars = false, $special_chars = false)
    {
        $characters = $random_string = "";

        if ($uppercase_chars) $characters .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        if ($lowercase_chars) $characters .= "abcdefghijklmnopqrstuvwxyz";
        $characters .= "0123456789";
        if ($special_chars) $characters .= "!@#$%^&*()";

        for ($i = 0; $i < $length; $i++) {
            $random_string .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $random_string;
    }

    public static function query_log($builder)
    {
        $query = str_replace(array('?'), array('\'%s\''), $builder->toSql());
        $query = vsprintf($query, $builder->getBindings());
        dd($query);
    }
}
