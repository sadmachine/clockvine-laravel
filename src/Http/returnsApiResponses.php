<?php

namespace Imarc\clockvine\Http;

use Illuminate\Http\Request;

/**
 * This trait provides basic methods for a resource controller to wrap a model.
 */
trait returnsApiResponses
{
    public function index()
    {
        return new ApiResponse(($this->model)::all());
    }

    public function store(Request $request)
    {
        if (method_exists($this, 'validateApiRequest')) {
            app()->call([$this, 'validateApiRequest']);
        }

        return new ApiResponse(($this->model)::create($request->all()));
    }

    public function show($model)
    {
        return new ApiResponse($model);
    }

    public function update(Request $request, $model)
    {
        if (method_exists($this, 'validateApiRequest')) {
            app()->call([$this, 'validateApiRequest']);
        }

        $model->fill($request->all());
        $model->save();

        return new ApiResponse($model);
    }

    public function destroy($model)
    {
        $model->delete();

        return new ApiResponse($model);
    }
}
