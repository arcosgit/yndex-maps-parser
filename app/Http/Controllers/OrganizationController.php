<?php

namespace App\Http\Controllers;

use App\Http\Resources\Organization\OrganizationReviewResource;
use App\Models\OrganizationReview;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function reviews(int $org_id)
    {
        $reviews = OrganizationReview::where('org_id', $org_id)->latest()->paginate(10);
        $responseData = OrganizationReviewResource::collection($reviews)->response()->getData(true);
        return response()->json($responseData);
    }
}
