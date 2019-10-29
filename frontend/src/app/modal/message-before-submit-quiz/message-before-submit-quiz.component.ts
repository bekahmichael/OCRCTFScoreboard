import {Component, Input, AfterViewInit} from '@angular/core';
import {IWoDialogComponent} from '../../wo-module/wo-dialog/wo-dialog.interface';

@Component({
    selector: 'app-message-before-submit-quiz',
    templateUrl: './message-before-submit-quiz.component.html',
    styleUrls: ['./message-before-submit-quiz.component.css']
})
export class MessageBeforeSubmitQuizComponent implements AfterViewInit, IWoDialogComponent {
    @Input() dialog: any;
    questions: [];
    teams = [];
    unselectedQuestionsIndex = [];

    constructor() {
    }

    ngAfterViewInit() {
        this.dialog.options.css = {
            width: '500px',
        };
        this.questions = this.dialog.options.questions;
        this.questions.forEach((item, index) => {
            if (!item['is_selected']) {
                this.unselectedQuestionsIndex.push(index + 1);
            }
        });
    }

    close() {
        this.dialog.close(false);
    }

    doSend() {
        this.dialog.close(true);
    }
}
