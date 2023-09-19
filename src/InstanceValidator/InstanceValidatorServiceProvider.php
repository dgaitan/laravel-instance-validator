<?php

namespace DGaitan\InstanceValidator;

use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class InstanceValidatorServiceProvider extends ServiceProvider {
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'instance-validator');

        $this->publishes([
            __DIR__ . '/../../resources/lang' => resource_path('lang/vendor/instance-validator'),
        ]);

        Validator::extend('instance_of', function ($attribute, $value, $parameters, $validator) {
            if (count($parameters) != 1) {
                throw new Exception("The 'instance_of' validator requires a single type to be specified.");
            }

            return $value instanceof $parameters[0];
        });

        Validator::replacer('instance_of', function ($message, $attribute, $rule, $parameters) {
            $msg = Lang::get('instance-validator::' . $message);
            $msg = str_replace([':attribute', ':type'], [$attribute, $parameters[0]], $msg);
            return $msg;
        });

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        Validator::extend('collection_of', function ($attribute, $value, $parameters, $validator) {
            if (count($parameters) != 1) {
                throw new Exception("The 'collection_of' validator requires a single type to be specified.");
            }

            if (!$value instanceof Collection) {
                throw new Exception("The 'collection_of' validator requires a Collection instance.");
            }

            if ($value->isEmpty()) return true;

            $itemIsCorrectType = $value[0] instanceof $parameters[0] || (strtolower($parameters[0]) === 'string' && is_string($value[0]));

            return $itemIsCorrectType;
        });

        Validator::replacer('collection_of', function ($message, $attribute, $rule, $parameters) {
            $msg = Lang::get('instance-validator::' . $message);
            $msg = str_replace([':attribute', ':type'], [$attribute, $parameters[0]], $msg);
            return $msg;
        });

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        Validator::extend('paginator_of', function ($attribute, $value, $parameters, $validator) {
            if (count($parameters) != 1) {
                throw new Exception("The 'paginator_of' validator requires a single type to be specified.");
            }

            if (!$value instanceof LengthAwarePaginator) {
                throw new Exception("The 'paginator_of' validator requires a LengthAwarePaginator instance.");
            }

            if ($value->isEmpty()) return true;

            $itemIsCorrectType = $value->items()[0] instanceof $parameters[0] || (strtolower($parameters[0]) === 'string' && is_string($value->items()[0]));

            return $itemIsCorrectType;
        });

        Validator::replacer('paginator_of', function ($message, $attribute, $rule, $parameters) {
            $msg = Lang::get('instance-validator::' . $message);
            $msg = str_replace([':attribute', ':type'], [$attribute, $parameters[0]], $msg);
            return $msg;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        //
    }
}
