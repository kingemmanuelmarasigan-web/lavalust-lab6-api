<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Controller: SetupController
 * 
 * Automatically generated via CLI.
 */
class SetupController extends Controller
{
    public function create_user()
    {
        $this->call->database();

        $username = 'admin';
        $email = 'admin@example.com';
        $password = password_hash('admin123', PASSWORD_BCRYPT);
        $role = 'admin';

        $existing = $this->db
            ->table('users')
            ->where('username', $username)
            ->get();

        if ($existing) {
            echo "User already exists.";
            return;
        }

        $this->db->table('users')->insert([
            'username'   => $username,
            'email'      => $email,
            'password'   => $password,
            'role'       => $role,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        echo "Admin user created.";
    }
}