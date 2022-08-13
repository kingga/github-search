<?php


namespace App\Services;


use App\Repositories\GithubRepository;
use Carbon\Carbon;

class GithubService
{
    /**
     * @var GithubRepository The Github repository.
     */
    private GithubRepository $repository;

    /**
     * GithubService constructor.
     * @param GithubRepository $repository
     */
    public function __construct(GithubRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Search for a user.
     * @param string $username
     * @return array|null
     */
    public function getUser(string $username): ?array
    {
        $user = $this->repository->findUser($username);

        if (!$user) {
            return null;
        }

        $user['created_at'] = Carbon::parse($user['created_at'])->format('d/m/Y h:i:s A');
        $user['repositories'] = $this->repository->getRepositories($username);

        return $user;
    }
}
