<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
// ? I used backslash instead of use DateTimeImmutable
// use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Symfony\Component\String\u;

class AppFixtures extends Fixture
{

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly SluggerInterface $slugger
    ) {
    }


    public function load(ObjectManager $manager): void
    {
        $this->loadUser($manager);
        $this->loadTag($manager);
        $this->loadPost($manager);
        $manager->flush();
    }

    private function loadUser(ObjectManager $manager): void
    {
        $users = $this->getUsers();
        foreach ($users as [$fullName, $username, $email, $password, $roles]) {
            $user = (new User())
                ->setFullName($fullName)
                ->setUsername($username)
                ->setEmail($email)
                ->setRoles($roles);
            $user->setPassword($this->hasher->hashPassword($user, $password));

            $manager->persist($user);
            $this->addReference($username, $user);
        }

        $manager->flush();
    }

    private function getUsers(): array
    {
        return [
            ["Benidris Abdenour", "Abdenour", "abdenour@gmail.com", "password", ["ROLE_ADMIN"]],
            ["Loy Mathiassen", "lmathiassen0", "lmathiassen0@desdev.cn", "password", ["ROLE_ADMIN"]],
            ["Olajid fara", "farah234", "@dev.fr", "password", ["ROLE_ADMIN"]],
            ["Gideon Stolli", "gstolli1", "gstolli1@theatlantic.com", "password", ["ROLE_USER"]],
            ["Allie Taite", "MacClancey", "amacclancey2@yahoo.com", "password", ["ROLE_USER"]],
            ["Nonah Reagen", "Perrott", "nperrott0@spotify.com", "password", ["ROLE_USER"]]
        ];
    }


    private function loadTag(ObjectManager $manager): void
    {
        foreach ($this->getTags() as $name) {
            $tag = new Tag();
            $tag->setName($name);
            $manager->persist($tag);
            $this->addReference($name, $tag);
        }
        $manager->flush();
    }

    private function getTags(): array
    {
        return [
            "apollo",
            "slide",
            "currency",
            "country",
            "bike",
            "horse",
            "car"
        ];
    }

    private function loadPost(ObjectManager $manager): void
    {
        foreach ($this->getPosts() as [$title, $slug, $summary, $content, $publishedAt, $user, $tags]) {
            $post = (new Post())
                ->setTitle($title)
                ->setSlug($slug)
                ->setSummary($summary)
                ->setContent($content)
                ->setPublishedAt($publishedAt)
                ->setAuthor($user)
                ->addTag(...$tags);


            // ? Generating 1 to 5 comments per post
            foreach (range(1, 5) as $i) {
                $comment = (new Comment())
                    ->setAuthor($this->getReference(["gstolli1", "MacClancey", "Perrott"][1 == $i ? 1 : random_int(0, 2)]))
                    ->setContent($this->makeSummary(random_int(255, 455)))
                    ->setPublishedAt(new \DateTimeImmutable("now + $i minutes"))
                    ->setPost($post);

                $manager->persist($comment);
            }
            $manager->persist($post);
        }
        $manager->flush();
    }

    private function getPosts(): array
    {
        $posts = [];
        foreach ($this->getPhrases() as $i => $title) {
            $user = $this->getReference(["Abdenour", "lmathiassen0", "farah234"][0 == $i ? 0 : random_int(0, 2)]);
            $posts[] = [
                $title,
                $this->slugger->slug($title)->lower(),
                $this->makeSummary(),
                $this->getContent(),
                new \DateTimeImmutable,
                $user,
                $this->makeTags()

            ];
        }
        return $posts;
    }


    private function getPhrases(): array
    {
        return [
            "Enim anim aliquip est reprehenderit nisi consequat veniam",
            "irure commodo mollit nulla ipsum non pariatur qui cillum",
            "Eu mollit ad ex esse voluptate voluptate",
            "Duis ut eu quis commodo ipsum qui occaecat ut id",
            "Adipisicing tempor nisi amet commodo",
            "Eu sit aliquip cupidatat eiusmod Lorem",
            "Nulla elit cupidatat culpa veniam quis in",
            "Magna excepteur nulla incididunt qui commodo",
            "Occaecat est reprehenderit est laborum cillum do duis do",
            "Voluptate amet reprehenderit sunt aute non",
            "Irure adipisicing Lorem ea nostrud nostrud laboris anim",
            "culpa consectetur mollit ad reprehenderit exercitation",
            "Ad mollit ad excepteur incididunt laboris",
            "consectetur non ad ex mollit velit amet",
            "amet magna ipsum cupidatat deserunt fugiat velit",
            "Aute cillum Lorem laboris deserunt ipsum mollit sit in ipsum",
            "consequat minim laborum id est sint laboris",
            "tempor proident fugiat id labore aliquip"
        ];
    }


    private function getContent(): string
    {
        return  <<<'Markdown'
            Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. 
        Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. 
        Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. 
        Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu.

        In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. 
        Integer tincidunt :

            + Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate. 
            + Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. 
            + Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus.

        Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. 
        Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. 
        Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, 
        sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem.

        Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. 
        Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. 
        Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc, 
        quis gravida magna mi a libero. Fusce vulputate eleifend sapien. Vestibulum purus quam, scelerisque ut, 
        mollis sed, nonummy id, metus. Nullam accumsan lorem in dui. Cras ultricies mi eu turpis hendrerit fringilla. 
        Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; In ac dui quis mi
        Markdown;
    }

    // ? Function to Generate random summary for post
    private function makeSummary(int $maxlength = 255): string
    {
        $phrases = $this->getPhrases();
        shuffle($phrases);
        do {
            $summary = u(". ")->join($phrases)->append('.');
            array_pop($phrases);
        } while ($summary->length() > $maxlength);
        return $summary;
    }

    // ? Function to Generate random tags for post
    private function makeTags(): array
    {
        $tags = $this->getTags();
        shuffle($tags);
        $selectedTags = array_slice($tags, 0, random_int(2, 5));

        return array_map(function ($tag): Tag {
            return $this->getReference($tag);
        }, $selectedTags);
    }
}
