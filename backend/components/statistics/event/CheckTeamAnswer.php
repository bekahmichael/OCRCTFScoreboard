<?php

namespace app\components\statistics\event;

use yii\helpers\ArrayHelper;

/*
 * The class provide methods for check answer for quiz.
 */
class CheckTeamAnswer
{
    private $question;
    private $answer;

    /**
     * Constructor.
     *
     * @param array $question
     * @param array $answer
     */
    public function __construct($question, $answer)
    {
        $this->question = $question;
        $this->answer = $answer;
    }

    /**
     * Check the provided answer is correct.
     *
     * @return boolean
     */
    public function isCorrect()
    {
        $currect_answers = $this->getCurrectAnswers();

        if ($this->question['type'] === 0) {
            if (count($this->answer['answer_ids']) > 0) {
                return in_array($this->answer['answer_ids'][0], ArrayHelper::getColumn($currect_answers, 'id'));
            }
        }

        if ($this->question['type'] === 1) {
            if (count($this->answer['answer_ids']) > 0) {
                $has_incorrect = false;
                foreach ($currect_answers as $correct_answer) {
                    if (!in_array($correct_answer['id'], $this->answer['answer_ids'])) {
                        $has_incorrect = true;
                    }
                }

                return !$has_incorrect;
            }
        }

        if ($this->question['type'] === 2) {
            if (count($this->answer['answer_ids']) > 0) {
                return in_array($this->answer['answer_ids'][0], ArrayHelper::getColumn($currect_answers, 'id'));
            }
        }

        if ($this->question['type'] === 3) {
            if (array_key_exists('answers', $this->question) && is_array($this->question['answers'])) {
                foreach ($this->question['answers'] as $answer) {
                    if (trim($this->answer['answer_text']) === $answer['description']) {
                        return true;
                    }
                }
            }
        }

        return null;
    }

    private function getCurrectAnswers()
    {
        $out = [];

        if (array_key_exists('answers', $this->question) && is_array($this->question['answers'])) {
            foreach ($this->question['answers'] as $answer) {
                if ($answer['is_correct'] === 1) {
                    $out[] = $answer;
                }
            }
        }

        return $out;
    }
}