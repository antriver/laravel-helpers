<?php

namespace Tmd\LaravelSite\Models\Notifications;

/**
 * Stickable\Models\Notifications\GroupedNotification
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
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification whereEarningId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification whereForUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification whereFromUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification whereLikeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification whereNewCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification whereSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification whereStickerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification whereSubmissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Notifications\GroupedNotification whereType($value)
 * @mixin \Eloquent
 */
class GroupedNotification extends AbstractNotification
{
}
