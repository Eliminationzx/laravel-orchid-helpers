<?php

declare(strict_types=1);

namespace Orchid\Helpers\View\Components\Platform;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;
use Orchid\Screen\Repository;

class BadgeComponent extends Component
{
    private readonly string $badgeText;
    private readonly string $badgeColor;

    public function __construct(
        readonly public Repository|Model $target,
        readonly public string           $name,
        readonly public array            $colorMap = [],
        readonly public string           $defaultColor = 'secondary',
    ) {
        $value = data_get($this->target, $this->name);
        $this->badgeText = (string) $value;
        $this->badgeColor = $this->determineColor($this->badgeText);
    }

    private function determineColor(string $value): string
    {
        // Check exact match
        if (isset($this->colorMap[$value])) {
            return $this->colorMap[$value];
        }

        // Check case-insensitive match
        $lowerValue = strtolower($value);
        foreach ($this->colorMap as $key => $color) {
            if (strtolower($key) === $lowerValue) {
                return $color;
            }
        }

        // Check for common status values
        $commonColors = [
            'active' => 'success',
            'inactive' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'success' => 'success',
            'failed' => 'danger',
            'warning' => 'warning',
            'info' => 'info',
            'primary' => 'primary',
            'secondary' => 'secondary',
            'true' => 'success',
            'false' => 'secondary',
            '1' => 'success',
            '0' => 'secondary',
            'yes' => 'success',
            'no' => 'secondary',
        ];

        $lowerValue = strtolower($value);
        return $commonColors[$lowerValue] ?? $this->defaultColor;
    }

    public function badgeClass(): string
    {
        $classes = [
            'primary' => 'badge-primary',
            'secondary' => 'badge-secondary',
            'success' => 'badge-success',
            'danger' => 'badge-danger',
            'warning' => 'badge-warning',
            'info' => 'badge-info',
            'light' => 'badge-light',
            'dark' => 'badge-dark',
        ];

        return $classes[$this->badgeColor] ?? $classes['secondary'];
    }

    public function text(): string
    {
        return $this->badgeText;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('orchid-helpers::components.platform.badge-component');
    }
}
