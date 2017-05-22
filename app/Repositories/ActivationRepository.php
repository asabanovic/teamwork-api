<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Connection;
use App\User;
use Mail;
use App\UserActivation;

class ActivationRepository extends Model
{

    protected $activation;

    public function __construct(UserActivation $activation = null)
    {
        $this->activation = $activation ?: new UserActivation;
    }

    protected function getToken()
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

    public function createActivation($user)
    {
        $activation = $this->getActivation($user);

        if (!$activation) {
            return $this->createToken($user);
        }
        return $this->regenerateToken($user);
    }

    private function regenerateToken($user)
    {
        $token = $this->getToken();
        $this->activation->where('user_id', $user->id)->update([
            'token' => $token,
            'created_at' => new Carbon()
        ]);

        $this->sendToken($user, $token);

        return $token;
    }

    private function createToken($user)
    {
        $token = $this->getToken();
        $this->activation->insert([
            'user_id' => $user->id,
            'token' => $token,
            'created_at' => new Carbon()
        ]);

        $this->sendToken($user, $token);

        return $token;
    }

    public function getActivation($user)
    {
        return $this->activation->where('user_id', $user->id)->first();
    }


    public function getActivationByToken($token)
    {
        return $this->activation->where('token', $token)->first();
    }

    public function deleteActivation($token)
    {
        $this->activation->where('token', $token)->delete();
    }

    public function activateUser($token)
    {
        $activation = $this->getActivationByToken($token);

        if ($activation === null) {
            return null;
        }

        $user = User::find($activation->user_id);

        $user->activated = true;

        $user->save();

        $this->deleteActivation($token);

        return $user;

    }

    public function getActivationLinkByUserId($user_id)
    {      

        $record = $this->activation->where('user_id', $user_id)->first(['token']);
        return $this->getActivationLink($record->token);
    }

    public function getActivationLink($token)
    {
        return route('user.activate', ['token' => $token ]);
    }

    public function sendToken($user, $token)
    {   
        $link = $this->getActivationLink($token);

        Mail::send('email', ['link' => $link], function($message) use ($user)
        {
            $message->to($user->email)->subject('Please activate your account!');
        });

        return $link;
    }

}