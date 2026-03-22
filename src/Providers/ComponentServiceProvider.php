<?php

declare(strict_types=1);

namespace Orchid\Helpers\Providers;

use Illuminate\Support\ServiceProvider;
use OrchidHelpers\\View\Components\Platform\AlertComponent;
use OrchidHelpers\\View\Components\Platform\BadgeComponent;
use OrchidHelpers\\View\Components\Platform\BoolComponent;
use OrchidHelpers\\View\Components\Platform\ButtonComponent;
use OrchidHelpers\\View\Components\Platform\CardComponent;
use OrchidHelpers\\View\Components\Platform\CheckboxComponent;
use OrchidHelpers\\View\Components\Platform\FormComponent;
use OrchidHelpers\\View\Components\Platform\InputComponent;
use OrchidHelpers\\View\Components\Platform\ModalComponent;
use OrchidHelpers\\View\Components\Platform\RadioComponent;
use OrchidHelpers\\View\Components\Platform\SelectComponent;
use OrchidHelpers\\View\Components\Platform\TableComponent;
use OrchidHelpers\\View\Components\Platform\TextareaComponent;

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
