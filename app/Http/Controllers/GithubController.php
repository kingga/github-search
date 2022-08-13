<?php

namespace App\Http\Controllers;

use App\Http\Requests\GithubSearchRequest;
use App\Services\GithubService;
use Inertia\Inertia;
use Inertia\Response;

class GithubController extends Controller
{
    private GithubService $service;

    /**
     * GithubController constructor.
     * @param GithubService $service
     */
    public function __construct(GithubService $service)
    {
        $this->service = $service;
    }

    /**
     * Get the GitHub search page.
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('GithubProfilePage');
    }

    /**
     * Search for a user and return their information.
     * @param GithubSearchRequest $request
     * @return array
     */
    public function search(GithubSearchRequest $request): array
    {
        $user = $this->service->getUser($request->search);

        if ($user === null) {
            abort(404);
        }

        return $user;
    }
}
