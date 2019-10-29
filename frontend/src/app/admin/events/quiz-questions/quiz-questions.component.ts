import {Component, OnInit} from '@angular/core';
import {Router, ActivatedRoute} from '@angular/router';
import {WoBreadCrumbsService} from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import {MetaPageService} from '@app/core/meta-page.service';
import {ApiAdminService} from '@app/share/api-admin.service';
import {WoFlashService} from '@app/wo-module/wo-flash/wo-flash.service';
import {WoDialogConfirmComponent} from '@app/wo-module/wo-dialog/wo-dialog-confirm/wo-dialog-confirm.component';
import {WoDialogService} from '@app/wo-module/wo-dialog/wo-dialog.service';
import { ScrollToService } from 'ng2-scroll-to-el';

@Component({
    templateUrl: 'quiz-questions.component.html',
    styleUrls: ['quiz-questions.component.css'],
})
export class QuizQuestionsComponent implements OnInit {

    loading = true;
    id = 0;
    quiz = [];
    questions = [];
    questionsBeforeChanges = [];
    newQuestion = {
        'show_description': 0,
        'description': null,
        'type': 0,
        'points': 0,
        'level': 1,
        'hover': false,
        'show': true,
        'add_to_library' : false
    };
    _newQuestionShow = false;
    _newQuestionFromLibraryShow = false;
    newQuestionError = {};
    criteria = {
        defSort: '',
        sort: '',
    };

    STATUS_LABELS = [
        'Active',
        'Blocked',
        'Deleted',
    ];

    TYPES = [
        'Multiple choice',
        'Checkboxes',
        'Dropdown',
        'Open answer',
    ];

    get newQuestionShow(): boolean {
        return this._newQuestionShow;
    }
    set newQuestionShow(value: boolean) {
        this._newQuestionShow = value;
        this._newQuestionFromLibraryShow = value ? false :  this._newQuestionFromLibraryShow;
    }
    get newQuestionFromLibraryShow(): boolean {
        return this._newQuestionFromLibraryShow;
    }
    set newQuestionFromLibraryShow(value: boolean) {
        this._newQuestionFromLibraryShow = value;
        this._newQuestionShow  = value ? false :  this._newQuestionShow;
    }

    get uploadImageSrc() : string {
        return this.api.baseUrl + '/files/image?r=150x150&type=adaptive';
    }

    constructor(
        private router: Router,
        private activeRoute: ActivatedRoute,
        private breadcrumbs: WoBreadCrumbsService,
        private metaPage: MetaPageService,
        public api: ApiAdminService,
        private woFlash: WoFlashService,
        private dialog: WoDialogService,
        private scrollService: ScrollToService,
    ) {
        this.metaPage.setTitle('Questions');
    }

    ngOnInit() {
        this.activeRoute.parent.params.subscribe(parentParams => {
            this.id = parseInt(parentParams['id'], 10) || 0;
            this.getQuiz();
        });
    }

    getQuiz() {
        this.api.send('events/event-quizzes', {'event_id': this.id}).then(res => {
            this.quiz = res['row'];
            if (this.quiz !== null) {
                this.getQuestions();
            } else {
                this.loading = false;
            }
        });
    }

    getQuestions() {
        this.api.send('quizzes/quiz-questions', {'quiz_id': this.quiz['id']}).then(res => {
            this.questions = res['rows'];
            this.questionsBeforeChanges = JSON.parse(JSON.stringify(this.questions));
            this.questions.forEach(function(el) {
                el['hover'] = false;
                el['need_update'] = false;
                el['show'] = true;
                el['answers'] = [];
            });
            this.loading = false;
        });
    }

    addQuestion() {
        this.woFlash.hide();
        this.newQuestion['quiz_id'] = this.quiz['id'];
        this.api.send('quizzes/question', {'id': 0}, this.newQuestion).then(res => {
            if (res['errors']) {
                setTimeout(() => {
                    this.newQuestionError = res['errors'];
                    this.woFlash.addError(res['errors']);
                    this.woFlash.show('newQuestion');
                }, 100);
            } else {
                this.newQuestionShow = false;
                this.newQuestionError = {};
                this.woFlash.addMessage('The operation was successfully done!');
                this.woFlash.show();
                res['data']['show'] = true;
                res['data']['need_update'] = false;
                res['data']['answers'] = [];
                this.questions.push(res['data']);
                this.newQuestion = {        
                    'show_description': 0,
                    'description': null,
                    'type': 0,
                    'points': 0,
                    'level': 1,
                    'hover': false,
                    'show': true,
                    'add_to_library' : false,
                };
            }
        });
    }

    doUpdate(question) {
        this.woFlash.hide();
        this.api.send('questions/item', {'id': question['id']}, question).then(res => {
            if (res['errors']) {
                setTimeout(() => {
                    question['errors'] = res['errors'];
                    this.woFlash.addError(res['errors']);
                    this.woFlash.show();
                }, 100);
            } else {
                question['errors'] = {};
                this.woFlash.addMessage('The operation was successfully done!');
                this.woFlash.show();
            }
        });
    }

