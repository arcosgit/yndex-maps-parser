<?php

namespace App\Actions\Organization;

use App\Http\Resources\Organization\OrganizationResource;
use App\Models\Organization;
use App\Models\OrganizationReview;
use App\Services\UrlSerivece;
use App\Services\YandexMapsParseService;
use DB;
use Exception;

class GetOrCreateOrganizationAction
{
    public function execute(string $url): array
    {
        $cleanUrl = UrlSerivece::cleanUrl($url);
        $organisation = Organization::where('url', $cleanUrl)->first();

        if($organisation && $organisation->created_at->diffInHours(now()) > 24){
            $organisation->delete();
            $organisation = null;
        }

        if($organisation){
            return OrganizationResource::make($organisation)->resolve();
        }

        $orgParseData = YandexMapsParseService::execute($cleanUrl);

        try{
            DB::beginTransaction();

            $org = Organization::create([
                'url' => $cleanUrl,
                'name' => $orgParseData->title,
                'rating' => $orgParseData->rating,
                'count_rating' => $orgParseData->total_ratings,
                'count_reviews' => $orgParseData->total_reviews,
            ]);

            $row_reviews = [];
            if($orgParseData->reviews && \count($orgParseData->reviews) > 0){
                foreach ($orgParseData->reviews as $review) {
                    $row_reviews[] = [
                        'org_id' => $org->id,
                        'name' => $review['name'],
                        'rating' => $review['rating'],
                        'date' => $review['date'],
                        'review' => $review['review'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                OrganizationReview::insert($row_reviews);
            }
            DB::commit();
            return OrganizationResource::make($org)->resolve();
        } catch(Exception $e){
            DB::rollBack();
        }
        return $orgParseData->toArray();
    }
}
