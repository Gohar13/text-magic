<?php

namespace App\Factories;

use App\DTO\AnswerDTO;
use App\DTO\QuestionDTO;
use App\DTO\TestDTO;

class TestDTOFactory
{
    public function create(array $data): TestDTO
    {
        if (empty($data['test'])) {
            throw new \InvalidArgumentException('The "test" field is required and cannot be empty.');
        }

        $testDTO = new TestDTO();
        $testDTO->setTest($data['test']);

        if (!isset($data['questions']) || !is_array($data['questions'])) {
            throw new \InvalidArgumentException('The "questions" field is required and must be an array.');
        }

        $questions = [];
        foreach ($data['questions'] as $questionData) {
            if (empty($questionData['text'])) {
                throw new \InvalidArgumentException('Each question must have a "text" field and it cannot be empty.');
            }

            $questionDTO = new QuestionDTO();
            $questionDTO->setText($questionData['text']);

            if (!isset($questionData['answers']) || !is_array($questionData['answers'])) {
                throw new \InvalidArgumentException('Each question must have an "answers" field and it must be an array.');
            }

            $answers = [];
            foreach ($questionData['answers'] as $answerData) {

                if (!isset($answerData['text']) || !isset($answerData['is_correct'])) {
                    throw new \InvalidArgumentException('Each answer must have "text" and "is_correct" fields.');
                }

                $answerDTO = new AnswerDTO();
                $answerDTO->setText($answerData['text']);
                $answerDTO->setIsCorrect($answerData['is_correct']);
                $answers[] = $answerDTO;
            }

            $questionDTO->setAnswers($answers);
            $questions[] = $questionDTO;
        }

        $testDTO->setQuestions($questions);
        return $testDTO;
    }
}