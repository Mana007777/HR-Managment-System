<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasCompanyContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('company_id')) {
            return $next($request);
        }

        session()->flash('error', 'Please select a company to access this section.');

        return redirect()->route('companies.index');
    }
}