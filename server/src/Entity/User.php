<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[Orm\Entity(repositoryClass: UserRepository::class)]
#[Orm\Table(name: "`user`")]
#[UniqueEntity('username', message: 'This username is already in use.')]
#[UniqueEntity('email', message: 'This email address is already in use.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const MAX_EMAIL_LENGTH = 180;
    public const MIN_PASSWORD_LENGTH = 5;
    public const MIN_USERNAME_LENGTH = 5;
    public const MAX_USERNAME_LENGTH = 32;

    #[Orm\Id]
    #[Orm\GeneratedValue]
    #[Orm\Column(type: "integer")]
    private $id;



    #[Assert\NotBlank(message: 'Please specify a valid email.')]
    #[Assert\Email(
        message: 'Please specify a valid email ({{ value }} is not a valid email address).'
    )]
    #[Assert\Length(
        max: self::MAX_EMAIL_LENGTH,
        maxMessage: 'Your email address cannot be more than {{ limit }} characters.'
    )]
    #[Assert\Type('string')]
    #[Orm\Column(type: "string", length: self::MAX_EMAIL_LENGTH, unique: true)]
    private $email;



    #[Orm\Column(type: "json")]
    private $roles = [];



    /**
     * @var string The hashed password
     */
    #[Assert\NotBlank(message: 'Your password cannot be empty.')]
    #[Assert\Length(
        min: self::MIN_PASSWORD_LENGTH,
        minMessage: 'Your password must be at least {{ limit }} characters.'
    )]
    #[Orm\Column(type: "string")]
    private $password;



    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9-_]*$/',
        match: true,
        message: 'Your username is not valid. Try not using any special chars.'
    )]
    #[Assert\Length(
        min: self::MIN_USERNAME_LENGTH,
        minMessage: 'Your username must be at least {{ limit }} characters.',
        max: self::MAX_USERNAME_LENGTH,
        maxMessage: 'Your username cannot be more than {{ limit }} characters.',
    )]
    #[Assert\Type(type: 'string', message: 'Username must be a valid string.')]
    #[Assert\NotBlank(message: 'You must specify an username.')]
    #[Orm\Column(type: "string", length: 32, unique: true)]
    private $username;

    #[ORM\Column(type: 'datetime_immutable')]
    private $registeredAt;

    #[ORM\OneToOne(mappedBy: 'owner', targetEntity: Profile::class, cascade: ['persist', 'remove'])]
    private $profile;



    public function __construct()
    {
        $this->setRegisteredAt(new DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRegisteredAt(): ?\DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(\DateTimeImmutable $registeredAt): self
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(Profile $profile): self
    {
        // set the owning side of the relation if necessary
        if ($profile->getOwner() !== $this) {
            $profile->setOwner($this);
        }

        $this->profile = $profile;

        return $this;
    }
}
