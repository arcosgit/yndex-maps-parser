<?php

namespace App\Http\Controllers;

use App\Actions\Organization\GetOrCreateOrganizationAction;
use App\Http\Requests\Parse\ParseUrlRequest;
class ParseController extends Controller
{
    public function parse(ParseUrlRequest $request, GetOrCreateOrganizationAction $action)
    {
        $url = $request->validated()['url'];
        $result = $action->execute($url);
        return response()->json($result);
    }
}
