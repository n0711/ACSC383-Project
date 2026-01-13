<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Helpers;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Core\Auth;
use App\Services\AuthService;

final class AuthController
{
    public function register(Request $req): Response
    {
        $errors = [];
        $values = [
            'display_name' => $req->input('display_name'),
            'email' => $req->input('email'),
        ];
        $verifyLink = null;

        if ($req->isPost()) {
            $display = $req->input('display_name');
            $email = $req->input('email');
            $pass = $req->input('password');
            $pass2 = $req->input('password2');

            $v = new Validator();
            $v->required('display_name', $display, 'Display name is required.')
              ->minLen('display_name', $display, 2, 'Display name must be at least 2 characters.')
              ->required('email', $email, 'Email is required.')
              ->email('email', $email, 'Enter a valid email.')
              ->required('password', $pass, 'Password is required.')
              ->minLen('password', $pass, 6, 'Password must be at least 6 characters.');

            if ($pass !== $pass2) $errors['password2'] = 'Passwords do not match.';

            $errors = array_merge($errors, $v->errors());

            if ($email !== '' && !str_contains($email, '@')) {
                $errors['email'] = 'Enter a valid university email.';
            }

            if (empty($errors)) {
                $svc = new AuthService();

                if ($svc->findUserByEmail($email)) {
                    $errors['email'] = 'That email is already registered.';
                } else {
                    $result = $svc->register($display, $email, $pass);
                    $token = $result['verification_token'];

                    $verifyLink = Helpers::route('verify', ['token' => $token]);

                    Helpers::flash('success', 'Account created. Use the demo verification link below.');
                }
            } else {
                Helpers::flash('warning', 'Please fix the highlighted fields.');
            }

            $values['display_name'] = $display;
            $values['email'] = $email;
        }

        return Response::html(\App\Core\View::render('auth/register', [
            'title' => 'Create account',
            'errors' => $errors,
            'values' => $values,
            'verifyLink' => $verifyLink,
        ]));
    }

    public function verify(Request $req): Response
    {
        $token = $req->input('token');
        $ok = null;

        if ($token !== '') {
            $svc = new AuthService();
            $ok = $svc->verifyToken($token);

            if ($ok) {
                Helpers::flash('success', 'Verified! You can now log in.');
            } else {
                Helpers::flash('danger', 'Invalid or expired verification token (demo).');
            }
        }

        return Response::html(\App\Core\View::render('auth/verify', [
            'title' => 'Verify (Demo)',
            'token' => $token,
            'ok' => $ok,
        ]));
    }

    public function login(Request $req): Response
    {
        $errors = [];
        $values = ['email' => $req->input('email')];

        if ($req->isPost()) {
            $email = $req->input('email');
            $pass = $req->input('password');

            $v = new Validator();
            $v->required('email', $email, 'Email is required.')
              ->email('email', $email, 'Enter a valid email.')
              ->required('password', $pass, 'Password is required.');

            $errors = $v->errors();

            if (empty($errors)) {
                $svc = new AuthService();
                $res = $svc->login($email, $pass);

                if ($res['ok'] ?? false) {
                    Auth::login($res['user']);
                    Helpers::flash('success', 'Welcome back!');
                    return Response::redirect(Helpers::route('landing'));
                }

                $errors['general'] = (string)($res['error'] ?? 'Login failed.');
                Helpers::flash('danger', $errors['general']);
            } else {
                Helpers::flash('warning', 'Please fix the highlighted fields.');
            }

            $values['email'] = $email;
        }

        return Response::html(\App\Core\View::render('auth/login', [
            'title' => 'Sign in',
            'errors' => $errors,
            'values' => $values,
        ]));
    }

    public function logout(Request $req): Response
    {
        Auth::logout();
        Helpers::flash('info', 'Signed out.');
        return Response::redirect(Helpers::route('landing'));
    }
}
