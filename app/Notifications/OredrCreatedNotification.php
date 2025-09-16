<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OredrCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $order;
    public function __construct($order)
    {
        $this->order=$order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];

        // $channels=[];
        // if($notifiable->notification_preference['order_created']['sms'])
        // {
        //     $channels[]='vonage';
        // }
        // if($notifiable->notification_preference['order_created']['mail'])
        // {
        //     $channels[]='mail';
        // }
        // if($notifiable->notification_preference['order_created']['broadcast'])
        // {
        //     $channels[]='broadcast';
        // }
        // return $channels;
    }

    /**
     * Get the mail representation of the notification.
    //  */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //         ->line('The introduction to the notification.')
    //         ->action('Notification Action', url('/'))
    //         ->line('Thank you for using our application!');
    // }

    public function toDatabase($notifiable){
        return[
            'order_id'=>$this->order->id,
            'user_name'=>$this->order->user->name,
            //who create order
            "message"=>'you have a new order from'.$this->order->user->name,
        ];
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
