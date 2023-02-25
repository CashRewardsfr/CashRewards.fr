<?php

namespace App\Entity;

use App\Repository\LogRepository;
use App\Entity\Traits\Timestamp;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Log
{
    use Timestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string')]
    private $offerwallName;

    #[ORM\Column(type: 'json')]
    private $params;

    #[ORM\Column(type: 'integer')]
    private $result;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOfferwallName(): ?string
    {
        return $this->offerwallName;
    }

    public function setOfferwallName(?string $offerwallName): self
    {
        $this->offerwallName = $offerwallName;

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    public function getResult(): ?int
    {
        return $this->result;
    }

    public function setResult(?int $result): self
    {
        $this->result = $result;

        return $this;
    }
}
