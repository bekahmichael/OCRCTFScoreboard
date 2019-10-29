import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { AdminComponent } from './admin.component';

const routes: Routes = [
    { path: '', component: AdminComponent, children: [
        { path: 'users', loadChildren: './users/users.module#UsersModule'},
        { path: 'teams', loadChildren: './teams/teams.module#TeamsModule'},
        { path: 'questions', loadChildren: './questions/questions.module#QuestionsModule'},
        { path: 'events', loadChildren: './events/events.module#EventsModule'},
        { path: 'scoreboards', loadChildren: './scoreboards/scoreboards.module#ScoreboardsModule'},
        { path: '', redirectTo: 'users', pathMatch: 'full' },
    ]},
    { path: 'login', component: LoginComponent },
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class AdminRoutingModule {}
