<?php
namespace Laravolt\Password;

use Closure;

class CheckPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(auth()->guest() || $this->shouldPassThrough($request)) {
            return $next($request);
        }

        if(auth()->user()->passwordMustBeChanged(config('password.duration'))) {
            return redirect(config('password.redirect'))->withWarning(trans('password::password.must_change_password'));
        }

        return $next($request);
    }

    protected function shouldPassThrough($request)
    {
        $except = array_merge((array) config('password.except'), (array) config('password.redirect'));

        foreach ($except as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }

}
