<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Responses\JsonResponse;
use App\Services\AuthenticationService;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\InternalServerErrorException;
use App\Exceptions\NotFoundException;
use App\Repositories\ArticleRepository;

final class ArticlesController
{
    final public function __construct(private Request $request, private ArticleRepository $articleRepository, private JsonResponse $response, private AuthenticationService $authenticationService) {
        if (!$this->authenticationService->authenticated($this->request)) {
            throw new UnauthorizedException("You must be authenticated to access this resource.");
        }
    }

    final public function get(): JsonResponse
    {
        $articles = $this->articleRepository->getArticles();

        return $this->response
            ->withField("success", true)
            ->withField("articles", $articles)
            ->withHeader("X-Server", "IIS/10.0")
            ->withStatusCode(200);
    }

    final public function post(): JsonResponse
    {
        $body = $this->request->validate([
            "description" => "required|min:10",
        ]);

        $successfullyCreatedArticle = $this->articleRepository->createArticle($body);

        if (!$successfullyCreatedArticle) {
            throw new InternalServerErrorException("An error occurred while creating the article.");
        }

        return $this->response
            ->withField("success", true)
            ->withField("message", "created")
            ->withHeader("X-Server", "IIS/10.0")
            ->withStatusCode(200);
    }

    final public function delete(): JsonResponse
    {
        $article = $this->request->parameter("article");

        $foundArticle = $this->articleRepository->getArticleById($article);

        if (!$foundArticle) {
            throw new NotFoundException("Article not found.");
        }

        $successfullyDeletedArticle = $this->articleRepository->deleteArticle($article);

        if (!$successfullyDeletedArticle) {
            throw new InternalServerErrorException("An error occurred while deleting the article.");
        }

        return $this->response
            ->withField("success", true)
            ->withField("message", "deleted")
            ->withHeader("X-Server", "IIS/10.0")
            ->withStatusCode(200);
    }

    final public function patch(): JsonResponse
    {
        $article = $this->request->parameter("article");

        $foundArticle = $this->articleRepository->getArticleById($article);

        if (!$foundArticle) {
            throw new NotFoundException("Article not found.");
        }

        $body = $this->request->validate([
            "description" => "required|min:10",
        ]);

        $successfullyUpdatedArticle = $this->articleRepository->updateArticle($article, $body);

        if (!$successfullyUpdatedArticle) {
            throw new InternalServerErrorException("An error occurred while updating the article.");
        }

        return $this->response
            ->withField("success", true)
            ->withField("message", "updated")
            ->withHeader("X-Server", "IIS/10.0")
            ->withStatusCode(200);
    }
}
