<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Github\Client as GithubClient;

class GithubSyncController extends Controller
{
    protected $client;
    protected $githubUser;

    public function __construct(GithubClient $client)
    {
        $this->client = $client;
    }

    public function sync(Request $request)
    {
        $client = $this->client;
        $user   = $this->githubAuthorize($request->token);
        $commit = 'Test commit';

        $this->commitFiles($request->dir, $request->repo, $commit);
        
        die('Files successfully uploaded to github.');
    }

    protected function commitFiles($dir, $repo, $commit)
    {
        $files = $this->getDirContents($dir);

        foreach ($files as $file) {
            if (file_exists($file) && !is_dir($file)) {
                $this->commit($file, $repo, $commit, $dir);
            }
        }
    }

    private function commit($file, $repo, $commit, $dir)
    {

        $client   = $this->client;
        $user     = $this->githubUser['login'];
        $contents = file_get_contents($file);
        $path     = str_replace('\\', '/', trim(str_replace(public_path($dir), '', $file), '\\'));

        try {
            if ($client->api('repo')->contents()->exists($user, $repo, $path)) {
                $content = $client->api('repo')->contents()->show($user, $repo, $path);
                $client->api('repo')->contents()->update($user, $repo, $path, $contents, $commit, $content['sha']);
            } else {
                $client->api('repo')->contents()->create($user, $repo, $path, $contents, $commit);
            }
        } catch (\Exception $e) {

        }
    }

    private function getDirContents($dir, &$results = [])
    {
        $dir = public_path($dir);

        if (is_dir($dir)) {
            $files = scandir($dir);

            foreach ($files as $key => $value) {
                $path = realpath($dir . DIRECTORY_SEPARATOR . $value);

                if (!is_dir($path)) {
                    $results[] = $path;
                } else if ($value != "." && $value != "..") {
                    $this->getDirContents(str_replace(public_path(), '', $path), $results);
                    $results[] = $path;
                }
            }
        }

        return $results;
    }

    private function githubAuthorize($token)
    {
        $client = $this->client;
        $client->authenticate($token, null, 'access_token_header');

        $user = $client->api('current_user')->show();

        $this->githubUser = $user;

        return $user;
    }
}
