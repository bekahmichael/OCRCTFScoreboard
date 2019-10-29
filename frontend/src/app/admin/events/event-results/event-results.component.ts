import { Component, AfterViewInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '@app/core/meta-page.service';
import { ApiAdminService } from '@app/share/api-admin.service';
import { WoFlashService } from '@app/wo-module/wo-flash/wo-flash.service';
import { environment } from '@env/environment';
import { AuthService } from '@app/auth/auth.service';

@Component({
    templateUrl: 'event-results.component.html',
    styleUrls: ['event-results.component.css']
})
export class EventResultsComponent implements AfterViewInit {
    eventId: number;
    isLoading = false;
    isUpdate = false;

    teams;
    questions;
    showAll = false;
    linkReportByQuiz = '#';

    openTeams = [];

    hasPointChanges = false;

    constructor (
        private metaPage: MetaPageService,
        private router: Router,
        private activeRoute: ActivatedRoute,
        private breadcrumbs: WoBreadCrumbsService,
        private api: ApiAdminService,
        private woFlash: WoFlashService,
        private auth: AuthService,
    ) {}

    ngAfterViewInit() {
        this.metaPage.setTitle('Results');
        this.activeRoute.params.subscribe(params => {
            if (params['o']) {
                params['o'].split(',').forEach(item => {
                    this.openTeams.push(parseInt(item, 10));
                });
            }
        });

        this.activeRoute.parent.params.subscribe(parentParams => {
            this.eventId = parseInt(parentParams['id'], 10) || 0;

            if (this.eventId > 0) {
                setTimeout(() => {
                    this.linkReportByQuiz = environment.API_BASE_URL + '/admin/events/download-report?event_id=' + this.eventId + '&access-token=' + this.auth.getToken();
                    this.loadData();
                }, 0);
            }
        });
    }

    toActualUrl() {
        this.openTeams = [];

        this.teams.forEach(team => {
            if (team._show === true) {
                this.openTeams.push(team.id);
            }
        });

        this.router.navigate(['/admin/events', this.eventId, 'results', {o: this.openTeams}]);
    }

    loadData() {
        this.isLoading = true;
        this.api.send('events/results', {event_id: this.eventId}).then(res => {
            this.isLoading = false;
            this.isUpdate = false;
            this.hasPointChanges = false;

            res['teams'].forEach(team => {
                team.reportUrl = environment.API_BASE_URL + '/admin/events/download-team-report?event_id=' + this.eventId + '&team_id=' + team.id + '&access-token=' + this.auth.getToken()
            })

            this.teams = res['teams'];
            this.questions = res['questions'];

            this.teams.forEach(team => {
                if (this.openTeams.indexOf(team.id) !== -1) {
                    team._show = true;
                }
                this.calculateTeamScores(team);
            });
        });
    }

    doShowAll() {
        this.showAll = !this.showAll;
        this.teams.forEach(team => {
            team._show = this.showAll;
        });

        this.toActualUrl();
    }

    doShow(row) {
        row._show = !row._show;
        let hasOpened = false;
        let hasHidden = true;

        this.teams.forEach(team => {
            if (team._show === true) {
                hasOpened = true;
            }
            if (team._show !== true) {
                hasHidden = false;
            }
        });

        if (this.showAll === true && hasOpened === false) {
            this.showAll = false;
        } else if (this.showAll === false && hasHidden === true) {
            this.showAll = true;
        }

        this.toActualUrl();
    }

    isAnswered(team, question) {
        if (team['answers'] && team['answers'][question['id']]) {
            if (question.type === 3) {
                return team['answers'][question['id']]['answer_text'] !== '';
            } else {
                return team['answers'][question['id']]['answer_ids'].length > 0;
            }
        } else {
            return false;
        }
    }

    calculateTeamScores(team) {
        let scores = 0;

        this.questions.forEach(question => {
            if (team['answers'][question.id]) {
                if (team['answers'][question.id]['change_points_to'] > 0) {
                    scores += team['answers'][question.id]['change_points_to'];
                } else if (team['answers'][question.id]['is_correct'] === true) {
                    scores += question.points;
                }
            }
        });

        team['scores'] = scores;
    }

    isCheckedAnswer(answer, teamAnswers) {
        if (typeof teamAnswers === 'undefined') {
            return false;
        } else {
            if (teamAnswers.answer_ids.indexOf(answer.id) !== -1) {
                return true;
            }
            return false;
        }
    }

    doChangePointsTo(event, team, question_id, answer) {
        if (typeof answer !== 'undefined') {
            if (typeof answer['_change_points_to'] === 'undefined') {
                answer['_change_points_to'] = answer['change_points_to'];
            }
            if (this.isEmpty(event.target.value)) {
                answer['change_points_to'] = null;
            } else {
                answer['change_points_to'] = parseFloat(event.target.value);
            }

            answer['_hasChanges'] = true;
        } else {
            team['answers'][question_id] = {
                _change_points_to: null,
                answer_ids: [],
                _hasChanges: true,
                change_points_to: parseFloat(event.target.value),
            };
        }

        if (answer['change_points_to'] === answer['_change_points_to']) {
            team['answers'][question_id]['_hasChanges'] = false;
        } else if (this.isEmpty(answer['change_points_to']) && this.isEmpty(answer['_change_points_to'])) {
            team['answers'][question_id]['_hasChanges'] = false;
        }

        this.calculateTeamScores(team);

        this.hasPointChanges = false;
        this.teams.forEach(fteam => {
            Object.keys(fteam.answers).forEach((fquestion_id) => {
                if (fteam.answers[fquestion_id]['_hasChanges'] === true) {
                    this.hasPointChanges = true;
                }
            });
        });
    }

    isEmpty(val) {
        return (!val || 0 === val.length);
    }

    doSaveChanges() {
        const dataForm = [];

        this.teams.forEach(team => {
            Object.keys(team.answers).forEach(function(question_id) {
                dataForm.push({
                    team_id: team.id,
                    question_id: question_id,
                    change_points_to: team.answers[question_id]['change_points_to'],
                });
            });
        });

        this.api.send('events/results', {event_id: this.eventId}, {
            'data': dataForm,
            'cmd' : 'update_points'
        }).then(res => {
            setTimeout(() => {
                this.woFlash.addMessage('The operation was successfully done!');
                this.woFlash.show();
            }, 100);
            this.loadData();
        });
    }
}
