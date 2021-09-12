<?php

namespace App\Controller;

use App\Handlers\TheMovieDBHttpClientHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tmdb\ApiToken;
use Tmdb\Client;

class MainController extends AbstractController
{

    /** @var TheMovieDBHttpClientHandler */
    private $client;

    /**
     * MainController constructor.
     * @param TheMovieDBHttpClientHandler $client
     */
    public function __construct(TheMovieDBHttpClientHandler $client)
    {
        $this->client = $client;
    }


    /**
     * @Route("/", name="home")
     */
    public function index() : Response
    {
        $genders = $this->client->getGenders();
        $movies = $this->client->getUpcoming();
        $latestMovieTrailer = $this->client->getTrailers($movies['results'][0]['id']);
        return $this->render('home/index.html.twig', [
            'latest' => $latestMovieTrailer,
            'genders'   => $genders,
            'movies' => $movies,
        ]);
    }

    /**
     * @Route("/genre/movie", name="genre-movie-list")
     */
    public function genreMoviesList(Request $request ) : Response
    {
        $ids_genre = $request->request->get('gender');
        $ids_genre = is_array($ids_genre) ? $ids_genre : [$ids_genre];

        $movies['results'] = array_merge(... array_column(array_map(function ($id){
            return $this->client->getGenderMovies($id);
        }, $ids_genre), 'results'));

        $genders = $this->client->getGenders();

        return $this->render('home/index.html.twig', [
            'latest' => [],
            'genders'   => $genders,
            'movies' => $movies,
        ]);
    }

    /**
     * @Route("/search",name="search")
     */
    public function search(Request $request): Response
    {
        $search = trim($request->get('search'));
        if ($search == "") {
            return $this->render('movies_list/no_match.html.twig');
        } else {

            $searchByTitle = $this->client->searchMovies($search);

            return $this->render('movie/search.html.twig', [
                'bytitles' => $searchByTitle["results"],
            ]);
        }
    }

    /**
     * @Route("movie/{id}", name="movie_details")
     */
    public function movieDetails($id) : Response
    {

        $movie = $this->client->getMovie($id);
        $credits = $this->client->getCredits($id);
        $trailers = $this->client->getTrailers($id);

        return $this->render('movie/details.html.twig',[
            'movie' => $movie,
            'credits' => $credits,
            'trailers'=> $trailers
        ]);
    }
}
