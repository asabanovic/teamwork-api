<?php
namespace App\Repositories;


use Carbon\Carbon;
use Illuminate\Database\Connection;
use App\User;
use Mail;

class ActivationRepository
{

    protected $db;

    protected $table = 'user_activations';

    public function __construct(Connection $db)
    {
        $this->db = $db;
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
        $this->db->table($this->table)->where('user_id', $user->id)->update([
            'token' => $token,
            'created_at' => new Carbon()
        ]);

        $this->sendToken($user, $token);

        return $token;
    }

    private function createToken($user)
    {
        $token = $this->getToken();
        $this->db->table($this->table)->insert([
            'user_id' => $user->id,
            'token' => $token,
            'created_at' => new Carbon()
        ]);

        $this->sendToken($user, $token);

        return $token;
    }

    public function getActivation($user)
    {
        return $this->db->table($this->table)->where('user_id', $user->id)->first();
    }


    public function getActivationByToken($token)
    {
        return $this->db->table($this->table)->where('token', $token)->first();
    }

    public function deleteActivation($token)
    {
        $this->db->table($this->table)->where('token', $token)->delete();
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

    public function sendToken($user, $token)
    {   
        $link = route('user.activate', ['token' => $token ]);
        
        Mail::send('email', ['link' => $link], function($message) use ($user)
        {
            $message->to($user->email)->subject('Please activate your account!');
        });
    }

}