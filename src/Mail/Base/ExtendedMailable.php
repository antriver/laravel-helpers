<?php

namespace Tmd\LaravelHelpers\Mail\Base;

use Illuminate\Mail\Mailable;
use Tmd\LaravelHelpers\Models\Achievement;
use Tmd\LaravelHelpers\Models\User;

/**
 * Extends the base Mailable class with some useful functions for adding content.
 * A lot of this is replicated from Illuminate\Notifications\Messages\SimpleMessage
 * because the interface is nice, but we want to use it for both Notifications and regular Mailables.
 */
abstract class ExtendedMailable extends Mailable
{
    /**
     * The "level" of the notification (info, success, error).
     *
     * @var string
     */
    public $level = 'info';

    /**
     * The "intro" lines of the notification.
     *
     * @var array
     */
    public $introLines = [];

    /**
     * The "outro" lines of the notification.
     *
     * @var array
     */
    public $outroLines = [];

    /**
     * The text / label for the action.
     *
     * @var string
     */
    public $actionText;

    /**
     * The action URL.
     *
     * @var string
     */
    public $actionUrl;

    /**
     * @var array
     */
    public $boxes = [];

    /**
     * @var User
     */
    public $recipient;

    /**
     * Indicate that the notification gives information about a successful operation.
     *
     * @return $this
     */
    public function success()
    {
        $this->level = 'success';

        return $this;
    }

    /**
     * Indicate that the notification gives information about an error.
     *
     * @return $this
     */
    public function error()
    {
        $this->level = 'error';

        return $this;
    }

    /**
     * Set the "level" of the notification (success, error, etc.).
     *
     * @param  string $level
     *
     * @return $this
     */
    public function level($level)
    {
        $this->level = $level;

        return $this;
    }

    public function box($content, $href = null, $title = null)
    {
        $this->boxes[] = [
            'content' => $content,
            'href' => $href,
            'title' => $title,
        ];

        return $this;
    }

    public function userBox(User $user, $title = null)
    {
        $this->boxes[] = [
            'preformattedContent' =>
                '<img src="'.$user->getAvatarUrl().'" style="width:60px; height:60px; border:0; border-radius:50%;" />
                <br/><strong>'.e($user->username).'</strong>',
            'href' => $user->getUrl(),
            'title' => $title,
            'style' => 'user-box',
        ];

        return $this;
    }

    public function achievementBox(Achievement $achievement, $title = null)
    {
        $this->boxes[] = [
            'preformattedContent' =>
                '<img src="'.$achievement->getImageUrl().'" style="width:60px; border:0; border-radius:50%;" />
                <br/><strong>'.$achievement->name.'</strong>
                <br/>'.$achievement->description,
            'href' => $achievement->getUrl(),
            'title' => $title,
            'style' => 'achievement-box',
        ];

        return $this;
    }

    /**
     * Add a line of text to the notification.
     *
     * @param  \Illuminate\Notifications\Action|string $line
     *
     * @return $this
     */
    public function line($line)
    {
        if (!$this->actionText) {
            $this->introLines[] = $this->formatLine($line);
        } else {
            $this->outroLines[] = $this->formatLine($line);
        }

        return $this;
    }

    /**
     * Format the given line of text.
     *
     * @param  string|array $line
     *
     * @return string
     */
    protected function formatLine($line)
    {
        if (is_array($line)) {
            return implode(' ', array_map('trim', $line));
        }

        return trim(implode(' ', array_map('trim', preg_split('/\\r\\n|\\r|\\n/', $line))));
    }

    /**
     * Configure the "call to action" button.
     *
     * @param  string $text
     * @param  string $url
     *
     * @return $this
     */
    public function action($text, $url)
    {
        $this->actionText = $text;
        $this->actionUrl = $url;

        return $this;
    }

    /**
     * @return User
     */
    public function getRecipient(): User
    {
        return $this->recipient;
    }

    /**
     * IMPORTANT: This does not set the email address being sent to. It only sets the recipient property,
     * which is used in the template to display the user's name.
     *
     * @param User $recipient
     */
    public function setRecipient(User $recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * Build the view data for the message.
     *
     * @return array
     */
    public function buildViewData()
    {
        $data = parent::buildViewData();

        $data['recipient'] = $this->getRecipient();

        return $data;
    }
}
