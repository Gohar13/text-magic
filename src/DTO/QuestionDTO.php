<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class QuestionDTO
{
    /**
     * @Assert\NotBlank()
     */
    private string $text;

    /**
     * @Assert\Valid()
     * @Assert\Count(min=1)
     */
    private array $answers;

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return AnswerDTO[]
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function setAnswers(array $answers): self
    {
        $this->answers = $answers;
        return $this;
    }
}