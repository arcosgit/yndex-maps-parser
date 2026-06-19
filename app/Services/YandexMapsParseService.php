<?php

namespace App\Services;

use Http;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\DomCrawler\Crawler;

class YandexMapsParseService
{
    private function __construct(
        public readonly ?string $title = null,
        public readonly ?string $rating = null,
        public readonly ?int $total_ratings = null,
        public readonly ?int $total_reviews = null,
        public readonly ?array $reviews = null,
    ){}
    public static function execute(string $url): self
    {
        $html = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
        ])->get($url)->body();

        $crawler = new Crawler($html);

        $title = self::firstText($crawler, 'h1');
        $rating = self::rating($crawler);
        $totalRatings = self::totalRatings($crawler);
        $totalReviews = self::totalReviews($crawler);
        $urlReviews = self::withTabReviews($url);

        $reviews = null;
        if ($totalReviews && $totalReviews > 0) {
            $reviews = self::parseReviews($urlReviews);
        }

        return new self(
            title: $title,
            rating: $rating,
            total_ratings: $totalRatings,
            total_reviews: $totalReviews,
            reviews: $reviews,
        );
    }
    private static function parseReviews(string $url): array
    {
        try {
            $html = Browsershot::url($url)
                ->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36')
                ->setExtraHttpHeaders([
                    'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                ])
                ->waitForSelector('.business-reviews-card-view__review')
                ->bodyHtml();

            $crawler = new Crawler($html);

            $reviewBlocks = $crawler->filter('.business-reviews-card-view__review');

            if ($reviewBlocks->count() === 0) {
                return [];
            }
            $reviews = [];

            foreach ($reviewBlocks as $block) {
                $blockCrawler = new Crawler($block);

                $name = self::extractAuthor($blockCrawler);
                $rating = self::extractRating($blockCrawler);
                $date = self::extractDate($blockCrawler);
                $reviewText = self::extractReviewText($blockCrawler);

                $reviews[] = [
                    'name'   => $name,
                    'rating' => $rating,
                    'date'   => $date,
                    'review' => $reviewText,
                ];
            }
            return $reviews;
        } catch (\Exception $e) {
            \Log::error('Ошибка парсинга отзывов: ' . $e->getMessage());
            return [];
        }
    }

    private static function extractAuthor(Crawler $blockCrawler): ?string
    {
        $link = $blockCrawler->filter('a.business-review-view__link');
        if ($link->count() === 0) {
            return null;
        }
        $span = $link->filter('span');
        return $span->count() ? trim($span->first()->text()) : null;
    }

    private static function extractRating(Crawler $blockCrawler): ?int
    {
        $starsDiv = $blockCrawler->filter('div.business-rating-badge-view__stars._spacing_normal');
        if ($starsDiv->count() > 0) {
            $ariaLabel = $starsDiv->first()->attr('aria-label');
            if ($ariaLabel && preg_match('/Оценка\s*(\d+)\s*Из\s*5/i', $ariaLabel, $matches)) {
                return (int) $matches[1];
            }
        }
        $fullStars = $blockCrawler->filter('span.inline-image._loaded.icon.business-rating-badge-view__star._full');
        return $fullStars->count();
    }

    private static function extractDate(Crawler $blockCrawler): ?string
    {
        $dateSpan = $blockCrawler->filter('span.business-review-view__date');
        if ($dateSpan->count() === 0) {
            return null;
        }
        $innerSpan = $dateSpan->filter('span');
        return $innerSpan->count() ? trim($innerSpan->first()->text()) : null;
    }

    private static function extractReviewText(Crawler $blockCrawler): ?string
    {
        $textContainer = $blockCrawler->filter('span.spoiler-view__text-container');
        if ($textContainer->count() === 0) {
            return null;
        }
        return trim($textContainer->first()->text());
    }

    private static function withTabReviews(string $url): string
    {
        if (str_contains($url, 'tab=reviews')) {
            return $url;
        }

        if (preg_match('/([?&])z=/', $url)) {
            return preg_replace('/([?&])z=/', '$1tab=reviews&z=', $url, 1);
        }

        return $url . (str_contains($url, '?') ? '&' : '?') . 'tab=reviews';
    }

    private static function firstText(Crawler $crawler, string $selector): ?string
    {
        try {
            $node = $crawler->filter($selector);
            return $node->count() ? trim($node->first()->text()) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function rating(Crawler $crawler): ?string
    {
        return self::firstText($crawler, 'span.business-rating-badge-view__rating-text');
    }

    private static function totalRatings(Crawler $crawler): ?int
    {
        $text = self::firstText($crawler, 'div.business-header-rating-view__text._clickable');

        return self::extractNumber($text);
    }

    private static function totalReviews(Crawler $crawler): ?int
    {
        $text = self::firstText(
            $crawler,
            'div.tabs-select-view__title._name_reviews .tabs-select-view__counter'
        );

        return self::extractNumber($text);
    }

    private static function extractNumber(?string $text): ?int
    {
        if ($text === null) {
            return null;
        }

        preg_match('/\d[\d\s]*/u', $text, $matches);

        if (empty($matches[0])) {
            return null;
        }

        return (int) str_replace(' ', '', $matches[0]);
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'rating' => $this->rating,
            'total_ratings' => $this->total_ratings,
            'total_reviews' => $this->total_reviews,
        ];
    }
}
