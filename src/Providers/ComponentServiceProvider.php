<?php

declare(strict_types=1);

namespace Orchid\Helpers\Providers;

use Illuminate\Support\ServiceProvider;
use Orchid\\Helpers\\View\Components\Platform\AlertComponent;
use Orchid\\Helpers\\View\Components\Platform\BadgeComponent;
use Orchid\\Helpers\\View\Components\Platform\BoolComponent;
use Orchid\\Helpers\\View\Components\Platform\ButtonComponent;
use Orchid\\Helpers\\View\Components\Platform\CardComponent;
use Orchid\\Helpers\\View\Components\Platform\CheckboxComponent;
use Orchid\\Helpers\\View\Components\Platform\FormComponent;
use Orchid\\Helpers\\View\Components\Platform\InputComponent;
use Orchid\\Helpers\\View\Components\Platform\ModalComponent;
use Orchid\\Helpers\\View\Components\Platform\RadioComponent;
use Orchid\\Helpers\\View\Components\Platform\SelectComponent;
use Orchid\\Helpers\\View\Components\Platform\TableComponent;
use Orchid\\Helpers\\View\Components\Platform\TextareaComponent;

class ComponentServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        $this->registerBladeComponents();
    }

    private function registerBladeComponents() : void
    {
        $this->loadViewComponentsAs('orchid-helpers', [
            'alert' => AlertComponent::class,
            'badge' => BadgeComponent::class,
            'bool' => BoolComponent::class,
            'button' => ButtonComponent::class,
            'card' => CardComponent::class,
            'checkbox' => CheckboxComponent::class,
            'form' => FormComponent::class,
            'input' => InputComponent::class,
            'modal' => ModalComponent::class,
            'radio' => RadioComponent::class,
            'select' => SelectComponent::class,
            'table' => TableComponent::class,
            'textarea' => TextareaComponent::class,
        ]);
    }
}
