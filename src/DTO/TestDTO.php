<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TestDTO
{
    /**
     * @Assert\NotBlank()
     */
    private string $test;

    /**
     * @Assert\Valid()
     * @Assert\Count(min=1)
     */
    private array $questions;

    public function getTest(): ?string
    {
        return $this->test;
    }

    public function setTest(string $test): self
    {
        $this->test = $test;
        return $this;
    }

    /**
     * @return QuestionDTO[]
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function setQuestions(array $questions): self
    {
        $this->questions = $questions;
        return $this;
    }
}