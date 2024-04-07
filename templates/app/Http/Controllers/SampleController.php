<?php

namespace App\Http\Controllers;

use App\Http\Requests\SampleStoreRequest;
use App\Http\Requests\SampleUpdateRequest;
use App\Models\Sample;
use Illuminate\Http\Request;

class SampleController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $samples = Sample::query()
            ->when($request->input('%%FIRSTCOLUMN%%'), function ($query, $value) {
                $query->where('%%FIRSTCOLUMN%%', 'LIKE', "%{$value}%");
            })
            ->get();

        return view('samplesChainCase.index', compact('samples'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('samplesChainCase.create');
    }

    /**
     * @param \App\Http\Requests\SampleStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SampleStoreRequest $request)
    {
        $sample = Sample::create($request->validated());

        return redirect()->route('samplesChainCase.index');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sample $sample
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Sample $sample)
    {
        return view('samplesChainCase.show', compact('sample'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sample $sample
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Sample $sample)
    {
        return view('samplesChainCase.edit', compact('sample'));
    }

    /**
     * @param \App\Http\Requests\SampleUpdateRequest $request
     * @param \App\Models\Sample $sample
     * @return \Illuminate\Http\Response
     */
    public function update(SampleUpdateRequest $request, Sample $sample)
    {
        $sample->update($request->validated());

        return redirect()->route('samplesChainCase.index');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sample $sample
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Sample $sample)
    {
        $sample->delete();

        return redirect()->route('samplesChainCase.index');
    }
}
