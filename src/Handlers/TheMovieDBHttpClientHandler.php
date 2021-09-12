<?php

namespace App\Handlers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TheMovieDBHttpClientHandler
{

    private HttpClientInterface $client;

    private string $url;
    private string $token;

    private array $headers = [];

    public function __construct(HttpClientInterface $client, $url, $token)
    {
        $this->client = $client;
        $this->url = $url;
        $this->token = $token;

        $this->headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ];
    }

    public function getLatest(array $parameters = [], array $headers = [])
    {
        return $this->call('movie/latest', $parameters, $headers);
    }

    public function getUpcoming(array $parameters = [], array $headers = [])
    {
        return $this->call('movie/upcoming', $parameters, $headers);
    }

    public function getGenders(array $parameters = [], array $headers = [])
    {
        return $this->call('genre/movie/list', $parameters, $headers);
    }

    public function getGenderMovies($genre_id, array $parameters = [], array $headers = [])
    {
        return $this->call('genre/' . $genre_id . '/movies', $parameters, $headers);
    }

    public function searchMovies($query, array $parameters = [], array $headers = [])
    {
        return $this->call('search/movie', array_merge($parameters, [
            'query' => $query
        ], $headers));
    }

    public function getMovie($movie_id, array $parameters = [], array $headers = [])
    {
        return $this->call('movie/' . $movie_id, $parameters, $headers);
    }

    public function getCredits($movie_id, array $parameters = [], array $headers = [])
    {
        return $this->call('movie/' . $movie_id . '/credits', $parameters, $headers);
    }

    public function getTrailers($movie_id, array $parameters = [], array $headers = [])
    {
        return $this->call('movie/' . $movie_id . '/trailers', $parameters, $headers);
    }

    private function call(string $endpoint, array $params = [], array $headers = []): array
    {
        try {
            $querystring = '?' . http_build_query($params);


            $response = $this->client->request('GET', $this->url . $endpoint . $querystring, ['headers' => array_merge($this->headers, $headers)]);

            return (Response::HTTP_OK) ? $response->toArray() : [];
        } catch (\Exception $e) {
            dump($e->getMessage(), $e->getFile(), $e->getLine());
            die;
        }
    }
}
