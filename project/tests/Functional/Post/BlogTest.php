<?php

namespace App\Tests\Functional\Post;

use App\Entity\Post\Tag;
use App\Entity\Post\Post;
use App\Repository\Post\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogTest extends WebTestCase
{
    public function testBlogPageWorks(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorExists('h1');
        $this->assertSelectorTextContains('h1', 'from scratch');
    }

    public function testPaginationWorks(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $posts = $crawler->filter('div.card');
        $this->assertEquals(9, count($posts));

        $link = $crawler->selectLink('2')->extract(['href'])[0];
        $crawler = $client->request(Request::METHOD_GET, $link);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $posts = $crawler->filter('div.card');
        $this->assertGreaterThanOrEqual(1, count($posts));
    }

    public function testDropdownWorks(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $link = $crawler->filter('.dropdown-menu > li > a')->link()->getUri();

        $client->request(Request::METHOD_GET, $link);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('category.index');
    }

    public function testSearchBarWorks(): void
    {

        $client = static::createClient();

        /** @var UrlGeneratorInterface */
        $urlGeneratorInterface = $client->getContainer()->get('router');

        /** @var EntityManagerInterface */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var PostRepository */
        $postRepository = $entityManager->getRepository(Post::class);

        /** @var Post */
        $post = $postRepository->findOneBy([]);

        /** @var Tag */
        $tag = $post->getTags()[0];

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGeneratorInterface->generate('post.index')
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $searches = [
            substr($post->getTitle(), 0, 3),
            substr($tag->getName(), 0, 3)
        ];

        foreach ($searches as $search) {
            $form = $crawler->filter('form[name=search]')->form([
                'search[q]' => $search
            ]);

            $crawler = $client->submit($form);

            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
            $this->assertRouteSame('post.index');

            $nbPosts = count($crawler->filter('div.card'));
            $postsTitle = $crawler->filter('div.card h5');
            $count = 0;

            foreach ($postsTitle as $title) {
                if (
                    str_contains($title->textContent, $search) ||
                    str_contains($tag->getName(), $search)
                ) {
                    $count++;
                }
            }

            $this->assertEquals($nbPosts, $count);
        }
    }

    public function testSearchBarReturnsNoItems(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface */
        $urlGeneratorInterface = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGeneratorInterface->generate('post.index')
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter('form[name=search]')->form([
            'search[q]' => 'aazzeerrttyy'
        ]);

        $crawler = $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('post.index');

        $this->assertSelectorExists('form[name=search]');
        $this->assertSelectorNotExists('div.card');
    }
}
