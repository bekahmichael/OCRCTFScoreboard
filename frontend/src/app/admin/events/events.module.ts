import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { WoModule } from '@app/wo-module/wo.module';
import { EventsRoutingModule } from './events-routing.module';
import { EventsListComponent } from './events-list/events-list.component';
import { EventTabsComponent } from './event-tabs/event-tabs.component';
import { EventProfileComponent } from './event-profile/event-profile.component';
import { EventTeamsComponent } from './event-teams/event-teams.component';
import { EventScoreboardComponent } from './event-scoreboard/event-scoreboard.component';
import { ShareModule } from '@app/share/share.module';
import { QuizQuestionsComponent } from './quiz-questions/quiz-questions.component';
import { QuizQuestionFromLibraryComponent } from './quiz-question-from-library/quiz-question-from-library.component';
import { EventResultsComponent } from './event-results/event-results.component';
import {ScrollToModule} from 'ng2-scroll-to-el';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        WoModule,
        EventsRoutingModule,
        ShareModule,
        ScrollToModule
    ],
    declarations: [
        EventsListComponent,
        EventTabsComponent,
        EventProfileComponent,
        EventTeamsComponent,
        EventScoreboardComponent,
        QuizQuestionsComponent,
        EventResultsComponent,
        QuizQuestionFromLibraryComponent
    ]
})
export class EventsModule {}
