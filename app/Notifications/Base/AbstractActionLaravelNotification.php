<?php

namespace Tmd\LaravelSite\Notifications\Base;

use Tmd\LaravelSite\Models\Comment;
use Tmd\LaravelSite\Models\Message;
use Tmd\LaravelSite\Models\Notifications\SingleNotification;
use Tmd\LaravelSite\Models\Post;
use Tmd\LaravelSite\Models\Sticker;
use Tmd\LaravelSite\Models\StickerCompletion;
use Tmd\LaravelSite\Models\Submission;
use Tmd\LaravelSite\Models\Task;
use Tmd\LaravelSite\Models\User;
use Tmd\LaravelSite\Notifications\Channels\CustomDatabaseChannel;

/**
 * Note we don't use ShouldQueue because all these notifications are generated
 * by queued listeners anyway!
 */
abstract class AbstractActionLaravelNotification extends AbstractLaravelNotification
{
    /**
     * @var User
     */
    public $fromUser;

    /**
     * @var Comment
     */
    public $comment;

    /**
     * @var StickerCompletion
     */
    public $stickerCompletion;

    /**
     * @var int
     */
    public $likeId;

    /**
     * @var Comment
     */
    public $newComment;

    /**
     * @var Post
     */
    public $post;

    /**
     * @var Sticker
     */
    public $sticker;

    /**
     * @var Submission
     */
    public $submission;

    /**
     * @var Task
     */
    public $task;

    /**
     * @var Message
     */
    public $message;

    /**
     * Return the type id of this notification.
     *
     * @return int
     */
    abstract public function getType();

    /**
     * @return string
     */
    abstract public function getSubject();

    /**
     * Get the notification's delivery channels.
     *
     * @param User $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        // Don't notify ourselves
        $from = $this->getFromUser();
        if ($from && $from->id == $notifiable->id) {
            return [];
        }

        $channels = [];
        $type = $this->getType();
        //$settings = $notifiable->getSettings();

        //if ($settings->notificationOptions & $type) {
            $channels[] = CustomDatabaseChannel::class;
        //}

        //if ($settings->emailNotificationOptions & $type) {
            $channels[] = 'mail';
        //}

        //if ($settings->pushNotificationOptions & $type) {
            //$channels[] = 'push';
        //}

        return $channels;
    }

    /**
     * Convert the Notification to a model to be stored in the database.
     *
     * @param User $notifiable
     *
     * @return SingleNotification
     */
    public function toCustomDatabaseModel(User $notifiable)
    {
        return new SingleNotification($this->toArray($notifiable));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        $fromUser = $this->getFromUser();

        $postId = null;
        if ($this->post) {
            $postId = $this->post->id;
        } elseif ($this->comment) {
            $postId = $this->comment->postId;
        }

        return [
            'type' => $this->getType(),
            'forUserId' => $notifiable->id,
            'fromUserId' => $fromUser ? $fromUser->id : null,
            'commentId' => $this->comment ? $this->comment->id : null,
            'completionId' => $this->stickerCompletion ? $this->stickerCompletion->id : null,
            'likeId' => $this->likeId ?: null,
            'newCommentId' => $this->newComment ? $this->newComment->id : null,
            'postId' => $postId,
            'stickerId' => $this->sticker ? $this->sticker->id : null,
            'submissionId' => $this->submission ? $this->submission->id : null,
            'taskId' => $this->task ? $this->task->id : null,
            'messageId' => $this->message ? $this->message->id : null,
        ];
    }

    /**
     * @return User|null
     */
    protected function getFromUser()
    {
        return $this->fromUser;
    }

    protected function getFromName($capitalize = true)
    {
        $fromUser = $this->getFromUser();

        return $fromUser ? $fromUser->username : ($capitalize ? 'Somebody' : 'somebody');
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     *
     * @return \Tmd\LaravelSite\Mail\NotificationMail
     */
    public function toMail($notifiable)
    {
        $message = parent::toMail($notifiable);

        $message->subject($this->getSubject());

        return $message;
    }
}
