<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnswerRepository::class)
 */
class Answer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Question::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private Question $question;


    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $is_correct;

    /**
     * @ORM\OneToMany(targetEntity=UserQuestionAnswer::class, mappedBy="answer")
     */
    private $userQuestionAnswers;

    public function __construct()
    {
        $this->userQuestionAnswers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?Question // Updated getter
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getIsCorrect(): ?bool
    {
        return $this->is_correct;
    }

    public function setIsCorrect(bool $is_correct): self
    {
        $this->is_correct = $is_correct;

        return $this;
    }

    /**
     * @return Collection<int, UserQuestionAnswer>
     */
    public function getUserQuestionAnswers(): Collection
    {
        return $this->userQuestionAnswers;
    }

    public function addUserQuestionAnswer(UserQuestionAnswer $userQuestionAnswer): self
    {
        if (!$this->userQuestionAnswers->contains($userQuestionAnswer)) {
            $this->userQuestionAnswers[] = $userQuestionAnswer;
            $userQuestionAnswer->setAnswer($this); // Updated method to match naming convention
        }

        return $this;
    }

    public function removeUserQuestionAnswer(UserQuestionAnswer $userQuestionAnswer): self
    {
        if ($this->userQuestionAnswers->removeElement($userQuestionAnswer)) {
            // set the owning side to null (unless already changed)
            if ($userQuestionAnswer->getAnswer() === $this) { // Updated method to match naming convention
                $userQuestionAnswer->setAnswer(null);
            }
        }

        return $this;
    }
}
