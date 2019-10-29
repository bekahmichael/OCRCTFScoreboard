import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { WoModule } from '@app/wo-module/wo.module';
import { FormsModule } from '@angular/forms';
import { AdminNewUserComponent } from './admin-new-user/admin-new-user.component';
import { AdminAccessToTeamsComponent } from './admin-access-to-teams/admin-access-to-teams.component';
import { MessageBeforeSubmitQuizComponent } from './message-before-submit-quiz/message-before-submit-quiz.component';
import { QuizPausedComponent } from './quiz-paused/quiz-paused.component';
import { QuizFinishedComponent } from '@app/modal/quiz-finished/quiz-finished.component';
import { QuizSocketRefreshComponent } from '@app/modal/quiz-socket-refresh/quiz-socket-refresh.component';
import { QuizNotStartedComponent } from '@app/modal/quiz-not-started/quiz-not-started.component';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        WoModule,
    ],
    declarations: [
        AdminNewUserComponent,
        AdminAccessToTeamsComponent,
        MessageBeforeSubmitQuizComponent,
        QuizPausedComponent,
        QuizFinishedComponent,
        QuizSocketRefreshComponent,
        QuizNotStartedComponent,
    ],
    entryComponents: [
        AdminNewUserComponent,
        AdminAccessToTeamsComponent,
        MessageBeforeSubmitQuizComponent,
        QuizPausedComponent,
        QuizFinishedComponent,
        QuizSocketRefreshComponent,
        QuizNotStartedComponent,
    ],
    providers: [
    ],
})
export class ModalModule { }
