<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

class ArticleTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function it_shows_a_collection_of_articles()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);
        $article = Article::factory()->create();

        $this->json('GET', '/api/articles')
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => [
                    [
                        'id' => $article->id,
                        'attributes' => [
                            'title' => $article->title,
                            'content' => $article->content,
                            'picture' => $article->thumbnail,
                            'created_at' => $article->created_at->diffForHumans()
                        ],
                    ]
                ],
            ]);
    }

    /**
     * @test
     */
    public function it_shows_an_article()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);
        $article = Article::factory()->create();

        $this->json('GET', "/api/articles/{$article->slug}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'id' => $article->id,
                'attributes' => [
                    'title' => $article->title,
                    'content' => $article->content,
                    'picture' => $article->thumbnail,
                    'created_at' => $article->created_at->diffForHumans()
                ],
            ]);
    }

    /**
     * @test
     */
    public function it_creates_an_article()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $data = [
            'title' => 'This is the title',
            'thumbnail' => 'https://picsum.photos/250/200',
            'content' => 'This is the contetn',
            'user_id' => $user->id,
        ];

        $this->json('POST', '/api/articles', $data)
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertEquals(1, Article::count());
    }

    /**
     * @test
     */
    public function it_deletes_an_article()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);
        $article = Article::factory()->create();

        $this->json('DELETE', "/api/articles/{$article->slug}")
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertNull(Article::find($article->id));
    }

    /**
     * @test
     */
    public function it_cannot_deletes_an_article_that_dont_belongs_to_the_user()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $article = Article::factory()->for($user)->create();

        Sanctum::actingAs($user1);

        $this->json('DELETE', "/api/articles/{$article->slug}")
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
