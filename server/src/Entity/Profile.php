<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{
    public const DICEBEAR_API = 'https://avatars.dicebear.com/api/adventurer-neutral/%s.svg';

    public const MAX_NAME_LENGTH = 32;
    public const MAX_BIO_LENGTH = 255;
    public const MAX_WEBSITE_LENGTH = 64;
    public const MAX_LINKEDIN_LENGTH = 255;
    public const MAX_GITHUB_LENGTH = 64;
    public const MAX_DISCORD_LENGTH = 32;
    public const MAX_COUNTRY_LENGTH = 64;
    public const MAX_PROFILE_PICTURE_LENGTH = 128;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['display'])]
    private $id;



    #[Assert\Length(
        max: self::MAX_NAME_LENGTH,
        maxMessage: 'Your name cannot be more than {{ limit }} characters.'
    )]
    #[Assert\Type('string')]
    #[ORM\Column(type: 'string', length: self::MAX_NAME_LENGTH, nullable: true)]
    #[Groups(['display'])]
    private $name;


    #[Assert\Length(
        max: self::MAX_BIO_LENGTH,
        maxMessage: 'Your biography cannot be more than {{ limit }} characters.'
    )]
    #[Assert\Type('string')]
    #[ORM\Column(type: 'string', length: self::MAX_BIO_LENGTH, nullable: true)]
    #[Groups(['display'])]
    private $biography;


    #[Assert\Type('string')]
    #[Assert\Date(message: 'Your birthday must be a valid date.')]
    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    #[Groups(['display'])]
    private $birthday;


    #[Assert\Length(
        max: self::MAX_WEBSITE_LENGTH,
        maxMessage: 'Your website url cannot be more than {{ limit }} characters.'
    )]
    #[Assert\Type('string')]
    #[ORM\Column(type: 'string', length: self::MAX_WEBSITE_LENGTH, nullable: true)]
    #[Groups(['display'])]
    private $website;


    #[Assert\Length(
        max: self::MAX_LINKEDIN_LENGTH,
        maxMessage: 'Your linkedin profile link cannot be more than {{ limit }} characters.'
    )]
    #[Assert\Type('string')]
    #[ORM\Column(type: 'string', length: self::MAX_LINKEDIN_LENGTH, nullable: true)]
    #[Groups(['display'])]
    private $linkedIn;


    #[Assert\Length(
        max: self::MAX_GITHUB_LENGTH,
        maxMessage: 'Your github profile link cannot be more than {{ limit }} characters.'
    )]
    #[Assert\Type('string')]
    #[ORM\Column(type: 'string', length: self::MAX_GITHUB_LENGTH, nullable: true)]
    #[Groups(['display'])]
    private $github;


    #[Assert\Length(
        max: self::MAX_DISCORD_LENGTH,
        maxMessage: 'Your discord tag cannot be more than {{ limit }} characters.'
    )]
    #[Assert\Type('string')]
    #[ORM\Column(type: 'string', length: self::MAX_DISCORD_LENGTH, nullable: true)]
    #[Groups(['display'])]
    private $discord;


    #[Assert\Length(
        max: self::MAX_COUNTRY_LENGTH,
        maxMessage: 'Your country name cannot be more than {{ limit }} characters.'
    )]
    #[Assert\Type('string')]
    #[ORM\Column(type: 'string', length: self::MAX_COUNTRY_LENGTH, nullable: true)]
    #[Groups(['display'])]
    private $country;


    #[Assert\Length(
        max: self::MAX_PROFILE_PICTURE_LENGTH,
        maxMessage: 'Your profile picture cannot be more than {{ limit }} characters.'
    )]
    #[Assert\Type('string')]
    #[ORM\Column(type: 'string', length: self::MAX_PROFILE_PICTURE_LENGTH)]
    #[Groups(['display'])]
    private $profilePicture;



    #[ORM\OneToOne(inversedBy: 'profile', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $owner;



    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['display'])]
    private $lastUpdate;

    public function __construct()
    {
        $this->setLastUpdate(new DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    public function setBirthday(string $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getLinkedIn(): ?string
    {
        return $this->linkedIn;
    }

    public function setLinkedIn(?string $linkedIn): self
    {
        $this->linkedIn = $linkedIn;

        return $this;
    }

    public function getGithub(): ?string
    {
        return $this->github;
    }

    public function setGithub(?string $github): self
    {
        $this->github = $github;

        return $this;
    }

    public function getDiscord(): ?string
    {
        return $this->discord;
    }

    public function setDiscord(?string $discord): self
    {
        $this->discord = $discord;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;
        $this->setProfilePicture(sprintf(self::DICEBEAR_API, $owner->getUserIdentifier()));

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeImmutable
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(\DateTimeImmutable $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }
}
