<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;
use Hashids\Hashids;

class CheckBusinessOwner
{
    public function handle($request, Closure $next)
    {
        $encodedId = $request->route('business_id')
                    ?? $request->route('id');

        try {
            $hashids = new Hashids(config('app.key'), 10);
            $decoded = $hashids->decode($encodedId);
            if (empty($decoded)) {
                return redirect()->route('dashboard');
            }

            $businessId = $decoded[0];

            $business = Business::find($businessId);
            if(Auth::user()->role == 1){
                if (!$business ||
                    Auth::id() !== $business->user_id 
                    ) {
                        return redirect()->route('dashboard')
                            ->with('error', 'Unauthorized access');
                }
            }
            

        } catch (\Exception $e) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
