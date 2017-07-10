<?php

namespace Tmd\LaravelSite\Models\Notifications;

/**
 * Tmd\LaravelSite\Models\Notifications\SingleNotification
 *
 * @property int $id
 * @property int $type
 * @property int $forUserId
 * @property int|null $fromUserId
 * @property int|null $commentId
 * @property int|null $earningId
 * @property int|null $likeId
 * @property int|null $newCommentId
 * @property int|null $postId
 * @property int|null $stickerId
 * @property int|null $submissionId
 * @property int|null $taskId
 * @property int|null $messageId
 * @property string|null $text
 * @property \Carbon\Carbon $createdAt
 * @property string|null $seenAt
 * @property-read \Tmd\LaravelSite\Models\Comment|null $comment
 * @property-read \Tmd\LaravelSite\Models\Message|null $message
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\AbstractNotification unseen()
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification whereEarningId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification whereForUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification whereFromUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification whereLikeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification whereNewCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification whereSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification whereStickerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification whereSubmissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\Notifications\SingleNotification whereType($value)
 * @mixin \Eloquent
 */
class SingleNotification extends AbstractNotification
{
}
