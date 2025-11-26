<?php

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

if (!function_exists('formatDatePH')) {
    function formatDatePH($date)
    {
        if (empty($date)) {
            return '';  
        }

        return Carbon::parse($date)->format('d-m-Y');
    }
}
if (!function_exists('getClientIp')) {
    function getClientIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        }
    }
}

if (!function_exists('saveUserLogs')) {
    function saveUserLogs($latitude, $longitude,$busn_id='',$actionId = 0, $actionName = '', $message = '', $status = '',$remarks='')
    {
        $userlogs = [
            'action_id' => $actionId,
            'action_name' => $actionName,
            'busn_id'=> $busn_id,
            'message' => $message,
            'public_ip_address' => getClientIp(),
            'status' => $status,
            'remarks' => $remarks,
            'longitude' => $longitude,
            'latitude' => $latitude,
            'created_by' => Auth::id(),
            'created_by_name' => Auth::user()->name ?? 'Guest',
            'created_date' => now(),
        ];

        DB::table('user_logs')->insert($userlogs);
    }
}