<?php

namespace App\Entity;

use App\Repository\FeedbackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'feedback')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $author = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    private ?float $productRating = null;

    #[ORM\ManyToOne(inversedBy: 'feedback')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    /**
     * @var Collection<int, FeedBackRating>
     */
    #[ORM\OneToMany(targetEntity: FeedBackRating::class,mappedBy: 'feedback', cascade: ['remove'], orphanRemoval: true )]
    private Collection $feedBackRatings;

    public function __construct()
    {
        $this->feedBackRatings = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?Profile
    {
        return $this->author;
    }

    public function setAuthor(?Profile $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getProductRating(): ?float
    {
        return $this->productRating;
    }

    public function setProductRating(?float $productRating): static
    {
        $this->productRating = $productRating;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    /**
     * @return Collection<int, FeedBackRating>
     */
    public function getFeedBackRatings(): Collection
    {
        return $this->feedBackRatings;
    }

    public function addFeedBackRating(FeedBackRating $feedBackRating): static
    {
        if (!$this->feedBackRatings->contains($feedBackRating)) {
            $this->feedBackRatings->add($feedBackRating);
            $feedBackRating->setFeedback($this);
        }

        return $this;
    }

    public function removeFeedBackRating(FeedBackRating $feedBackRating): static
    {
        if ($this->feedBackRatings->removeElement($feedBackRating)) {
            // set the owning side to null (unless already changed)
            if ($feedBackRating->getFeedback() === $this) {
                $feedBackRating->setFeedback(null);
            }
        }

        return $this;
    }


    public function getAverageRelevance(Feedback $feedback): ?float
    {

        $total = 0;
        $count = 0;

        foreach ($this->getFeedBackRatings() as $rate) {
            if ($rate->getRate() !== null) {
                $total += $rate->getRate();
                $count++;
            }
        }

       return  $count > 0 ? $total / $count : null;    }



}
