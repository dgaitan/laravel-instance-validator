<?php

namespace TimMcLeod\InstanceValidator;

use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class InstanceValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'instance-validator');

        Validator::extend('instance_of', function ($attribute, $value, $parameters, $validator)
        {
            if (count($parameters) != 1)
            {
                throw new Exception("The 'instance_of' validator requires a single type to be specified.");
            }

            return $value instanceof $parameters[0];
        });

        Validator::replacer('instance_of', function ($message, $attribute, $rule, $parameters)
        {
            $msg = Lang::trans('instance-validator::' . $message);
            $msg = str_replace([':attribute', ':type'], [$attribute, $parameters[0]], $msg);
            return $msg;
        });

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        Validator::extend('collection_of', function ($attribute, $value, $parameters, $validator)
        {
            if (count($parameters) != 1)
            {
                throw new Exception("The 'collection_of' validator requires a single type to be specified.");
            }

            $isCollection = $value instanceof Collection;
            $itemIsCorrectType = $value[0] instanceof $parameters[0];

            return $isCollection && $itemIsCorrectType;
        });

        Validator::replacer('collection_of', function ($message, $attribute, $rule, $parameters)
        {
            $msg = Lang::trans('instance-validator::' . $message);
            $msg = str_replace([':attribute', ':type'], [$attribute, $parameters[0]], $msg);
            return $msg;
        });

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        Validator::extend('paginator_of', function ($attribute, $value, $parameters, $validator)
        {
            if (count($parameters) != 1)
            {
                throw new Exception("The 'paginator_of' validator requires a single type to be specified.");
            }

            if(!$value instanceof LengthAwarePaginator)
            {
                throw new Exception("The 'paginator_of' validator requires a LengthAwarePaginator instance.");
            }

            $itemIsCorrectType = $value->items()[0] instanceof $parameters[0];

            return $itemIsCorrectType;
        });

        Validator::replacer('paginator_of', function ($message, $attribute, $rule, $parameters)
        {
            $msg = Lang::trans('instance-validator::' . $message);
            $msg = str_replace([':attribute', ':type'], [$attribute, $parameters[0]], $msg);
            return $msg;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
