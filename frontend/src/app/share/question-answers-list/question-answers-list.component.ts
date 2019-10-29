import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {ApiAdminService} from '@app/share/api-admin.service';
import {WoFlashService} from '@app/wo-module/wo-flash/wo-flash.service';
import {WoDialogConfirmComponent} from '@app/wo-module/wo-dialog/wo-dialog-confirm/wo-dialog-confirm.component';
import {WoDialogService} from '@app/wo-module/wo-dialog/wo-dialog.service';

@Component({
    selector: 'question-answers',
    templateUrl: 'question-answers-list.component.html',
    styleUrls: ['question-answers-list.component.css'],
})
export class QuestionAnswersListComponent implements OnInit {
    @Input() questionId: number;
    @Input() questionType: number;
    @Input() answers = [];
    @Output() answersChanged = new EventEmitter();
    @Output() questionNeedUpdate = new EventEmitter();

    answersBeforeChanges = [];
    newAnswer = {is_correct: 0};
    newAnswerShow = false;
    newAnswerError = {};
    criteria = {
        defSort: '',
        sort: '',
    };
    loadingAnswers = true;
    oldAnswersData = [];

    STATUS_LABELS = [
        'Active',
        'Blocked',
        'Deleted'
    ];

    constructor(
        private woFlash: WoFlashService,
        public api: ApiAdminService,
        private dialog: WoDialogService,
    ) {
    }

    ngOnInit() {
        this.getAnswers();
    }

    getAnswers() {
        this.api.send('questions/answers', {
            question_id: this.questionId,
            sort: this.criteria.sort,
            defSort: this.criteria.defSort
        }).then(res => {
            if (res['data'] !== null) {
                this.answers = res['rows'];
                this.answersBeforeChanges = JSON.parse(JSON.stringify(this.answers));
                this.answersChanged.emit(this.answers);
            }
            this.loadingAnswers = false;
        });
    }

    addAnswer() {
        this.woFlash.hide();
        this.newAnswer['question_id'] = this.questionId;
        console.log(this.newAnswer);
        this.api.send('questions/answer', {id: 0}, this.newAnswer).then(res => {
            if (res['errors']) {
                setTimeout(() => {
                    this.newAnswerError = res['errors'];
                    this.woFlash.addError(res['errors']);
                    this.woFlash.show('question' + this.questionId);
                }, 100);
            } else {
                this.newAnswerShow = false;
                this.newAnswerError = {};
                this.woFlash.addMessage('The operation was successfully done!');
                this.woFlash.show('question' + this.questionId);
                this.getAnswers();
                this.newAnswer = {is_correct: 0};
            }
        });
    }

    doEditAnswer(answerKey) {
        this.oldAnswersData[answerKey] = Object.assign({}, this.answers[answerKey]);
        this.answers[answerKey]['edit'] = 1;
        this.answersChanged.emit(this.answers);
    }

    doDeleteAnswer(row) {
        const dialogRef = this.dialog.open(WoDialogConfirmComponent, {message: 'Are you sure you want to delete answer?'});
        dialogRef.afterClosed().subscribe(result => {
            if (result === true) {
                this.api.send('questions/delete-answer', {id: row.value['id']}, {}).then(res => {
                    this.answers.splice(row.key, 1);
                    this.answersChanged.emit(this.answers);
                });
            }
        });
    }

    doUpdateAnswer(row) {
        this.woFlash.hide();
        this.api.send('questions/answer', {id: row.value['id']}, row.value).then(res => {
            if (res['errors']) {
                setTimeout(() => {
                    row.value['errors'] = res['errors'];
                    this.woFlash.addError(res['errors']);
                    this.woFlash.show('question' + this.questionId);
                }, 100);
            } else {
                this.answers[row.key]['edit'] = 0;
                this.answersChanged.emit(this.answers);
                this.woFlash.addMessage('The operation was successfully done!');
                this.woFlash.show('question' + this.questionId);
            }
        });
    }

    doCancelAnswer(answerKey) {
        this.answers[answerKey] = Object.assign({}, this.oldAnswersData[answerKey]);
        this.answers[answerKey]['edit'] = 0;
        this.answersChanged.emit(this.answers);
        this.oldAnswersData[answerKey] = {};
    }

    sortByAnswer(event) {
        this.criteria.sort = event.current;
        this.getAnswers();
    }

    onImageChanged = (event, answer) => {
        if (event.result && event.result.file.id) {
            answer['file_id'] = event.result.file.id;
        } else if (event.remove === true) {
            answer['file_id'] = null;
        }
        this.setQuestionNeedUpdate();
    }

    setQuestionNeedUpdate() {
        if (JSON.stringify(this.answersBeforeChanges) == JSON.stringify(this.answers)) {
            this.questionNeedUpdate.emit(false);
        } else {
            this.questionNeedUpdate.emit(true);
        }
    }

    moveDown(key) {
        let a = this.answers[key];
        const sequenceTemp = this.answers[key + 1]['sequence'];
        this.answers[key + 1]['sequence'] = a['sequence'];
        this.answers[key] = this.answers[key + 1];
        a['sequence'] = sequenceTemp;
        this.answers[key + 1] = a;
        this.setQuestionNeedUpdate();
    }

    moveUp(key) {
        let a = this.answers[key];
        const sequenceTemp = this.answers[key - 1]['sequence'];
        this.answers[key - 1]['sequence'] = a['sequence'];
        this.answers[key] = this.answers[key - 1];
        a['sequence'] = sequenceTemp;
        this.answers[key - 1] = a;
        this.setQuestionNeedUpdate();
    }
}
