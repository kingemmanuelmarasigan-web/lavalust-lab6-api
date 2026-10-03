<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Controller: ApiController
 * 
 * Automatically generated via CLI.
 */
class ApiController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->call->library('api');
        $this->call->database();
    }

    public function login()
    {
        $this->api->require_method('POST');

        $input = $this->api->body();

        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        $user = $this->db
            ->table('users')
            ->where('username', $username)
            ->get();

        if (!$user || !password_verify($password, $user['password'])) {
            $this->api->respond([
                'message' => 'Invalid username or password'
            ], 401);

            return;
        }

        $tokens = $this->api->issue_tokens([
            'id'      => $user['id'],
            'username' => $user['username'],
            'role'     => $user['role']
        ]);

        $this->api->respond($tokens);
    }

    public function logout()
    {
        $this->api->require_method('POST');

        $input = $this->api->body();

        $refreshToken = $input['refresh_token'] ?? '';

        if ($refreshToken !== '') {
            $this->api->revoke_refresh_token($refreshToken);
        }

        $this->api->respond([
            'message' => 'Logged out'
        ]);
    }

    public function products()
    {
        $this->api->require_method('GET');

        $this->api->require_jwt();

        $products = $this->db
            ->table('products')
            ->get_all();

        $this->api->respond($products);
    }

    public function add_product()
    {
        $this->api->require_method('POST');

        $this->api->require_jwt();

        $input = $this->api->body();

        if (
            empty($input['product_name']) ||
            !isset($input['price']) ||
            !isset($input['quantity'])
        ) {
            $this->api->respond([
                'message' => 'Product name, price and quantity are required'
            ], 400);

            return;
        }

        $id = $this->db
            ->table('products')
            ->insert([
                'product_name' => $input['product_name'],
                'description'  => $input['description'] ?? '',
                'price'        => $input['price'],
                'quantity'     => $input['quantity'],
                'created_at'   => date('Y-m-d H:i:s')
            ]);

        $this->api->respond([
            'message' => 'Product added successfully',
            'id'      => $id
        ], 201);
    }

    public function update_product($id)
    {
        $this->api->require_method('PUT');

        $this->api->require_jwt();

        $input = $this->api->body();

        $updated = $this->db
            ->table('products')
            ->where('id', $id)
            ->update([
                'product_name' => $input['product_name'],
                'description'  => $input['description'] ?? '',
                'price'        => $input['price'],
                'quantity'     => $input['quantity']
            ]);

        $this->api->respond([
            'message' => 'Product updated successfully'
        ]);
    }

    public function delete_product($id)
    {
        $this->api->require_method('DELETE');

        $this->api->require_jwt();

        $this->db
            ->table('products')
            ->where('id', $id)
            ->delete();

        $this->api->respond([
            'message' => 'Product deleted successfully'
        ]);
    }
}