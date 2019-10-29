import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AuthAdminGuard } from '@app/auth/auth-admin-guard.service';
import { ScoreboardTemplateListComponent } from './scoreboards-template-list/scoreboards-template-list.component';
import { ScoreboardTemplateProfileComponent } from './scoreboard-template-profile/scoreboard-template-profile.component';

const routes: Routes = [
    { path: '', canActivate: [AuthAdminGuard], component: ScoreboardTemplateListComponent },
    { path: ':id', canActivate: [AuthAdminGuard], component: ScoreboardTemplateProfileComponent },
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class ScoreboardsRoutingModule {}

