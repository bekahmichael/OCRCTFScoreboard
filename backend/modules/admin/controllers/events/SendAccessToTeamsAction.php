<?php

namespace app\modules\admin\controllers\events;

use Yii;
use yii\base\Action;
use app\models\Teams;
use app\models\EventTeams;
use app\models\TeamUsers;
use app\models\Events;

/**
 * @inheritdoc
 */
class SendAccessToTeamsAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($event_id)
    {
        if (Yii::$app->request->isPost) {
            $emails = [];
            $data = Yii::$app->request->post();
            $event = Events::findOne($event_id);
            $event_team = EventTeams::findOne(['event_id' => $event_id, 'team_id' => $data['team_id']]);

            $users = TeamUsers::find()
                ->where(['team_id' => $data['team_id']])
                ->andWhere(['status' => TeamUsers::STATUS_ACTIVE])
                ->with('user')
                ->asArray()
                ->all();

            foreach ($users as $user) {
                if ($user['user']['status'] === 'active') {
                    $emails[] = $user['user']['email'];
                }
            }

            if (count($emails) > 0) {
                $host_info = parse_url(Yii::$app->getUrlManager()->getHostInfo());
                Yii::$app->mailer->compose('general/access-to-event', [
                    'link'       => $data['accessLink'],
                    'login_link' => $data['loginLink'],
                    'event'      => $event,
                    'event_team' => $event_team,
                ])
                ->setFrom('no-reply@' . $host_info['host'])
                ->setTo($emails)
                ->setSubject('Cincinnati - Access to ' . $event->name)
                ->send();
            }

            return [
                'code'     => 200,
                'data'     => $data,
                'event_id' => $event_id,
                'users'    => $users,
                'emails'   => $emails,
            ];
        } else if (Yii::$app->request->isGet) {
            return [
                'teams' => $this->getAssignedTeams($event_id),
            ];
        } else {
            return [];
        }
    }

    private function getAssignedTeams($event_id)
    {
        $query = Teams::find()
                    ->select([
                        'id'             => 'teams.id',
                        'name'           => 'teams.name',
                        'event_teams_id' => 'event_teams.id',
                        'access_key'     => 'event_teams.access_key',
                        'pin'            => 'event_teams.pin',
                    ])
                    ->asArray();

        $query->leftJoin(
            'event_teams',
            implode(' AND ', [
                'event_teams.team_id = teams.id',
                'event_teams.event_id = ' . $event_id,
                'event_teams.status = ' . EventTeams::STATUS_ACTIVE,
            ])
        );

        $query->andWhere(['not', ['event_teams.id' => null]]);
        $query->orderBy(['teams.name' => SORT_ASC]);

        return $query->asArray()->all();
    }
}