<?php

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;

Route::get('__open-in-editor', fn (Request $request) => redirect(
    file_get_contents(Vite::hotFile()).'/__open-in-editor?'.Arr::query($request->input())
))->name('vite-open-in-editor');
