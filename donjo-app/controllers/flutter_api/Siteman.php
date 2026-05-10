<?php

use App\Models\User;

defined('BASEPATH') || exit('No direct script access allowed');

class Siteman extends MY_Controller
{
    public function auth()
    {
        $username = trim($this->input->post('username'));
        $password = trim($this->input->post('password'));

        $user = User::where('username', $username)->status()->first();

        $authLolos =  password_verify($password, $user->password);

        if ($user === false || $authLolos === false) {
          return json([
            'status' => 401,
            'message' => 'Login Failed',
            'data' => null
          ]);
        }

        $token = base64_encode($user);

        return json([
          'status' => 200,
          'message' => 'Login Success',
          'data' => $user,
          'token' => $token,
        ]);
    }
}
