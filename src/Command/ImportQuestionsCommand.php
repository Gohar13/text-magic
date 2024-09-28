<?php

namespace App\Command;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Test;
use App\Factories\TestDTOFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportQuestionsCommand extends Command
{
    protected static $defaultName = 'app:import-questions';

    private EntityManagerInterface $entityManager;
    private TestDTOFactory $testDTOFactory;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        TestDTOFactory         $testDTOFactory,
        ValidatorInterface     $validator
    ){
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->testDTOFactory = $testDTOFactory;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Imports questions and answers from a JSON file.')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            $output->writeln("<error>File not found: $filePath</error>");
            return Command::FAILURE;
        }

        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);

        $testDTO = $this->testDTOFactory->create($data);

        $errors = $this->validator->validate($testDTO);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $output->writeln($error->getMessage());
                return Command::FAILURE;
            }
        }

        $test = new Test();
        $test->setName($testDTO->getTest());
        $this->entityManager->persist($test);

        foreach ($testDTO->getQuestions() as $questionDTO) {
            $question = new Question();
            $question->setText($questionDTO->getText());
            $question->setTest($test);
            $this->entityManager->persist($question);

            foreach ($questionDTO->getAnswers() as $answerDTO) {
                $answer = new Answer();
                $answer->setText($answerDTO->getText());
                $answer->setIsCorrect($answerDTO->isCorrect());
                $answer->setQuestion($question);
                $this->entityManager->persist($answer);
            }
        }

        $this->entityManager->flush();
        $output->writeln("<info>Questions and answers imported successfully!</info>");

        return Command::SUCCESS;
    }
}
