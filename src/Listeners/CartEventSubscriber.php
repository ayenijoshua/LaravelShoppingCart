<?php

namespace App\Listeners;

use App\Events\CartDestroyed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class CartEventSubscriber
{
    /**
     * handle cart item added event.
     *
     */
    public function onCartItemAdded($event)
    {
        Log::info($event->item);
    }
    /**
     * handle cart item updated event
     */
    public function onCartItemUpdated($event){

    }
    /**
     * handle cart item remove event
     */
    public function onCartItemRemoved($event){
        
    }
    /**
     * handle cart item gotten event
     */
    public function onCartItemGotten($event){
        
    }
    /**
     * handle cart items gotten event
     */
    public function onCartItemsGotten($event){
        
    }
    /**
     * handle cart emptyed event
     */
    public function onCartEmptyed($event){
        
    }
    /**
     * handle cart set event
     */
    public function onCartSet($event){
        
    }
    /**
     * handle cart destruction event
     */
    public function onCartDestroyed($event){
        
    }
    /**
     * handle cart restoration event
     */
    public function onCartRestored(){

    }
    /**
     * handle cart options gotten event
     */
    public function onCartOptionsGotten(){

    }


    /**
     * Handle the event.
     *
     * @param  CartDestroyed  $event
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\CartItemAdded',
            'App\Listeners\CartEventSubscriber@onCartItemAdded'
        );
        $events->listen(
            'App\Events\CartItemUpdated',
            'App\Listeners\CartEventSubscriber@onCartItemUpdated'
        );
        $events->listen(
            'App\Events\CartItemRemoved',
            'App\Listeners\CartEventSubscriber@onCartItemRemoved'
        );
        $events->listen(
            'App\Events\CartItemGotten',
            'App\Listeners\CartEventSubscriber@onCartItemGotten'
        );
        $events->listen(
            'App\Events\CartItemsGotten',
            'App\Listeners\CartEventSubscriber@onCartItemsGotten'
        );
        $events->listen(
            'App\Events\CartEmptyed',
            'App\Listeners\CartEventSubscriber@onCartEmptyed'
        );
        $events->listen(
            'App\Events\CartSet',
            'App\Listeners\CartEventSubscriber@onCartSet'
        );
        $events->listen(
            'App\Events\CartDestroyed',
            'App\Listeners\CartEventSubscriber@onCartDestroyed'
        );
        $events->listen(
            'App\Events\CartRestored',
            'App\Listeners\CartEventSubscriber@onCartRestored'
        );
        $events->listen(
            'App\Events\CartOptionsGotten',
            'App\Listeners\CartEventSubscriber@onCartOptionsGotten'
        );
    }
}
