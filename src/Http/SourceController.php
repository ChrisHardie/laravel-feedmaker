<?php

namespace ChrisHardie\Feedmaker\Http;

use ChrisHardie\Feedmaker\Models\Source;

class SourceController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function __invoke()
    {
        return view('feedsindex', [
            'sources' => Source::where('active', true)->whereNotNull('last_succeed_at')->orderByDesc('last_succeed_at')->get()
        ]);
    }
}
