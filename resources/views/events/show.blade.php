@if($event->template == 1)
    <x-templates.papilia :event="$event" :imageUrl="$imageUrl" />
@elseif($event->template == 2)
    <x-templates.dos :event="$event" :imageUrl="$imageUrl" />
@else
    <div style="text-align: center; padding: 50px; font-family: sans-serif;">
        <h2>Esta plantilla está en construcción</h2>
    </div>
@endif