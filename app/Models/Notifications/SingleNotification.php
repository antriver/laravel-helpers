<?php

namespace Tmd\LaravelSite\Models\Notifications;

/**
 * Stickable\Models\Notifications\SingleNotification
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
 * @property-read \Stickable\Models\Comment|null $comment
 * @property-read \Stickable\Models\Message|null $message
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\AbstractNotification unseen()
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification whereEarningId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification whereForUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification whereFromUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification whereLikeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification whereNewCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification whereSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification whereStickerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification whereSubmissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\SingleNotification whereType($value)
 * @mixin \Eloquent
 */
class SingleNotification extends AbstractNotification
{
}
