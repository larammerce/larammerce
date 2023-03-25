<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller {
    public function getDBVersion(Request $request){
        $final_string = "#".DB::table("users")
            ->select("updated_at")
            ->orderBy("updated_at", "desc")
            ->take(1)
            ->pluck("updated_at")
            ->first();
        $final_string .= "#".DB::table("products")
            ->select("updated_at")
            ->orderBy("updated_at", "desc")
            ->take(1)
            ->pluck("updated_at")
            ->first();
        $final_string .= "#".DB::table("invoices")
            ->select("updated_at")
            ->orderBy("updated_at", "desc")
            ->take(1)
            ->pluck("updated_at")
            ->first();
        $final_string .= "#".DB::table("directories")
            ->select("updated_at")
            ->orderBy("updated_at", "desc")
            ->take(1)
            ->pluck("updated_at")
            ->first();
        die(hash("sha256", $final_string));
    }
}