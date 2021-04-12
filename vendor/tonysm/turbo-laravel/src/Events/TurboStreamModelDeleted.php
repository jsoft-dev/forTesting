<?php

namespace Tonysm\TurboLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Database\Eloquent\Model;
use Tonysm\TurboLaravel\TurboStreamModelRenderer;

class TurboStreamModelDeleted implements ShouldBroadcastNow
{
    use InteractsWithSockets;
    use TurboStreamBroadcasts;

    public Model $model;
    public string $action;

    public function __construct(Model $model, string $action = "remove")
    {
        $this->model = $model;
        $this->action = $action;
    }

    public function render(): string
    {
        return resolve(TurboStreamModelRenderer::class)
            ->renderDeleted($this->model, 'remove')
            ->render();
    }
}
