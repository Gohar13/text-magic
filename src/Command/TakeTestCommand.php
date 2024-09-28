<?php

namespace App\Command;

use App\Entity\Answer;
use App\Entity\Test;
use App\Entity\UserQuestionAnswer;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\Table;

class TakeTestCommand extends Command
{
    protected static $defaultName = 'app:take-test';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Get all questions and answers in random order.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $test = $this->entityManager->getRepository(Test::class)->findOneBy([], ['id' => 'DESC']);

        if (!$test) {
            $output->writeln('Test not found.');
            return Command::FAILURE;
        }

        $questions = $test->getQuestions();
        if ($questions->isEmpty()) {
            $output->writeln('No questions found for this test.');
            return Command::SUCCESS;
        }

        $questionHelper = $this->getHelper('question');
        $tableHelper = new Table($output);
        $tableRowData = [];

        $questionsArray = $questions->toArray();
        shuffle($questionsArray);

        foreach ($questionsArray as $question) {
            $mappedAnswers = $this->mapAnswers($question->getAnswers());
            $consoleQuestion = new ChoiceQuestion(
                $question->getText(),
                array_keys($mappedAnswers)
            );
            $consoleQuestion->setMultiselect(true);
            $userAnswers = $questionHelper->ask($input, $output, $consoleQuestion);

            $this->processUserAnswers($userAnswers, $mappedAnswers, $question, $tableRowData);
        }

        $tableHelper
            ->setHeaders(['Question', 'Your answer', 'Is correct'])
            ->setRows($tableRowData)
            ->render($output);

        return Command::SUCCESS;
    }

    private function mapAnswers(Collection $answers): array
    {
        return array_reduce($answers->toArray(), function ($carry, Answer $answer) {
            $carry[$answer->getText()] = $answer;
            return $carry;
        }, []);
    }

    private function processUserAnswers(array $userAnswers, array $mappedAnswers, $question, array &$tableRowData): void
    {
        $correctAnswers = $this->getCorrectAnswers($question->getAnswers());

        foreach ($userAnswers as $userConsoleAnswer) {
            $selectedAnswer = $mappedAnswers[$userConsoleAnswer] ?? null;
            if ($selectedAnswer) {
                $userAnswer = new UserQuestionAnswer();
                $userAnswer->setUserId(1); // User ID is hardcoded as we don't have auth
                $userAnswer->setQuestion($question);
                $userAnswer->setAnswer($selectedAnswer);
                $userAnswer->setIsCorrect($correctAnswers->contains($selectedAnswer));
                $this->entityManager->persist($userAnswer);

                $tableRowData[] = [
                    $question->getText(),
                    $selectedAnswer->getText(),
                    $correctAnswers->contains($selectedAnswer) ? 'YES' : 'NO',
                ];
            }
        }
        $this->entityManager->flush();
    }

    public function getCorrectAnswers(Collection $answers): Collection
    {
        return $answers->filter(fn(Answer $answer) => $answer->getIsCorrect());
    }
}
