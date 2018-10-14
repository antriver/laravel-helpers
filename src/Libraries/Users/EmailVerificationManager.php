<?php

namespace Tmd\LaravelHelpers\Libraries\Users;

use Tmd\LaravelHelpers\Libraries\Traits\GeneratesTokensTrait;
use Tmd\LaravelHelpers\Mail\EmailVerificationMail;
use Tmd\LaravelHelpers\Models\EmailVerification;
use Tmd\LaravelHelpers\Models\User;
use Tmd\LaravelHelpers\Models\UserEmailChange;
use Tmd\LaravelHelpers\Repositories\UserRepository;
use Carbon\Carbon;
use Mail;
use Tmd\LaravelRepositories\Base\AbstractRepository;
use Tmd\LaravelRepositories\Interfaces\RepositoryInterface;

/**
 * Handles sending verification emails to new users, or when an existing user changes their email address.
 * New users are created with the email set on the User, and emailVerified = 0.
 * Email changes are not set on the User until verified.
 *
 * @method EmailVerification find($key)
 * @method EmailVerification findOrFail($key)
 *
 * @package Tmd\LaravelHelpers\Libraries\Users
 */
class EmailVerificationManager extends AbstractRepository implements RepositoryInterface
{
    use GeneratesTokensTrait;

    /**
     * @param User $user
     *
     * @return \Illuminate\Database\Eloquent\Collection|EmailVerification[]
     */
    public function findPendingVerifications(User $user)
    {
        return EmailVerification::where('userId', $user->id)->orderBy('id')->get();
    }

    /**
     * @param User $user
     *
     * @return EmailVerification
     */
    public function findLatestPendingVerification(User $user)
    {
        return EmailVerification::where('userId', $user->id)->orderBy('id', 'DESC')->first();
    }

    /**
     * @param User $user
     *
     * @return EmailVerification
     */
    public function sendNewUserVerification(User $user)
    {
        $token = $this->createNewToken();

        /** @var EmailVerification $emailVerification */
        $emailVerification = EmailVerification::create(
            [
                'userId' => $user->id,
                'email' => $user->email,
                'token' => $token,
            ]
        );

        $this->sendEmail($emailVerification, $user);

        return $emailVerification;
    }

    /**
     * @param User $user
     * @param      $email
     *
     * @return EmailVerification
     */
    public function sendEmailChangeVerification(User $user, $email)
    {
        $token = $this->createNewToken();

        /** @var EmailVerification $emailVerification */
        $emailVerification = EmailVerification::create(
            [
                'userId' => $user->id,
                'email' => $email,
                'token' => $token,
                'isChange' => 1,
            ]
        );

        $this->sendEmail($emailVerification, $user, true);

        return $emailVerification;
    }

    /**
     * @param EmailVerification $emailVerification
     * @param User              $user
     * @param bool              $queued
     */
    public function sendEmail(EmailVerification $emailVerification, User $user, $queued = false)
    {
        if ($queued) {
            Mail::to($emailVerification->email)->queue(
                $this->createMessage($emailVerification, $user)
            );
        } else {
            Mail::to($emailVerification->email)->send(
                $this->createMessage($emailVerification, $user)
            );
        }
    }

    /**
     * @param EmailVerification $emailVerification
     * @param User              $user
     * @param bool              $queued
     */
    public function resendEmail(EmailVerification $emailVerification, User $user, $queued = false)
    {
        $this->sendEmail($emailVerification, $user, $queued);

        $emailVerification->resentAt = (new Carbon())->toDateTimeString();
        $this->persist($emailVerification);
    }

    /**
     * @param EmailVerification $emailVerification
     * @param UserRepository    $userRepository
     */
    public function verify(EmailVerification $emailVerification, UserRepository $userRepository)
    {
        $user = $userRepository->findOrFail($emailVerification->userId);

        if ($emailVerification->isChange) {
            // Log the change
            UserEmailChange::create(
                [
                    'userId' => $user->id,
                    'oldEmail' => $user->email,
                    'newEmail' => $emailVerification->email,
                ]
            );
        }

        // Update the user's email address and set as verified
        $user->email = $emailVerification->email;
        $user->emailVerified = 1;

        $userRepository->persist($user);

        $emailVerification->delete();
    }

    /**
     * Return the fully qualified class name of the Models this repository returns.
     *
     * @return string
     */
    public function getModelClass()
    {
        return EmailVerification::class;
    }

    /**
     * @param EmailVerification $emailVerification
     * @param User              $user
     *
     * @return EmailVerificationMail
     */
    protected function createMessage(EmailVerification $emailVerification, User $user)
    {
        return new EmailVerificationMail($emailVerification, $user);
    }
}
