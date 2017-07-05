<?php

namespace Amirite\Libraries\Notifications;

use Amirite\Models\Comment;
use Amirite\Models\Notifications\AbstractNotification;
use Amirite\Models\Post;
use Amirite\Models\User;
use Amirite\Repositories\NotificationRepository;

class NotificationCollectionFactory
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * TODO: Cache
     *
     * Return an array with 2 items. The first is the count of individual GroupedNotifications.
     * The second is an array of NotificationConnections, with the notifications organised.
     *
     * @param User $user
     *
     * @return array
     */
    public function getCollectionsForUser(User $user)
    {
        $notifications = $this->notificationRepository->getUsersNotifications($user);
        $collections = $this->createNotificationCollections($notifications);

        $this->sortNotificationCollections($collections);

        return [count($notifications), $collections];
    }

    /**
     * @param array $collections
     */
    private function sortNotificationCollections(array &$collections)
    {
        usort(
            $collections,
            function (NotificationCollection $a, NotificationCollection $b) {
                return $a->lastAt < $b->lastAt;
            }
        );
    }

    /**
     * @param AbstractNotification[] $notifications
     *
     * @return NotificationCollection[]
     */
    private function createNotificationCollections($notifications)
    {
        /** @var NotificationCollection[] $groups */
        $groups = [];

        foreach ($notifications as $notification) {
            $collectionKey = $this->getCollectionKeyForNotification($notification);
            switch ($collectionKey['type']) {
                case 'post':
                    if (!isset($groups[$collectionKey['key']])) {
                        $groups[$collectionKey['key']] =
                            $this->createPostNotificationGroup($notification->getPost(true))
                                ->setKey($collectionKey['key']);
                    }
                    break;
                case 'comment':
                    if (!isset($groups[$collectionKey['key']])) {
                        $groups[$collectionKey['key']] =
                            $this->createCommentNotificationGroup($notification->getComment(true))
                                ->setKey($collectionKey['key']);
                    }
                    break;
                default:
                    if (!isset($groups[$collectionKey['key']])) {
                        $groups[$collectionKey['key']] =
                            (new NotificationCollection($notification->type))
                                ->setKey($collectionKey['key']);
                    }
                    break;
            }

            $groups[$collectionKey['key']]->addNotification($notification);
        }

        return $groups;
    }

    /**
     * @param Post $post
     *
     * @return NotificationCollection
     */
    private function createPostNotificationGroup(Post $post)
    {
        return (new NotificationCollection())
            ->setHeading($post->getSnippet())
            ->setIcon('file-text')
            ->setUrl($post->getUrl());
    }

    /**
     * @param Comment $comment
     *
     * @return NotificationCollection
     */
    private function createCommentNotificationGroup(Comment $comment)
    {
        return (new NotificationCollection())
            ->setHeading($comment->getSnippet())
            ->setIcon('comment')
            ->setUrl($comment->getUrl());
    }

    /**
     * @param AbstractNotification $notification
     *
     * @return string[]
     */
    public function getCollectionKeyForNotification(AbstractNotification $notification)
    {
        $postActions = [
            NotificationType::POST_LOVED,
            NotificationType::POST_DELETED,
            NotificationType::POST_APPROVED,
            NotificationType::COMMENT_ON_CREATED_POST,
            NotificationType::COMMENT_ON_SUBSCRIBED_POST,
        ];

        $commentActions = [
            NotificationType::COMMENT_LOVED,
            NotificationType::COMMENT_REPLY,
        ];

        if (in_array($notification->type, $postActions)) {
            $type = 'post';
            $key = 'post-'.$notification->postId;
        } elseif (in_array($notification->type, $commentActions)) {
            $type = 'comment';
            $key = 'comment-'.$notification->commentId;
        } else {
            $type = '';
            $key = 'type-'.$notification->type;
        }

        return [
            'type' => $type,
            'key' => $key,
        ];
    }
}
