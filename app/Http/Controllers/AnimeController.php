<?php

namespace Liamtseva\Cinema\Http\Controllers;

use Liamtseva\Cinema\Http\Requests\StoreMovieRequest;
use Liamtseva\Cinema\Http\Requests\UpdateMovieRequest;
use Liamtseva\Cinema\Models\Anime;

class AnimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovieRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Anime $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Anime $movie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMovieRequest $request, Anime $movie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Anime $movie)
    {
        //
    }
}
