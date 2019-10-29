import {Component, HostBinding, OnDestroy, OnInit, Renderer2} from '@angular/core';
import {ActivatedRoute, Router} from '@angular/router';
import { environment } from '@env/environment';
import {HttpService} from '@app/core/http.service';
import {AuthService} from '@app/auth/auth.service';
import {WoDialogService} from '@app/wo-module/wo-dialog/wo-dialog.service';
import {MessageBeforeSubmitQuizComponent} from '@app/modal/message-before-submit-quiz/message-before-submit-quiz.component';
import {QuizPausedComponent} from '@app/modal/quiz-paused/quiz-paused.component';
import {QuizFinishedComponent} from '@app/modal/quiz-finished/quiz-finished.component';
import {QuizSocketRefreshComponent} from '@app/modal/quiz-socket-refresh/quiz-socket-refresh.component';
import {QuizNotStartedComponent} from '@app/modal/quiz-not-started/quiz-not-started.component';

const MAX_ATTEMPTS_TO_CONNECT = 300;

@Component({
    selector: 'app-quiz',
    templateUrl: './quiz.component.html',
    styleUrls: ['./quiz.component.css']
})
export class QuizComponent implements OnInit, OnDestroy {
    public title;
    sideBarToggleStorageKey = '__sideBarToggleStorageKey';

    @HostBinding('class.sidebar-compact') 'isCompactSidebar()';
    @HostBinding('class.no-logged') '!isLogged()';

    hash: string;
    environment;

    team = {};
    event = {};
    questions = [];
    data = {};

    loading = true;

    ws;

    wsMaxCountAttemptsConnect = MAX_ATTEMPTS_TO_CONNECT;

    statusEventQuiz;

    eventQuizStatuses = {
        'STATUS_NOT_STARTED':  0,
        'STATUS_ACTIVE':  1,
        'STATUS_PAUSED':  2,
        'STATUS_FINISHED':  3,
    };

    dialogPausedOn;
    dialogFinishedOn;
    dialogNotStarted;

    constructor(
        private render: Renderer2,
        private activeRoute: ActivatedRoute,
        private http: HttpService,
        private auth: AuthService,
        public router: Router,
        private dialog: WoDialogService,
    ) {
        this.title = environment.APP_NAME;
        this.environment = environment;
        this.render.addClass(document.body, 'quiz-module');
    }


    ngOnInit(): void {
        this.activeRoute.params.subscribe(params => {
            this.hash = params['hash'];
            this.getPage();
        });
    }

    getPage() {
        this.http.send(this.environment.API_BASE_URL + '/quiz/quiz', {
            'hash': this.hash,
        }).then(res => {
            if (res['code'] === 200) {
                this.team = res['team'];
                this.event = res['event'];
                if (res['questions']) {
                    this.setQuestions(res['questions']);
                }
                if (res['answers']) {
                    res['answers'].forEach((item) => {
                        this.setAnswersForQuestion(item);
                    });
                }
                this.checkStatusEventQuiz(res['status']);
                this.loading = false;
                this.initSocket();
            }
        });
    }

    logOut() {
        this.auth.logout();
        this.router.navigate(['/login']);
    }

    isCompactSidebar(): boolean {
        return !!parseInt(localStorage.getItem(this.sideBarToggleStorageKey), 10);
    }

    sidebarToggle () {
        localStorage.setItem(this.sideBarToggleStorageKey, this.isCompactSidebar() ? '0' : '1');
    }

    isLogged() {
        return !this.auth.isGuest;
    }

    ngOnDestroy () {
        this.render.removeClass(document.body, 'admin-module');
    }

    scroll(el: HTMLElement) {
        el.scrollIntoView();
    }

    sendQuizAnswers() {
        const dialogRef = this.dialog.open(MessageBeforeSubmitQuizComponent, {questions: this.questions});
        dialogRef.afterClosed().subscribe(result => {
            console.log(result);
        });
    }

