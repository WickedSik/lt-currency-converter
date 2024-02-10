<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Repository\CurrencyRateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRateRepository::class)]
class CurrencyRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 3)]
    private ?string $fromCode = null;

    #[ORM\Column(length: 3)]
    private ?string $toCode = null;

    #[ORM\Column(length: 255)]
    private ?string $toName = null;

    #[ORM\Column]
    private ?float $rate = null;

    #[ORM\Column]
    private ?float $inverseRate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromCode(): ?string
    {
        return $this->fromCode;
    }

    public function setFromCode(string $code): static
    {
        $this->fromCode = $code;

        return $this;
    }

    public function getToCode(): ?string
    {
        return $this->toCode;
    }

    public function setToCode(string $toCode): static
    {
        $this->toCode = $toCode;

        return $this;
    }

    public function getToName(): ?string
    {
        return $this->toName;
    }

    public function setToName(string $toName): static
    {
        $this->toName = $toName;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getInverseRate(): ?float
    {
        return $this->inverseRate;
    }

    public function setInverseRate(float $inverseRate): static
    {
        $this->inverseRate = $inverseRate;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}
