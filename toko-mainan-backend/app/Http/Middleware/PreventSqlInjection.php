<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventSqlInjection
{
    /**
     * Handle an incoming request.
     * Detect and block common SQL injection patterns
     */
    public function handle(Request $request, Closure $next)
    {
        $suspiciousPatterns = [
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bDROP\b.*\bTABLE\b)/i',
            '/(\bINSERT\b.*\bINTO\b)/i',
            '/(\bUPDATE\b.*\bSET\b)/i',
            '/(\bDELETE\b.*\bFROM\b)/i',
            '/(\bEXEC\b|\bEXECUTE\b)/i',
            '/(\'|\")(\s*)(OR|AND)(\s*)(\'|\")(\s*)=(\s*)(\'|\")/i',
            '/(\-\-|\#|\/\*|\*\/)/i',
            '/(\bxp_cmdshell\b)/i',
            '/(\bSCRIPT\b.*\>)/i',
        ];

        // Check all input data
        $allInputs = $request->all();
        
        foreach ($allInputs as $key => $value) {
            if (is_string($value)) {
                foreach ($suspiciousPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        \Log::warning('SQL Injection attempt detected', [
                            'ip' => $request->ip(),
                            'input' => $key,
                            'value' => $value,
                            'url' => $request->fullUrl()
                        ]);
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid input detected'
                        ], 400);
                    }
                }
            }
        }

        return $next($request);
    }
}
