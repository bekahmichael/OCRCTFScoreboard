import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AuthAdminGuard } from '@app/auth/auth-admin-guard.service';
import { EventsListComponent } from './events-list/events-list.component';
import { EventTabsComponent } from './event-tabs/event-tabs.component';
import { EventProfileComponent } from './event-profile/event-profile.component';
import { EventTeamsComponent } from './event-teams/event-teams.component';
import { EventScoreboardComponent } from './event-scoreboard/event-scoreboard.component';
import { QuizQuestionsComponent } from '@app/admin/events/quiz-questions/quiz-questions.component';
import { EventResultsComponent } from './event-results/event-results.component';

const routes: Routes = [
    { path: '', canActivate: [AuthAdminGuard], component: EventsListComponent },
    { path: ':id', canActivate: [AuthAdminGuard], component: EventTabsComponent, children: [
        { path: '', canActivate: [AuthAdminGuard], component: EventProfileComponent },
        { path: 'teams', canActivate: [AuthAdminGuard], component: EventTeamsComponent },
        { path: 'scoreboard', canActivate: [AuthAdminGuard], component: EventScoreboardComponent },
        { path: 'questions', canActivate: [AuthAdminGuard], component: QuizQuestionsComponent },
        { path: 'results', canActivate: [AuthAdminGuard], component: EventResultsComponent },
    ] },
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class EventsRoutingModule {}
