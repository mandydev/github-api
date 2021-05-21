<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Laravel\Socialite\Facades\Socialite;
use Github\Client as GithubClient;
use Cache;

class GithubController extends Controller
{
    public function test()
    {
        $client = new GithubClient();
        if (!Cache::has('github_user')) {
            return $this->githubAuthorize();
        }

        $githubUser = Cache::get('github_user');
        dd($githubUser);

        $client->authenticate('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', null, 'access_token_header');

        // * @param string      $username   the user who owns the repository
        // * @param string      $repository the name of the repository
        // * @param string      $path       path to file
        // * @param string      $content    contents of the new file
        // * @param string      $message    the commit message
        // * @param string|null $branch     name of a branch
        // * @param null|array  $committer  information about the committer

        $result = $client->api('repo')->contents()->create('mandydev', 'phpcurd', 'github', 'hello world', 'test commit', 'master', ['name' => 'Mandeep Kumar', 'email' => 'mandeepkumar1268@gmail.com']);
        
        dd($result);
    }

    public function githubAuthorize()
    {
        return Socialite::driver('github')->redirect();
    }

    public function githubCallback()
    {
        $user = Socialite::driver('github')->user();

        Cache::put('github_user', $user);
        
        return redirect('/github');
    }
}
