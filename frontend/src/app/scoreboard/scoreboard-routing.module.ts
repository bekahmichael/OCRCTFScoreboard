import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { ScoreboardComponent } from './scoreboard.component';
import { DisplayComponent } from './display/display.component';
import { ScoreboardNotFoundComponent } from './scoreboard-not-found/scoreboard-not-found.component';

const routes: Routes = [
    { path: 'scoreboard-not-found', component: ScoreboardNotFoundComponent },
    {
        path: '', component: ScoreboardComponent ,
        children: [
            { path: ':access-key', component: DisplayComponent },
            { path: '', redirectTo: '/', pathMatch: 'full' },
        ],
    },
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class ScoreboardRoutingModule {}
