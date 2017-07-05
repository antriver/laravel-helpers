<?php

namespace Tmd\LaravelSite\Models\Notifications;

use Tmd\LaravelSite\Libraries\LanguageHelpers;
use Tmd\LaravelSite\Libraries\Moderation\ModerationLogger;
use Tmd\LaravelSite\Libraries\Notifications\NotificationType;
use Tmd\LaravelSite\Models\Base\AbstractModel;
use Tmd\LaravelSite\Models\Comment;
use Tmd\LaravelSite\Models\Interfaces\BelongsToUserInterface;
use Tmd\LaravelSite\Models\Traits\BelongsToUserTrait;
use Tmd\LaravelSite\Models\Traits\CreatedAtWithoutUpdatedAtTrait;
use Tmd\LaravelSite\Models\User;
use Tmd\LaravelSite\Repositories\UserRepository;
use Auth;
use Lang;

/**
 * Stickable\Models\AbstractNotification
 *
 * @property integer                      $id
 * @property integer                      $type
 * @property integer                      $forUserId
 * @property integer                      $fromUserId
 * @property integer                      $postId
 * @property integer                      $commentId    Comment being replied to / loved
 * @property integer                      $newCommentId Comment that was created / replying
 * @property integer                      $messageId
 * @property integer                      $achievementId
 * @property string                       $text
 * @property \Carbon\Carbon               $createdAt
 * @property string                       $seenAt
 * @property-read \Stickable\Models\Comment $comment
 * @property-read \Stickable\Models\Message $message
 * @property-read \Stickable\Models\User    $user
 * @property-read \Stickable\Models\Post    $post
 * @method static \Illuminate\Database\Query\Builder|\Stickable\Models\Notifications\AbstractNotification whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Stickable\Models\Notifications\AbstractNotification
 *         whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\Stickable\Models\Notifications\AbstractNotification
 *         whereForUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Stickable\Models\Notifications\AbstractNotification
 *         whereFromUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Stickable\Models\Notifications\AbstractNotification
 *         wherePostId($value)
 * @method static \Illuminate\Database\Query\Builder|\Stickable\Models\Notifications\AbstractNotification
 *         whereCommentId($value)
 * @method static \Illuminate\Database\Query\Builder|\Stickable\Models\Notifications\AbstractNotification
 *         whereNewCommentId($value)
 * @method static \Illuminate\Database\Query\Builder|\Stickable\Models\Notifications\AbstractNotification
 *         whereMessageId($value)
 * @method static \Illuminate\Database\Query\Builder|\Stickable\Models\Notifications\AbstractNotification
 *         whereAchievementId($value)
 * @method static \Illuminate\Database\Query\Builder|\Stickable\Models\Notifications\AbstractNotification
 *         whereText($value)
 * @method static \Illuminate\Database\Query\Builder|\Stickable\Models\Notifications\AbstractNotification
 *         whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Stickable\Models\Notifications\AbstractNotification
 *         whereSeenAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Stickable\Models\Notifications\AbstractNotification unseen()
 * @mixin \Eloquent
 */
abstract class AbstractNotification extends AbstractModel implements BelongsToUserInterface
{
    use BelongsToUserTrait;
    use CreatedAtWithoutUpdatedAtTrait;

    protected $table = 'notifications';

    public $fromUserIds;

    public $fromUsers;

    public function __toString()
    {
        return $this->getText();
    }

