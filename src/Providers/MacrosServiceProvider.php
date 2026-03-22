<?php

declare(strict_types=1);

namespace OrchidHelpers\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Form;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Field;

class MacrosServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        $this->registerOrchidMacros();
        $this->registerCollectionMacros();
        $this->registerStringMacros();
        $this->registerArrayMacros();
        $this->registerQueryBuilderMacros();
        $this->registerModelMacros();
        $this->registerResponseMacros();
        $this->registerRequestMacros();
        $this->registerRouteMacros();
        $this->registerViewMacros();
        $this->registerFileMacros();
        $this->registerStorageMacros();
        $this->registerValidatorMacros();
        $this->registerSchemaMacros();
        $this->registerDatabaseMacros();
        $this->registerCacheMacros();
        $this->registerSessionMacros();
        $this->registerCookieMacros();
        $this->registerMailMacros();
        $this->registerQueueMacros();
        $this->registerEventMacros();
        $this->registerLogMacros();
        $this->registerConfigMacros();
        $this->registerUrlMacros();
        $this->registerHtmlMacros();
        $this->registerFormMacros();
        $this->registerPaginatorMacros();
        $this->registerCarbonMacros();
    }

    protected function registerOrchidMacros(): void
    {
        if(!Link::hasMacro('can')) {
            Link::macro('can', function($ability, $arguments = []) : Action {
                $this->canSee(auth()->user()?->can($ability, $arguments) ?? false);
                return $this;
            });
        }

        if(!Field::hasMacro('can')) {
            Field::macro('can', function($ability, $arguments = []) : Field {
                $this->canSee(auth()->user()?->can($ability, $arguments) ?? false);
                return $this;
            });
        }
    }

    protected function registerCollectionMacros(): void
    {
        if (!Collection::hasMacro('toUpper')) {
            Collection::macro('toUpper', function () {
                return $this->map(function ($value) {
                    return is_string($value) ? strtoupper($value) : $value;
                });
            });
        }
    }

    protected function registerStringMacros(): void
    {
        if (!Str::hasMacro('toTitleCase')) {
            Str::macro('toTitleCase', function ($value) {
                return ucwords(strtolower($value));
            });
        }
    }

    protected function registerArrayMacros(): void
    {
        if (!Arr::hasMacro('dotFlatten')) {
            Arr::macro('dotFlatten', function ($array, $prepend = '') {
                $results = [];
                foreach ($array as $key => $value) {
                    if (is_array($value) && !empty($value)) {
                        $results = array_merge($results, Arr::dotFlatten($value, $prepend.$key.'.'));
                    } else {
                        $results[$prepend.$key] = $value;
                    }
                }
                return $results;
            });
        }
    }

    protected function registerQueryBuilderMacros(): void
    {
        // Register macro without hasMacro check to avoid static method issue
        Builder::macro('whereLike', function ($column, $value) {
            return $this->where($column, 'LIKE', "%{$value}%");
        });
    }

    protected function registerModelMacros(): void
    {
        if (!Model::hasMacro('scopeActive')) {
            Model::macro('scopeActive', function ($query) {
                return $query->where('active', true);
            });
        }
    }

    protected function registerResponseMacros(): void
    {
        if (!Response::hasMacro('success')) {
            Response::macro('success', function ($data = null, string $message = 'Success', int $status = 200) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $data,
                ], $status);
            });
        }
    }

    protected function registerRequestMacros(): void
    {
        if (!Request::hasMacro('wantsJson')) {
            Request::macro('wantsJson', function () {
                return $this->expectsJson() || $this->ajax() || $this->wantsJson();
            });
        }
    }

    protected function registerRouteMacros(): void
    {
        if (!Route::hasMacro('resourceApi')) {
            Route::macro('resourceApi', function ($name, $controller, array $options = []) {
                $options = array_merge([
                    'only' => ['index', 'store', 'show', 'update', 'destroy'],
                    'names' => [
                        'index' => "{$name}.index",
                        'store' => "{$name}.store",
                        'show' => "{$name}.show",
                        'update' => "{$name}.update",
                        'destroy' => "{$name}.destroy",
                    ],
                ], $options);
                return Route::resource($name, $controller, $options);
            });
        }
    }

    protected function registerViewMacros(): void
    {
        if (!View::hasMacro('shareGlobal')) {
            View::macro('shareGlobal', function ($key, $value = null) {
                if (is_array($key)) {
                    foreach ($key as $k => $v) {
                        View::share($k, $v);
                    }
                } else {
                    View::share($key, $value);
                }
            });
        }
    }

    protected function registerFileMacros(): void
    {
        if (!File::hasMacro('safePut')) {
            File::macro('safePut', function ($path, $contents, $lock = false) {
                $directory = dirname($path);
                if (!File::isDirectory($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }
                return File::put($path, $contents, $lock);
            });
        }
    }

    protected function registerStorageMacros(): void
    {
        if (!Storage::hasMacro('urlOrNull')) {
            Storage::macro('urlOrNull', function ($path) {
                try {
                    return Storage::url($path);
                } catch (\Exception $e) {
                    return null;
                }
            });
        }
    }

    protected function registerValidatorMacros(): void
    {
        if (!Validator::hasMacro('validateOrFail')) {
            Validator::macro('validateOrFail', function () {
                if ($this->fails()) {
                    throw new \Illuminate\Validation\ValidationException($this);
                }
                return $this->validated();
            });
        }
    }

    protected function registerSchemaMacros(): void
    {
        if (!Schema::hasMacro('hasIndex')) {
            Schema::macro('hasIndex', function ($table, $index) {
                $connection = Schema::getConnection();
                $database = $connection->getDatabaseName();
                $result = $connection->select(
                    "SELECT COUNT(*) as count FROM information_schema.statistics 
                     WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                    [$database, $table, $index]
                );
                return $result[0]->count > 0;
            });
        }
    }

    protected function registerDatabaseMacros(): void
    {
        if (!DB::hasMacro('transactional')) {
            DB::macro('transactional', function (callable $callback, int $attempts = 1) {
                return DB::transaction($callback, $attempts);
            });
        }
    }

    protected function registerCacheMacros(): void
    {
        if (!Cache::hasMacro('rememberForeverWithTags')) {
            Cache::macro('rememberForeverWithTags', function ($tags, $key, callable $callback) {
                return Cache::tags($tags)->rememberForever($key, $callback);
            });
        }
    }

    protected function registerSessionMacros(): void
    {
        if (!Session::hasMacro('flashSuccess')) {
            Session::macro('flashSuccess', function ($message) {
                Session::flash('success', $message);
            });
        }
    }

    protected function registerCookieMacros(): void
    {
        if (!Cookie::hasMacro('forever')) {
            Cookie::macro('forever', function ($name, $value, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null) {
                return Cookie::make($name, $value, 525600, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
            });
        }
    }

    protected function registerMailMacros(): void
    {
        if (!Mail::hasMacro('sendWithQueue')) {
            Mail::macro('sendWithQueue', function ($mailable, $queue = null) {
                return Mail::toQueue($mailable, $queue);
            });
        }
    }

    protected function registerQueueMacros(): void
    {
        if (!Queue::hasMacro('pushUnique')) {
            Queue::macro('pushUnique', function ($job, $data = '', $queue = null) {
                $payload = json_encode($job->payload());
                $key = md5($payload);
                if (!Cache::has("queue:unique:{$key}")) {
                    Cache::put("queue:unique:{$key}", true, 3600);
                    return Queue::push($job, $data, $queue);
                }
                return null;
            });
        }
    }

    protected function registerEventMacros(): void
    {
        if (!Event::hasMacro('dispatchIf')) {
            Event::macro('dispatchIf', function ($condition, $event, $payload = [], $halt = false) {
                if ($condition) {
                    return Event::dispatch($event, $payload, $halt);
                }
                return null;
            });
        }
    }

    protected function registerLogMacros(): void
    {
        if (!Log::hasMacro('debugDump')) {
            Log::macro('debugDump', function ($message, $data) {
                Log::debug($message . ': ' . var_export($data, true));
            });
        }
    }

    protected function registerConfigMacros(): void
    {
        if (!Config::hasMacro('getOrSet')) {
            Config::macro('getOrSet', function ($key, $default = null) {
                $value = Config::get($key);
                if ($value === null) {
                    Config::set($key, $default);
                    return $default;
                }
                return $value;
            });
        }
    }

    protected function registerUrlMacros(): void
    {
        if (!URL::hasMacro('secureRoute')) {
            URL::macro('secureRoute', function ($name, $parameters = [], $absolute = true) {
                return URL::route($name, $parameters, $absolute, true);
            });
        }
    }

    protected function registerHtmlMacros(): void
    {
        if (!HTML::hasMacro('linkRoute')) {
            HTML::macro('linkRoute', function ($name, $title = null, $parameters = [], $attributes = []) {
                $title = $title ?: $name;
                $url = URL::route($name, $parameters);
                return '<a href="' . $url . '"' . HTML::attributes($attributes) . '>' . HTML::entities($title) . '</a>';
            });
        }
    }

    protected function registerFormMacros(): void
    {
        if (!Form::hasMacro('openRoute')) {
            Form::macro('openRoute', function ($name, $parameters = [], $attributes = [], $method = 'POST') {
                $url = URL::route($name, $parameters);
                return Form::open(array_merge(['url' => $url, 'method' => $method], $attributes));
            });
        }
    }

    protected function registerPaginatorMacros(): void
    {
        if (!Paginator::hasMacro('simplePaginate')) {
            Paginator::macro('simplePaginate', function ($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null) {
                $page = $page ?: Paginator::resolveCurrentPage($pageName);
                $total = $this->count();
                $results = $this->forPage($page, $perPage)->get($columns);
                return new \Illuminate\Pagination\LengthAwarePaginator(
                    $results,
                    $total,
                    $perPage,
                    $page,
                    [
                        'path' => Paginator::resolveCurrentPath(),
                        'pageName' => $pageName,
                    ]
                );
            });
        }
    }

    protected function registerCarbonMacros(): void
    {
        if (!Carbon::hasMacro('startOfBusinessDay')) {
            Carbon::macro('startOfBusinessDay', function () {
                return $this->setTime(9, 0, 0);
            });
        }
    }
}