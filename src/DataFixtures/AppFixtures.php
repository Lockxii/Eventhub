<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Registration;
use App\Entity\User;
use App\Enum\EventStatus;
use App\Enum\RegistrationStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $categories = [];
        foreach (['technology' => 'Technology', 'community' => 'Community'] as $slug => $name) {
            $category = $manager->getRepository(Category::class)->findOneBy(['slug' => $slug]);
            if (!$category instanceof Category) {
                $category = (new Category())->setName($name)->setSlug($slug);
                $manager->persist($category);
            }
            $categories[$slug] = $category;
        }

        $users = [];
        foreach ([
            'organizer' => ['organizer@example.test', 'organizer'],
            'participant' => ['participant@example.test', 'participant'],
            'member' => ['member@example.test', 'member'],
        ] as $key => [$email, $username]) {
            $user = $manager->getRepository(User::class)->findOneBy(['email' => $email]);
            if (!$user instanceof User) {
                $user = new User();
                $user->setEmail($email);
                $user->setUsername($username);
                $user->setPassword($this->passwordHasher->hashPassword($user, 'eventhub-demo'));
                $manager->persist($user);
            }
            $users[$key] = $user;
        }

        $events = [];
        foreach ([
            'php-meetup' => ['PHP Meetup', 'A community meetup about modern PHP.', 'technology', EventStatus::Published, 7, 50],
            'community-workshop' => ['Community Workshop', 'A practical workshop for local organizers.', 'community', EventStatus::Draft, 14, 25],
        ] as $slug => [$title, $description, $categorySlug, $status, $daysAhead, $capacity]) {
            $event = $manager->getRepository(Event::class)->findOneBy(['slug' => $slug]);
            if (!$event instanceof Event) {
                $startAt = (new \DateTimeImmutable(sprintf('+%d days', $daysAhead)))->setTime(18, 0);
                $event = (new Event())
                    ->setTitle($title)
                    ->setSlug($slug)
                    ->setDescription($description)
                    ->setStartAt($startAt)
                    ->setEndAt($startAt->modify('+2 hours'))
                    ->setCapacity($capacity)
                    ->setStatus($status)
                    ->setOrganizer($users['organizer'])
                    ->setCategory($categories[$categorySlug]);
                $manager->persist($event);
            }
            $events[$slug] = $event;
        }

        foreach ([
            ['php-meetup', 'participant', RegistrationStatus::Confirmed],
            ['php-meetup', 'member', RegistrationStatus::Waitlist],
            ['community-workshop', 'participant', RegistrationStatus::Cancelled],
        ] as [$eventSlug, $userKey, $status]) {
            $event = $events[$eventSlug];
            $user = $users[$userKey];
            $registration = $manager->getRepository(Registration::class)->findOneBy([
                'event' => $event,
                'user' => $user,
            ]);
            if (!$registration instanceof Registration) {
                $registration = (new Registration())
                    ->setStatus($status);
                $event->addRegistration($registration);
                $user->addRegistration($registration);
                $manager->persist($registration);
            }
        }

        $manager->flush();
    }
}
