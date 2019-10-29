import {Component, Input, OnInit} from '@angular/core';
import {environment} from '@env/environment';

@Component({
    selector: 'quiz-question',
    templateUrl: './question.component.html',
    styleUrls: ['./question.component.css']
})
export class QuestionComponent implements OnInit {
    @Input() index;
    @Input() event;
    @Input() question: {
        'id': any,
        'title': '',
        'description': '',
        'file_id': '',
        'type': any,
        'activeAnswers': [],
        'answer': '',
        'points': 0,
        'level': 1,
        'is_selected': false,
        'showLevel': false,
        'first_of_level': false
    };
    @Input() ws;
    environment;

    TYPES = [
        'Multiple choice',
        'Checkboxes',
        'Dropdown',
        'Open answer',
    ];

    selectedAnswers: {};

    constructor() {
        this.environment = environment;
    }

    ngOnInit() {
    }

    getImageUrl(id) {
        if (id) {
            const url = new URL(this.environment.API_BASE_URL + '/quiz/files/image?r=250x250', window.location.origin);
            url.searchParams.set('id', id);
            return url.href;
        }
        return '';
    }

    getFullImageUrl(id) {
        if (id) {
            const url = new URL(this.environment.API_BASE_URL + '/quiz/files/image?r=1000x1000', window.location.origin);
            url.searchParams.set('id', id);
            return url.href;
        }
        return null;
    }

    changeSelectedTextarea(event: Event, question) {
        question.is_selected = event.target['value'].trim() !== '';
        this.sendAnswerToServer(question.id, 0, event.target['value'].trim());
    }

    changeSelectedCheckbox(event, question, answer) {
        let selected = false;
        const selectedAnswerIds = [];
        question.activeAnswers.forEach((item) => {
            if (item.is_selected) {
               selected = true;
                selectedAnswerIds.push(item.id);
            }
        });
        question.is_selected = selected;
        this.sendAnswerToServer(question.id, selectedAnswerIds, '');
    }

    changeSelectedRadio(event, question) {
        this.sendAnswerToServer(question.id, [question.is_selected], '');
    }

    changeSelectedList(event, question) {
        this.sendAnswerToServer(question.id, [question.is_selected], '');
    }

    sendAnswerToServer(questionId, answerIds, answerText) {
        const data = {
            'type': 'set_answer',
            'question_id': questionId,
            'answer_ids': answerIds,
            'answer_text': answerText,
        };
        this.ws.send(JSON.stringify(data));
    }
}
