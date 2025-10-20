<?php

declare(strict_types=1);

namespace Workbench\App\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected int|null $id = null;

    #[ORM\Column(name: 'name')]
    public string $name;

    #[ORM\Column(name: 'email')]
    public string $email;

    #[ORM\Column(name: 'password')]
    public string $password;

    public function __construct(string $name, string $email, string $password)
    {
        $this->name     = $name;
        $this->email    = $email;
        $this->password = $password;
    }
}
