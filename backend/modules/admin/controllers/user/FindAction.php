<?php

namespace app\modules\admin\controllers\user;

use Yii;
use yii\base\Action;
use app\models\Users;
use yii\data\Pagination;

/**
 * @inheritdoc
 */
class FindAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $page      = Yii::$app->request->post('page', 1) - 1;
        $sort      = Yii::$app->request->post('sort', '');
        $search    = Yii::$app->request->post('common', '');
        $role      = Yii::$app->request->post('role', '');
        $condition = Yii::$app->request->post('condition', []);

        $query = Users::find();

        $query->select([
            '`users`.*',
            '`auth_assignment`.`item_name` as `role`',
            'CONCAT(`users`.`first_name`, " ", `users`.`last_name`) as `name`',
        ]);

        $query->leftJoin('auth_assignment', 'users.id = auth_assignment.user_id');

        if (!empty($search)) {
            $query
                ->where(['like','email',$search])
                ->orWhere(['like','first_name',$search])
                ->orWhere(['like','last_name',$search]);
        }

        if ($role !== '') {
            $query->andWhere(['auth_assignment.item_name' => $role]);
        }

        if (!empty($sort)) {
            $query->orderBy([
                ltrim($sort, '-') => $sort{0} == '-' ? SORT_DESC : SORT_ASC
            ]);
        }
        if (!empty($condition['username'])) {
            $query->andWhere(['like', 'username', $condition['username']]);
        }
        if (!empty($condition['email'])) {
            $query->andWhere(['like', 'email', $condition['email']]);
        }
        if (!empty($condition['name'])) {
            $query->andWhere(['like', 'CONCAT(`users`.`first_name`, " ", `users`.`last_name`)', $condition['name']]);
        }

        if (!empty($condition['status'])) {
            $query->andWhere(['status' => $condition['status']]);
        } else {
            $query->andWhere(['!=', 'status', 'deleted']);
        }

        // Cdate range
        if (!empty($condition['cdate_from']) && !empty($condition['cdate_to'])) {
            $cdate_from = \DateTime::createFromFormat('m/d/Y', $condition['cdate_from']);
            $cdate_to = \DateTime::createFromFormat('m/d/Y', $condition['cdate_to']);
            if ($cdate_from && $cdate_to) {
                $query->andWhere(['between', 'users.created_at', $cdate_from->format('Y-m-d 00:00:00'), $cdate_to->format('Y-m-d 23:59:59')]);
            }
        } else if (!empty($condition['cdate_from'])) {
            $cdate_from = \DateTime::createFromFormat('m/d/Y', $condition['cdate_from']);
            if ($cdate_from) {
                $query->andWhere(['>', 'users.created_at', $cdate_from->format('Y-m-d 00:00:00')]);
            }
        } else if (!empty($condition['cdate_to'])) {
            $cdate_to = \DateTime::createFromFormat('m/d/Y', $condition['cdate_to']);
            if ($cdate_to) {
                $query->andWhere(['<', 'users.created_at', $cdate_to->format('Y-m-d 23:59:59')]);
            }
        }

        $countQuery = clone $query;
        $totalCount = $countQuery->count();
        $pages = new Pagination(['totalCount' => $totalCount]);
        $pages->defaultPageSize = 60;
        $pages->setPage($page);

        $rows = $query->offset($pages->offset)->limit($pages->limit)->asArray()->all();

        foreach ($rows as &$row) {
            $row['created_at'] = date('c', strtotime($row['created_at']));
        }

        return [
            'rows'      => $rows,
            'curr_page' => $page + 1,
            'last_page' => ceil($pages->totalCount / $pages->defaultPageSize),
        ];
    }
}
