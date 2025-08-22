<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public function render()
    {
        return view('components.sidebar', [
            'stars' => $this->stars(),
        ]);
    }

    protected function stars()
    {
        return Cache::remember(
            key: 'inxilpro:stars',
            ttl: now()->addHour(),
            callback: fn () => Http::get('https://api.github.com/users/inxilpro/starred')->json()
        );
    }
}
