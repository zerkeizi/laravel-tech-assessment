<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exposes the authenticated user's id to the current DB connection as the
 * session variable "current_user_id", so the parties_log AFTER UPDATE
 * trigger can attribute the change instead of defaulting to user 1.
 *
 * Always sets it (to the user id, or NULL when unauthenticated) rather than
 * only when authenticated, so a request never inherits a stale value left
 * behind by a previous request on a reused connection (persistent
 * connections, Octane, etc). The DB connection isn't pooled today, but this
 * keeps the trigger correct if that ever changes.
 */
class SetDatabaseAuditUser
{
    public function handle(Request $request, Closure $next): Response
    {
        DB::statement('SET @current_user_id = ?', [Auth::id()]);

        return $next($request);
    }
}
