<?php

namespace Orchid\Helpers\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Orchid\Helpers\Orchid\Traits\DeleteActionTrait;
use Orchid\Helpers\Tests\TestCase;

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

    public function test_it_allows_concrete_eloquent_models()
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
        
        $request = Request::create('/destroy', 'POST', [
            'morph' => $mockModelClassName,
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

    public function test_it_rejects_abstract_classes_and_interfaces()
    {
        // Create an abstract model class
        $abstractClassName = 'TestAbstractModel_' . uniqid();
        
        eval("
            abstract class $abstractClassName extends \Illuminate\Database\Eloquent\Model {
                protected \$table = 'users';
            }
        ");
        
        $request = Request::create('/destroy', 'POST', [
            'morph' => $abstractClassName,
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
}
