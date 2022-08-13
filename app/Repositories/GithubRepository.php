<?php


namespace App\Repositories;

use Illuminate\Support\Facades\Http;

/**
 * Class GithubRepository
 * @package App\Repositories
 */
class GithubRepository
{
    /**
     * @var string The URL for the API.
     */
    private string $api = 'https://api.github.com';

    /**
     * Find a user by their username.
     * @param string $username
     * @return array|null
     */
    public function findUser(string $username): ?array
    {
        $response = Http::get("$this->api/users/$username");

        if ($response->status() !== 200) {
            return null;
        }

        $data = collect($response->json());

        return $data->only(
            'login',
            'avatar_url',
            'html_url',
            'name',
            'blog',
            'location',
            'followers',
            'following',
            'public_repos',
            'created_at',
        )->toArray();
    }

    /**
     * Get the repositories for a user.
     * @param string $username
     * @return array
     */
    public function getRepositories(string $username): array
    {
        $response = Http::get("$this->api/users/$username/repos");

        // Only collect the information we want to return to the user.
        $data = collect($response->json())->map(function ($repo) {
            return [
                'name' => $repo['name'],
                'url' => $repo['html_url'],
                'description' => $repo['description'],
                'stars' => $repo['stargazers_count'],
                'watchers' => $repo['watchers_count'],
                'forks' => $repo['forks_count'],
                'language' => $repo['language'],
            ];
        });

        // Sort the repos by star count to find the most popular ones.
        $data = $data->sortByDesc('stars');

        // Only the top 4.
        return $data->slice(0, 4)->toArray();
    }
}
