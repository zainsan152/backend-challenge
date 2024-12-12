<?php

namespace App\Console\Commands;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:fetch';

    protected $description = 'Fetch articles from external sources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching articles from NewsAPI...');
        $this->fetchFromNewsAPI();

        $this->info('Fetching articles from The Guardian...');
        $this->fetchFromGuardian();

        $this->info('Fetching articles from BBC News...');
        $this->fetchFromBBC();

        $this->info('Fetching articles from New York Times...');
        $this->fetchFromNYTimes();

        $this->info('Articles fetched successfully.');
    }

    public function fetchFromGuardian()
    {
        try {
            DB::beginTransaction();
            $response = Http::get('https://content.guardianapis.com/search', [
                'api-key' => env('GUARDIAN_API_KEY'),
                'section' => 'technology',
                'show-fields' => 'headline,byline,trailText,webPublicationDate,webUrl',
                'page-size' => 10,
            ]);

            if ($response->ok()) {
                $articles = $response->json()['response']['results'];

                foreach ($articles as $data) {
                    Article::updateOrCreate(
                        ['url' => $data['webUrl']],
                        [
                            'title' => $data['fields']['headline'],
                            'description' => $data['fields']['trailText'],
                            'author' => $data['fields']['byline'] ?? null,
                            'source' => 'The Guardian',
                            'category' => $data['sectionName'] ?? 'General',
                            'url' => $data['webUrl'],
                            'published_at' => $data['webPublicationDate'] ?? null,
                        ]
                    );
                }
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
        }
    }

    public function fetchFromBBC()
    {
        try {
            DB::beginTransaction();
            $response = Http::get('https://newsapi.org/v2/top-headlines', [
                'apiKey' => env('NEWS_API_KEY'),
                'sources' => 'bbc-news',
            ]);

            if ($response->ok()) {
                $articles = $response->json()['articles'];

                foreach ($articles as $data) {
                    Article::updateOrCreate(
                        ['url' => $data['url']],
                        [
                            'title' => $data['title'],
                            'description' => $data['description'],
                            'author' => $data['author'] ?? null,
                            'source' => 'BBC News',
                            'category' => 'General',
                            'url' => $data['url'],
                            'published_at' => $data['publishedAt'] ?? null,
                        ]
                    );
                }
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
        }
    }

    public function fetchFromNewsAPI()
    {
        try {
            DB::beginTransaction();
            $response = Http::get('https://newsapi.org/v2/top-headlines', [
                'apiKey' => env('NEWS_API_KEY'),
                'country' => 'us',
                'category' => 'technology',
            ]);


            if ($response->ok()) {
                $articles = $response->json()['articles'];

                foreach ($articles as $data) {
                    Article::updateOrCreate(
                        ['url' => $data['url']],
                        [
                            'title' => $data['title'],
                            'description' => $data['description'],
                            'author' => $data['author'],
                            'source' => 'NewsAPI',
                            'category' => $data['category'] ?? null,
                            'url' => $data['url'],
                            'published_at' => $data['publishedAt'] ?? null
                        ]
                    );
                }
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
        }
    }

    public function fetchFromNYTimes()
    {
        try {
            DB::beginTransaction();
            $response = Http::get('https://api.nytimes.com/svc/topstories/v2/technology.json', [
                'api-key' => env('NYT_API_KEY'),
            ]);

            if ($response->ok()) {
                $articles = $response->json()['results'];

                foreach ($articles as $data) {
                    Article::updateOrCreate(
                        ['url' => $data['url']],
                        [
                            'title' => $data['title'],
                            'description' => $data['abstract'],
                            'author' => $data['byline'] ?? null,
                            'source' => 'New York Times',
                            'category' => $data['section'] ?? 'General',
                            'url' => $data['url'],
                            'published_at' => $data['published_date'] ?? null,
                        ]
                    );
                }
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
        }
    }
}

