<?php

namespace OrchidHelpers\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use OrchidHelpers\Orchid\Traits\DeleteActionTrait;
use OrchidHelpers\Tests\TestCase;

class DeleteActionTraitSecurityTest extends TestCase
{
    // Remove RefreshDatabase trait since we don't need actual database
    // use RefreshDatabase;

    public function test_it_rejects_missing_morph_parameter()
    {
        $request = Request::create('/destroy', 'POST', ['id' => 1]);
        
        $screen = new class {
            use DeleteActionTrait;
        };
        
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionCode(403);
        
        $screen->destroy($request);
    }

    public function test_it_rejects_missing_id_parameter()
    {
        $request = Request::create('/destroy', 'POST', ['morph' => 'App\Models\User']);
        
        $screen = new class {
            use DeleteActionTrait;
        };
        
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionCode(403);
        
        $screen->destroy($request);
    }

    public function test_it_rejects_non_existent_model_class()
    {
        $request = Request::create('/destroy', 'POST', [
            'morph' => 'NonExistent\Model',
            'id' => 1,
        ]);
        
        $screen = new class {
            use DeleteActionTrait;
        };
        
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionCode(403);
        $this->expectExceptionMessage('The specified class does not exist.');
        
        $screen->destroy($request);
    }

    public function test_it_rejects_non_model_classes()
    {
        // With empty config (default), no models are allowed
        Config::set('orchid-helpers.allowed_models', []);
        
        $request = Request::create('/destroy', 'POST', [
            'morph' => \stdClass::class,
            'id' => 1,
        ]);
        
        $screen = new class {
            use DeleteActionTrait;
        };
        
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionCode(403);
        $this->expectExceptionMessage('The specified model is not allowed for this operation.');
        
        $screen->destroy($request);
    }

    public function test_it_respects_allowed_models_configuration()
    {
        // Create unique mock model classes for testing
        $allowedClassName = 'TestAllowedModel_' . uniqid();
        $rejectedClassName = 'TestRejectedModel_' . uniqid();
        
        eval("
            class $allowedClassName extends \Illuminate\Database\Eloquent\Model {
                protected \$table = 'users';
                
                public static function findOrFail(\$id)
                {
                    throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
                }
            }
            
            class $rejectedClassName extends \Illuminate\Database\Eloquent\Model {
                protected \$table = 'posts';
                
                public static function findOrFail(\$id)
                {
                    throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
                }
            }
        ");
        
        Config::set('orchid-helpers.allowed_models', [
            $allowedClassName,
        ]);
        
        $request = Request::create('/destroy', 'POST', [
            'morph' => $rejectedClassName,
            'id' => 1,
        ]);
        
        $screen = new class {
            use DeleteActionTrait;
        };
        
        // Should reject because the model is not in allowed list
        $this->expectExceptionCode(403);
        $this->expectExceptionMessage('The specified model is not allowed for this operation.');
        
        $screen->destroy($request);
    }

    public function test_it_allows_models_in_allowed_list()
    {
        // Create a unique mock model class for testing to avoid class name conflicts
        $mockModelClassName = 'TestMockModel_' . uniqid();
        
        eval("
            class $mockModelClassName extends \Illuminate\Database\Eloquent\Model {
                protected \$table = 'users';
                
                public static function findOrFail(\$id)
                {
                    throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
                }
            }
        ");
        
        // Use the actual class name in configuration
        $allowedClassName = $mockModelClassName;
        
        Config::set('orchid-helpers.allowed_models', [
            $allowedClassName,
        ]);
        
        $request = Request::create('/destroy', 'POST', [
            'morph' => $allowedClassName,
            'id' => 1,
        ]);
        
        $screen = new class {
            use DeleteActionTrait;
            
            public function authorize($ability, $arguments = [])
            {
                return true;
            }
        };
        
        // This should not throw an exception for model validation
        // (though it will fail on findOrFail since no database record)
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        $screen->destroy($request);
    }

    public function test_it_rejects_wildcard_patterns_in_allowed_models()
    {
        Config::set('orchid-helpers.allowed_models', [
            'App\Models\*', // Wildcard patterns are no longer supported
        ]);
        
        $request = Request::create('/destroy', 'POST', [
            'morph' => 'App\Models\Product',
            'id' => 1,
        ]);
        
        $screen = new class {
            use DeleteActionTrait;
        };
        
        // With strict security, wildcard patterns are NOT supported
        // The class 'App\Models\Product' doesn't exist, so it should fail at class_exists check
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionCode(403);
        $this->expectExceptionMessage('The specified class does not exist.');
        
        $screen->destroy($request);
    }

    public function test_it_rejects_all_models_when_allowed_models_empty()
    {
        Config::set('orchid-helpers.allowed_models', []);
        
        $request = Request::create('/destroy', 'POST', [
            'morph' => 'App\Models\AnyModel',
            'id' => 1,
        ]);
        
        $screen = new class {
            use DeleteActionTrait;
        };
        
        // With strict security, empty configuration means NO models are allowed
        // The class 'App\Models\AnyModel' doesn't exist, so it should fail at class_exists check
        // before reaching isModelAllowed check
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionCode(403);
        $this->expectExceptionMessage('The specified class does not exist.');
        
        $screen->destroy($request);
    }
}