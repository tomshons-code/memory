<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Models\Client;
use Illuminate\Auth\Access\AuthorizationException;

class EmailVerificationController extends Controller
{
    public function sendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(["message" => 'Already Verified'], 403);
        }

        $request->user()->sendEmailVerificationNotification();

        return ['status' => 'verification-link-sent'];
    }

    public function verify(Request $request)
    {
        $user = Client::findOrFail((int)$request->id);

        if (! hash_equals((string) $request->id, (string) $user->getKey())) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(["message" => 'Already Verified'], 403);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return [
            'message'=>'Email has been verified'
        ];
    }
}