    doUpdateAll() {
        this.woFlash.hide();
        this.api.send('questions/update-list', {'quiz_id': this.quiz['id']}, this.questions).then(res => {
            this.questions.forEach(function(question) {
                question['errors'] = {};
                question['answers'].forEach(function (answer) {
                    answer['errors'] = {};
                });
            });
            let questionsWithError = [];
            if (res['errors']) {
                res['errors'].forEach((error) => {
                    if (questionsWithError.indexOf(error['question_key']) == -1 ) {
                        questionsWithError.push(error['question_key']);
                        this.woFlash.addError('Question #' + (error['question_key'] + 1));
                    }
                    if (error['answer_key'] !== undefined) {
                        this.questions[error['question_key']]['answers'][error['answer_key']]['errors'] = error['answer_errors'];
                        this.woFlash.addError(error['answer_errors']);
                    } else {
                        this.questions[error['question_key']]['errors'] = error['question_errors'];
                        this.woFlash.addError(error['question_errors']);
                    }
                });
                this.woFlash.show();

                const element = document.getElementById('question_' + questionsWithError[0]);
                this.scrollService.scrollTo(element);
            } else {
                this.woFlash.addMessage('The operation was successfully done!');
                this.woFlash.show();
                this.ngOnInit();
            }
            this.questions.forEach((question, key) => {
                if (questionsWithError.indexOf(key) == -1 ) {
                    question['need_update'] = false;
                }
            });
        });
    }

    doDeleteQuestion(i) {
        const dialogRef = this.dialog.open(WoDialogConfirmComponent, {message: 'Are you sure you want to delete question?'});
        dialogRef.afterClosed().subscribe(result => {
            if (result === true) {
                this.api.send('questions/delete', {id: this.questions[i]['id']}, {}).then(res => {
                    //this.questions.splice(i, 1);
                    this.questions = [...this.questions.slice(0, i), ...this.questions.slice(i + 1)];
                });
            }
        });
    }

    onImageChanged = (event, question, key) => {
        if (event.result && event.result.file.id) {
            question['file_id'] = event.result.file.id;
        } else if (event.remove === true) {
            question['file_id'] = null;
        }
        if (key !== null) {
            this.checkQuestionNeedUpdate(key);
        }
    }

    checkPoints(question, key) {
        question['points'] = Math.round(question['points'] * 10) / 10;
        if (key !== null) {
            this.checkQuestionNeedUpdate(key);
        }
    }

    checkLevel(question, key) {
        question['level'] = parseInt(question['level'], 10);
        if (key !== null) {
            this.checkQuestionNeedUpdate(key);
        }
    }

    setQuestionAnswers(answers, question) {
        question['answers'] = answers;
    }

    checkQuestionNeedUpdate(key) {
        if (
            this.questionsBeforeChanges[key]['title'] != this.questions[key]['title'] ||
            this.questionsBeforeChanges[key]['show_description'] != this.questions[key]['show_description'] ||
            this.questionsBeforeChanges[key]['description'] != this.questions[key]['description'] ||
            this.questionsBeforeChanges[key]['type'] != this.questions[key]['type'] ||
            this.questionsBeforeChanges[key]['points'] != this.questions[key]['points'] ||
            this.questionsBeforeChanges[key]['level'] != this.questions[key]['level'] ||
            this.questionsBeforeChanges[key]['sequence'] != this.questions[key]['sequence']
        ) {
            this.setQuestionNeedUpdate(true, this.questions[key], false);
        } else {
            this.setQuestionNeedUpdate(false, this.questions[key], false);
        }
    }

    setQuestionNeedUpdate(needUpdate, question, answersModified) {
        question['need_update'] = needUpdate;
        if (answersModified === true) {
            question['need_update_by_answers'] = needUpdate;
        }
        if (answersModified === false) {
            question['need_update_by_question'] = needUpdate;
        }
        if (question['need_update_by_answers'] === true || question['need_update_by_question'] === true) {
            question['need_update'] = true;
        }
    }

    moveDown(key) {
        let q = this.questions[key];
        const sequenceTemp = this.questions[key + 1]['sequence'];
        this.questions[key + 1]['sequence'] = q['sequence'];
        this.questions[key] = this.questions[key + 1];
        q['sequence'] = sequenceTemp;
        this.questions[key + 1] = q;
        this.checkQuestionNeedUpdate(key);
        this.checkQuestionNeedUpdate(key + 1);
    }

    moveUp(key) {
        let q = this.questions[key];
        const sequenceTemp = this.questions[key - 1]['sequence'];
        this.questions[key - 1]['sequence'] = q['sequence'];
        this.questions[key] = this.questions[key - 1];
        q['sequence'] = sequenceTemp;
        this.questions[key - 1] = q;
        this.checkQuestionNeedUpdate(key - 1);
        this.checkQuestionNeedUpdate(key);
    }

    addQuestionFromLibrary(data) {
        const q = data['question'];
        q['quiz_id'] = this.quiz['id'];
        q['library_question_id'] = data['question']['id'];
        q['is_library_question'] = 0;
        q['id'] = 0;
        q['answers'] = data['answers'];
        this.api.send('quizzes/question', {'id': 0}, q).then(res => {
            if (res['errors']) {
                setTimeout(() => {
                    this.woFlash.addError(res['errors']);
                    this.woFlash.show();
                }, 100);
            } else {
                this.woFlash.addMessage('The operation was successfully done!');
                this.woFlash.show();
                res['data']['show'] = true;
                res['data']['need_update'] = false;
                res['data']['answers'] = [];
                const currentScrollTop = document.documentElement.scrollTop;
                const currentScrollHeight = document.documentElement.scrollHeight;
                this.questions = [...this.questions, res['data']];
                setTimeout(() => {
                    window.scrollTo(0, document.documentElement.scrollHeight - currentScrollHeight + currentScrollTop);
                }, 100);
            }
        });
    }
}
