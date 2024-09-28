<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AnswerDTO
{
    /**
     * @Assert\NotBlank()
     */
    private string $text;

    /**
     * @Assert\Type("bool")
     */
    private bool $is_correct;

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function isCorrect(): ?bool
    {
        return $this->is_correct;
    }

    public function setIsCorrect(bool $is_correct): self
    {
        $this->is_correct = $is_correct;
        return $this;
    }
}