    checkStatusEventQuiz(status) {
        this.statusEventQuiz = status;
        if (this.dialogPausedOn) {
            this.dialogPausedOn.close();
            this.dialogPausedOn = null;
        }
        if (this.statusEventQuiz === this.eventQuizStatuses.STATUS_PAUSED) {
            this.dialogPausedOn = this.dialog.open(QuizPausedComponent, {});
        }
        if (this.dialogFinishedOn) {
            this.dialogFinishedOn.close();
            this.dialogFinishedOn = null;
        }
        if (this.statusEventQuiz === this.eventQuizStatuses.STATUS_FINISHED) {
            this.dialogFinishedOn = this.dialog.open(QuizFinishedComponent, {});
        }
        if (this.dialogNotStarted) {
            this.dialogNotStarted.close();
            this.dialogNotStarted = null;
        }
        if (this.statusEventQuiz === this.eventQuizStatuses.STATUS_NOT_STARTED) {
            this.dialogNotStarted = this.dialog.open(QuizNotStartedComponent, {});
        }
    }

    setQuestions(questions) {
        let levels = [];
        questions.forEach(question => {
            levels.push(question.level);
        });

        let hasDiffrentLevels = levels.filter((x, i, a) => a.indexOf(x) == i).length > 1;

        questions.forEach(question => {
            question.showLevel = hasDiffrentLevels;
        });

        this.questions = questions;
    }

    setAnswersForQuestion(data) {
        this.questions.forEach((item) => {
            console.log(data);
            if (item.id == data.question_id) {
                console.log(data);
                let setSelectedQuestion = false;
                if (data.answer_ids.length > 0 || data.answer_text !== '') {
                    if (item.type === "0" || item.type === "2") {
                        setSelectedQuestion = data.answer_ids[0];
                    } else {
                        setSelectedQuestion = true;
                    }
                }
                if (data.answer_ids.length > 0) {
                    item.activeAnswers.forEach((answer) => {
                        if (data.answer_ids.indexOf(answer.id) >= 0) {
                            answer.is_selected = answer.id;
                        } else {
                            answer.is_selected = false;
                        }
                    });
                } else {
                    item.activeAnswers.forEach((answer) => {
                        answer.is_selected = false;
                    });
                }
                if (data.answer_text !== '' && item.type === "3") {
                    item.answer = data.answer_text;
                }
                item.is_selected = setSelectedQuestion;
            }
        });
    }

    initSocket() {
        this.ws = new WebSocket(this.environment.WS_URL + '/?hash=' + this.hash);
        this.ws.onopen = (event) => { this.socketOnOpen(event); };
        this.ws.onclose = (event) => { this.socketOnClose(event); };
        this.ws.onerror = (event) => { this.socketOnError(event); };
        this.ws.onmessage = (event) => { this.socketOnMessage(event); };
    }

    socketOnOpen(event) {
        console.log(event);
        console.log('WebSocket opened!');
        this.socketSendHash();
        this.wsMaxCountAttemptsConnect = MAX_ATTEMPTS_TO_CONNECT;
    }

    socketOnClose(event) {
        console.log('WebSocket closed!');
        // alert('Connection with server was disabled!');
        if (this.wsMaxCountAttemptsConnect > 0) {
            this.socketReconnect();
        } else {
            this.dialog.open(QuizSocketRefreshComponent, {});
        }
    }

    socketOnError(event) {
        console.log('WebSocket error:');
    }

    socketOnMessage(event) {
        const data = JSON.parse(event.data);
        if (data.questions) {
            this.setQuestions(data.questions);
        }
        if (data.answers) {
            data.answers.forEach((item) => {
                this.setAnswersForQuestion(item);
            });
        }
        if (data.status) {
            this.checkStatusEventQuiz(data.status);
        }
        if (data.type && data.type === 'set_answer') {
            this.setAnswersForQuestion(data);
        }
    }

    socketSend(data) {
        this.ws.send('test');
    }

    socketSendHash() {
        const data = {
            'type': 'set_hash',
            'hash': this.hash,
        };
        this.ws.send(JSON.stringify(data));
    }

    socketReconnect() {
        this.wsMaxCountAttemptsConnect--;
        setTimeout(() => { this.initSocket(); }, 5000);
    }
}
