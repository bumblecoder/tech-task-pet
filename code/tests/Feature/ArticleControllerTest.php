<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexReturnsPaginatedArticles(): void
    {
        $articles = Article::factory()->count(10)->create();

        $response = $this->getJson('/api/articles?per_page=5');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['title' => $articles[0]->title])
            ->assertHeader('X-Total-Count', 10);
    }

    public function testStoreCreatesArticle(): void
    {
        $data = [
            'title' => 'New Article',
            'content' => 'Content of the new article',
        ];

        $response = $this->postJson('/api/articles', $data);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('articles', $data);
    }

    public function testUpdateUpdatesArticle()
    {
        $article = Article::factory()->create();
        $data = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ];

        $response = $this->putJson("/api/articles/{$article->id}", $data);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('articles', $data);
    }

    public function testUpdateReturnsNotFoundForInvalidId()
    {
        $data = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ];

        $response = $this->putJson('/api/articles/999', $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'Article not found']);
    }

    public function testDestroyDeletesArticle()
    {
        $article = Article::factory()->create();

        $response = $this->deleteJson("/api/articles/{$article->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(['message' => 'Article deleted successfully']);

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function testDestroyReturnsNotFoundForInvalidId()
    {
        $response = $this->deleteJson('/api/articles/999');

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'Article not found']);
    }
}
