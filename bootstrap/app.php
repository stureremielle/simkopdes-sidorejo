<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(
            guests: '/login',
            users: '/admin/dashboard'
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->is('admin/*') || $request->is('admin')) {
                return redirect()->back()
                    ->withInput($request->except(['_token', 'password', 'password_confirmation', 'file_upload']))
                    ->withErrors(['session' => 'Sesi Anda telah kedaluwarsa atau ukuran berkas melampaui batas unggah server/CSRF (40 MB). Silakan coba lagi.']);
            }
        });

        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, \Illuminate\Http\Request $request) {
            if ($request->is('admin/*') || $request->is('admin')) {
                return redirect()->back()
                    ->withInput($request->except(['_token', 'password', 'password_confirmation', 'file_upload']))
                    ->withErrors(['file_upload' => 'Ukuran data/berkas terlalu besar! Batas maksimal unggahan server adalah 40 MB.']);
            }
        });
    })->create();