    public function scopeUnseen($query)
    {
        return $query->whereNull('seenAt');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class, 'commentId', 'id');
    }

    public function getComment($withTrashed = false)
    {
        return $this->getRelationFromRepository('commentId', 'CommentRepository', $withTrashed);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function message()
    {
        return $this->belongsTo('\Stickable\Models\Message', 'messageId', 'id');
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array['action'] = NotificationType::getName($this->type);
        $array['avatarUrl'] = $this->getFromUserAvatar();
        $array['groupCount'] = $this->getGroupCount();
        $array['groupingId'] = $this->getGroupingId();
        $array['icon'] = $this->getIcon();
        $array['quote'] = $this->getQuote();
        $array['text'] = $this->getText();
        $array['url'] = $this->getUrl();
        $array['test'] = 'array';

        return $array;
    }

    public function getUserId()
    {
        return $this->forUserId;
    }

    /**
     * Returns a hash of the grouping keys for this notification.
     */
    public function getGroupingId()
    {
        $components = [
            $this->forUserId,
            $this->type,
            $this->postId,
            $this->commentId,
            $this->achievementId,
            $this->newCommentId,
            (in_array($this->type, [256, 512, 1024]) ? $this->fromUserId : null),
        ];

        return implode('-', $components);
    }

    public function getUrl()
    {
        $parameters = $this->attributes;

        if ($fromUsers = $this->getFromUsers()) {
            $parameters['fromUsername'] = $fromUsers[0]->getUrlUsername();
        }

        if ($user = Auth::user()) {
            /** @var User $user */
            $parameters['currentUsername'] = $user->getUrlUsername();
        }

        return Lang::get(
            'notifications.urls.'.$this->type,
            $parameters
        );
    }

    /**
     * Returns a FontAwesome icon name for this notification.
     *
     * @return string|null
     */
    public function getIcon()
    {
        return Lang::get('notifications.icons.'.$this->type);
    }

    /**
     * Returns the text of this notification.
     *
     * @return string
     */
    public function getText()
    {
        $usernames = $this->getUsernameText();

        $achievementName = null;
        if ($this->achievementId) {
            $achievementRepository = app('AchievementRepository');
            if ($achievement = $achievementRepository->find($this->achievementId)) {
                $achievementName = $achievement->name;
            }
        }

        $deleteReason = null;
        if ($this->type == NotificationType::POST_DELETED) {
            if ($post = $this->getPost(true)) {
                if ($deletion = (new ModerationLogger())->getPostDeletion($post)) {
                    $deleteReason = $deletion->getReasonText();
                }
            }
        }

        return trans_choice(
            'notifications.types.'.$this->type,
            $this->getGroupCount(),
            [
                'usernames' => $usernames,
                'achievementName' => $achievementName,
                'deleteReason' => $deleteReason,
            ]
        );
    }

    public function getQuote()
    {
        switch ($this->type) {
            case NotificationType::COMMENT_ON_CREATED_POST:
            case NotificationType::QUEUED_POST_PUBLISHED:
            case NotificationType::COMMENT_ON_SUBSCRIBED_POST:
            case NotificationType::POST_LOVED:
            case NotificationType::POST_APPROVED:
            case NotificationType::POST_WILL_BE_APPROVED:
            case NotificationType::POST_DELETED:
                $postSnippet = null;
                if ($this->postId && $post = $this->getPost()) {
                    $postSnippet = $post->getSnippet();
                }

                return $postSnippet;

            case NotificationType::COMMENT_REPLY:
            case NotificationType::COMMENT_LOVED:
                $commentSnippet = null;
                if ($this->commentId && $comment = $this->comment) {
                    $commentSnippet = $comment->getSnippet();
                }

                return $commentSnippet;
        }

        return null;
    }

    /**
     * Get the list of usernames for the fromUserIds to display in the notification.
     *
     * Number of users:
     * 1: nameA
     * 2: nameA and nameB
     * 3: nameA, nameB and nameC
     * 4+: nameA, nameB, and 2+ others
     *
     * @return string|null
     */
    private function getUsernameText()
    {
        if ($userIds = $this->getFromUserIds()) {
            $userIdCount = count($userIds);

            $showUsernameCount = $userIdCount > 3 ? 2 : 3;
            $usernames = $this->getFromUsernames($showUsernameCount);

            if ($userIdCount <= 3) {
                $usernameStr = LanguageHelpers::naturalLanguageImplode($usernames);
            } else {
                $usernameStr = implode(', ', $usernames);
                $remaining = $userIdCount - $showUsernameCount;
                $usernameStr .= ', '.Lang::get('notifications.and-others', ['count' => $remaining]);
            }

            return $usernameStr;
        }

        return null;
    }

    /**
     * Returns the URL of the first user's avatar that's available.
     */
    private function getFromUserAvatar()
    {
        foreach ($this->getFromUsers() as $fromUser) {
            if ($url = $fromUser->getAvatarUrl()) {
                return $url;
            }
        }

        return null;
    }

    /**
     * Returns an array of userIDs that this grouped notification is from.
     * The fromUserIds value should be populated by a GROUP_CONCAT in the query that loaded this notification.
     *
     * @return array
     */
    private function getFromUserIds()
    {
        if (!is_null($this->fromUserIds)) {
            return $this->fromUserIds;
        }

        if (isset($this->attributes['fromUserIds'])) {
            $this->fromUserIds = explode(',', $this->attributes['fromUserIds']);
        } elseif ($this->fromUserId) {
            $this->fromUserIds = [$this->fromUserId];
        } else {
            $this->fromUserIds = [];
        }

        return $this->fromUserIds;
    }

    /**
     * Returns the number of notifications that were grouped together together into this one.
     * Populated by the COUNT() on the query in NotificationRepository.
     */
    private function getGroupCount()
    {
        return isset($this->attributes['groupCount']) ? $this->attributes['groupCount'] : 1;
    }

    /**
     * Returns an array of Users the notification is from.
     *
     * @param int $limit
     *
     * @return User[]
     */
    public function getFromUsers($limit = null)
    {
        if (!is_null($this->fromUsers)) {
            return $this->fromUsers;
        }

        $userRepository = new UserRepository();

        $this->fromUsers = [];
        $fromUserIds = $this->getFromUserIds();

        $i = 0;
        while ($i < count($fromUserIds) && ($limit === null || $i < $limit)) {
            $userId = $fromUserIds[$i];
            $this->fromUsers[] = $userRepository->find($userId);
            ++$i;
        }

        return $this->fromUsers;
    }

    /**
     * Returns an array of usernames the notification is from.
     *
     * @param int $limit
     *
     * @return string[]
     */
    private function getFromUsernames($limit = null)
    {
        $fromUsers = $this->getFromUsers($limit);

        $usernames = array_map(
            function (User $user) {
                return $user->username;
            },
            $fromUsers
        );

        return $usernames;
    }
}
