<?php

use app\models\AnswersQuiz;
use app\models\EventQuizzes;
use app\models\Events;
use app\models\EventTeams;
use app\models\Questions;
use app\models\Quizzes;
use Workerman\Worker;

require(__DIR__ . '/../vendor/autoload.php');

// Environment
require(__DIR__ . '/../env.php');

require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/console.php');
new yii\console\Application($config);



$events = [];
$users = [];

$context = array(
    'ssl' => array(
        'local_cert' => env('LOCAL_CERT_PATH'),
        'local_pk'   => env('LOCAL_PK_PATH'),
        'verify_peer' => false,
    )
);

if(env('HTTPS') == true) {
    $ws_worker            = new Worker("websocket://0.0.0.0:".env('WS_PORT',8011), $context);
    $ws_worker->transport = 'ssl';
} else {
    $ws_worker            = new Worker("websocket://0.0.0.0:".env('WS_PORT',8011));
}
$ws_worker->onWorkerStart = function() use (&$users, &$events)
{
    $inner_tcp_worker = new Worker("tcp://127.0.0.1:".env('LOCAL_TCP_PORT',1234));
    $inner_tcp_worker->onMessage = function($connection, $data) use (&$users, &$events) {
        $data = json_decode($data, true);
        try {
            if(isset($events[$data['event_id']])) {
                $questions = [];
                if($data['status'] === EventQuizzes::STATUS_ACTIVE) {
                    $questions = Questions::find()->joinWith(['quiz2questions'])->where(['quiz_id' => $data['quiz_id']])
                        ->with('activeAnswers')->getActive()->asArray()->all();
                }
                foreach ($events[$data['event_id']] as $team) {
                    $answers = null;
                    /** @var \Workerman\Connection\TcpConnection $wsConnection */
                    foreach ($team as $wsConnection) {
                        if($answers === null && $data['status'] === EventQuizzes::STATUS_ACTIVE) {
                            $answers = AnswersQuiz::find()->onCondition([
                                'event_id'    => $data['event_id'],
                                'quiz_id'     => $data['quiz_id'],
                                'team_id'     => $wsConnection->team_id
                            ])->asArray()->all();
                        }
                        $message = [
                            'status' => $data['status'],
                            'questions' => $questions,
                            'answers' => $answers === null ? [] : $answers,
                        ];
                        $wsConnection->send(json_encode($message));
                    }
                }
            }
        } catch (\Exception $e) {
            Worker::log($e->getMessage());
        }
    };
    $inner_tcp_worker->listen();
};

$ws_worker->onConnect = function($connection) use (&$users, &$events)
{
    $connection->onWebSocketConnect = function($connection) use (&$users, &$events)
    {
        $hash = isset($_GET['hash']) ? $_GET['hash'] : false;
        if($hash) {
            $connection->hash = $hash;
            Worker::log($connection->hash);
            list($teamId, $accessKey) = explode(':', $hash);
            /** @var EventTeams $e2t */
            $e2t = EventTeams::find()->where(['team_id' => $teamId, 'access_key' => $accessKey])->one();
            if ($e2t) {
                $connection->team_id = $teamId;
                $connection->event_id = $e2t->event_id;
                /** @var Events $event */
                $event = $e2t->getEvent()->one();
                /** @var Quizzes $quiz */
                $quiz  = $event->getQuizzes()->one();
                $connection->quiz_id = $quiz->id;

                if(!isset($events[$e2t->event_id])) {
                    $events[$e2t->event_id] = [];
                }
                if (isset($events[$e2t->event_id][$hash])) {
                    $events[$e2t->event_id][$hash][] = $connection;
                } else {
                    $events[$e2t->event_id][$hash][0] = $connection;
                }

                if (isset($users[$hash])) {
                    $users[$hash][] = $connection;
                } else {
                    $users[$hash][0] = $connection;
                }
            }
        }
    };
};

/**
 * @var \Workerman\Connection\TcpConnection $connection
 * @var mixed $data
 */
$ws_worker->onMessage = function($connection, $data) use (&$users, &$events) {
    Worker::log(date("H:i:s"));
    $dataFromUser = json_decode($data, true);
    if($dataFromUser['type'] === 'set_hash') {
        $hash = isset($dataFromUser['hash']) ? $dataFromUser['hash'] : false;
        if($hash) {
            $connection->hash = $hash;
            Worker::log($connection->hash);
            list($teamId, $accessKey) = explode(':', $hash);
            /** @var EventTeams $e2t */
            $e2t = EventTeams::find()->where(['team_id' => $teamId, 'access_key' => $accessKey])->one();
            if ($e2t) {
                $connection->team_id = $teamId;
                $connection->event_id = $e2t->event_id;
                /** @var Events $event */
                $event = $e2t->getEvent()->one();
                /** @var Quizzes $quiz */
                $quiz  = $event->getQuizzes()->one();
                $connection->quiz_id = $quiz->id;

                if(!isset($events[$e2t->event_id])) {
                    $events[$e2t->event_id] = [];
                }
                if (isset($events[$e2t->event_id][$hash])) {
                    $events[$e2t->event_id][$hash][] = $connection;
                } else {
                    $events[$e2t->event_id][$hash][0] = $connection;
                }

                if (isset($users[$hash])) {
                    $users[$hash][] = $connection;
                } else {
                    $users[$hash][0] = $connection;
                }
            }
        }
    } elseif($connection->hash) {
        if (isset($users[$connection->hash])) {
            $answer = AnswersQuiz::findOne([
                'event_id'    => $connection->event_id,
                'quiz_id'     => $connection->quiz_id,
                'team_id'     => $connection->team_id,
                'question_id' => $dataFromUser['question_id'],
            ]);
            if ($answer) {
                $answer->answer_ids  = $dataFromUser['answer_ids'] ? implode(',', $dataFromUser['answer_ids']) : '';
                $answer->answer_text = $dataFromUser['answer_text'];
            } else {
                $answer              = new AnswersQuiz();
                $answer->event_id    = $connection->event_id;
                $answer->quiz_id     = $connection->quiz_id;
                $answer->team_id     = $connection->team_id;
                $answer->question_id = $dataFromUser['question_id'];
                $answer->answer_ids  = $dataFromUser['answer_ids'] ? implode(',', $dataFromUser['answer_ids']) : '';
                $answer->answer_text = $dataFromUser['answer_text'];
            }
            $answer->save();
            /** @var \Workerman\Connection\TcpConnection $wsConnection */
            foreach ($users[$connection->hash] as $wsConnection) {
                if ($wsConnection != $connection) {
                    $wsConnection->send($data);
                }
            }
        }
    }
};

$ws_worker->onClose = function($connection) use(&$users)
{
    foreach($users as $userId => $user) {
        $connectionNum = array_search($connection, $user);
        if($connectionNum !== false) {
            unset($user[$connectionNum]);
            $users[$userId] = array_values($user);
            if(count($users[$userId]) == 0) {
                unset($users[$userId]);
                break;
            }
        }

    }
};
// Run worker
Worker::runAll();