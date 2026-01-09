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

            $business = Business::find($decoded[0]);
            if (
                Auth::user()->role == 1 &&
                (
                    !$business ||
                    Auth::id() !== $business->user_id
                )
            ) {
                return redirect()->route('dashboard')
                    ->with('error', 'Unauthorized access');
            }
            if (Auth::user()->role == 1 && $business) {

                $routeName = $request->route()->getName();
                $status    = $business->status;
                if (
                    $routeName === 'business.edit' &&
                    !in_array($status, ['DRAFT', 'RETURNED', 'RETURNED'])
                ) {
                    return redirect()->route('business.view', $encodedId)
                        ->with('error', 'You cannot edit this business');
                }
                if (
                    $routeName === 'business.view' &&
                    in_array($status, ['DRAFT', 'RETURNED', 'RETURNED'])
                ) {
                    return redirect()->route('business.edit', $encodedId)
                        ->with('error', 'Please complete and submit the business');
                }
            }

        } catch (\Exception $e) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
